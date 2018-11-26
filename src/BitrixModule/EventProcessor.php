<?php

namespace Adv\BitrixEventsPlugin;

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\SystemException;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;

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
     * @throws BitrixEventPluginException
     *
     * @return void
     */
    public function processEvent(PackageEvent $event)
    {
        $event->getComposer()->getPackage();
        $this->setApplication($event);
        $package = $event->getComposer()->getPackage();

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
            $package,
            $eventModel->getClass(),
            $eventModel->getMethod(),
            $eventModel->getSort() ?? 100
        );
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
                'DELETE FROM b_module_to_module WHERE FROM_MODULE_ID=\'%s\'',
                $packageName
            )
        );
        $application->getManagedCache()->clean('b_module_to_module');
    }
}
