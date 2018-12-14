<?php

namespace Adv\BitrixEventsPlugin;

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\SystemException;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\PackageInterface;

/**
 * Class EventProcessor
 *
 * @package Adv\BitrixEventsPlugin
 */
final class EventProcessor implements ProcessEventInterface
{
    use BitrixCoreFinderTrait;

    const EXTRAS_KEY = 'events';

    /**
     * @param PackageEvent $event
     *
     * @throws SystemException
     *
     * @return void
     */
    public function processEvent(PackageEvent $event)
    {
        $package = $this->getPackageFromOperation($event->getOperation());

        if (null === $package) {
            return;
        }

        /**
         * @var PackageInterface $package
         */
        try {
            $this->setApplication($event);
        } catch (BitrixEventPluginException $e) {
            $event->getIO()->write(
                \sprintf(
                    'Bitrix is not found. Events from %s was not installed. Please, register it manually.',
                    $package->getName()
                ),
                $event->getIO()::VERY_VERBOSE
            );

            return;
        }

        switch ($event->getName()) {
            case PackageEvents::POST_PACKAGE_INSTALL:
            case PackageEvents::POST_PACKAGE_UPDATE:
                $this->installEvents(
                    $package->getName(),
                    $package->getExtra(),
                    $event->getIO()
                );
                break;
            case PackageEvents::POST_PACKAGE_UNINSTALL:
                $this->uninstallEvents($package->getName());
                break;
            default:
                /**
                 * do nothing
                 */
        }
    }

    /**
     * @param OperationInterface $operation
     *
     * @return Package|null
     */
    protected function getPackageFromOperation(OperationInterface $operation)
    {
        $package = null;

        if (\method_exists($operation, 'getPackage')) {
            $package = $operation->getPackage();
        } elseif (\method_exists($operation, 'getInitialPackage')) {
            $package = $operation->getInitialPackage();
        }

        return $package;
    }

    /**
     * @todo check the difference
     *
     * @param string      $packageName
     * @param array       $extras
     * @param IOInterface $io
     *
     * @throws SystemException
     */
    private function installEvents(string $packageName, array $extras, IOInterface $io)
    {
        $this->uninstallEvents($packageName);

        $io->write($extras);
        if (!\is_array($extras[Plugin::PACKAGE_NAME][self::EXTRAS_KEY])) {
            return;
        }

        /** @noinspection ForeachSourceInspection */
        foreach ($extras[Plugin::PACKAGE_NAME][self::EXTRAS_KEY] as $name => $event) {
            try {
                $eventModel = EventModel::factory($event);
            } catch (BitrixEventPluginException $e) {
                $io->writeError(
                    \sprintf(
                        'Event %s must consider are event, module, class, method.',
                        $name
                    )
                );

                continue;
            }

            $this->registerEvent(
                $eventModel->getVersion() === $eventModel::VERSION_COMPATIBLE,
                $eventModel,
                $packageName
            );

            $io->write(
                \sprintf(
                    'Event %s successfully installed.',
                    $name
                )
            );
        }
    }

    /**
     * @param string $packageName
     *
     * @throws SystemException
     */
    private function uninstallEvents(string $packageName)
    {
        $application = Application::getInstance();

        $application::getConnection()->query(
            \sprintf(
                'DELETE FROM b_module_to_module WHERE TO_PATH=\'%s\'',
                $packageName
            )
        );
        $application->getManagedCache()->clean('b_module_to_module');
    }

    /**
     * @param bool       $isCompatible
     * @param EventModel $eventModel
     * @param string     $package
     */
    private function registerEvent(bool $isCompatible, EventModel $eventModel, string $package)
    {
        $function = $isCompatible ? 'registerEventHandlerCompatible' : 'registerEventHandler';

        EventManager::getInstance()->{$function}(
            $eventModel->getModule(),
            $eventModel->getEvent(),
            'main',
            $eventModel->getClass(),
            $eventModel->getMethod(),
            $eventModel->getSort() ?? 100,
            $package
        );
    }
}
