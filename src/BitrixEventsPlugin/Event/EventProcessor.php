<?php

namespace Adv\BitrixEventsPlugin\Event;

use Adv\BitrixEventsPlugin\BitrixCoreFinder;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\SystemException;
use Composer\IO\IOInterface;

/**
 * Class EventProcessor
 *
 * @package Adv\BitrixEventsPlugin\Event
 */
final class EventProcessor implements ProcessEventInterface
{
    const EXTRAS_KEY = 'events';

    /**
     * @var EventTypeRegistry
     */
    private $registry;
    /**
     * @var IOInterface $io
     */
    private $io;
    /**
     * @var BitrixCoreFinder
     */
    private $bitrixFinder;

    /**
     * EventProcessor constructor.
     *
     * @param EventTypeRegistry $registry
     */
    public function __construct(EventTypeRegistry $registry)
    {
        $this->registry = $registry;
        $this->bitrixFinder = new BitrixCoreFinder();
        /**
         * @todo remove dirty hack
         */
        (new EventModel());
    }

    /**
     * @param IOInterface $io
     */
    public function setIo(IOInterface $io)
    {
        $this->io = $io;
        $this->bitrixFinder->setIo($io);
    }

    /**
     * Process all events from registry
     */
    public function process()
    {
        try {
            $this->getBitrixFinder()->setApplication();

            foreach ($this->registry->generator() as $eventModel) {
                $this->dispatchEventProcessing($eventModel);
            }
        } catch (\Throwable $e) {
            $this->io->writeError(
                \sprintf(
                    '<error>Events installation error: %s. Please, install events manually.</error>',
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @return BitrixCoreFinder
     */
    public function getBitrixFinder(): BitrixCoreFinder
    {
        return $this->bitrixFinder;
    }

    /**
     * @param BitrixCoreFinder $bitrixFinder
     *
     * @return EventProcessor
     */
    public function setBitrixFinder(BitrixCoreFinder $bitrixFinder): EventProcessor
    {
        $this->bitrixFinder = $bitrixFinder;

        return $this;
    }

    /**
     * Dispatch event model processing
     *
     * @param EventModel $eventModel
     *
     * @throws NotFoundEventTypeException
     * @throws SystemException
     */
    private function dispatchEventProcessing(EventModel $eventModel)
    {
        switch ($eventModel->getType()) {
            case EventTypeRegistry::EVENT_TYPE_DELETE:
                $this->uninstallEvents($eventModel->getPackage());
                break;
            /**
             * @todo update with diff
             */
            case EventTypeRegistry::EVENT_TYPE_UPDATE:
                $this->uninstallEvents($eventModel->getPackage());
                $this->installEvent($eventModel);
                break;
            case EventTypeRegistry::EVENT_TYPE_INSTALL:
                $this->installEvent($eventModel);
                break;
            default:
                throw new NotFoundEventTypeException(
                    \sprintf(
                        'Not found event type %s to package %s',
                        $eventModel->getType(),
                        $eventModel->getPackage()
                    )
                );
                break;
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

        $this->io->write(
            \sprintf(
                '<info>Events to package <comment>%s</comment> successfully removed.</info>',
                $packageName
            )
        );
    }

    /**
     * @param EventModel $event
     */
    private function installEvent(EventModel $event)
    {
        try {
            $this->registerEvent($event);

            $this->io->write(
                \sprintf(
                    '<info>Event <comment>%s</comment> to package <comment>%s</comment> successfully installed.</info>',
                    $event->getName(),
                    $event->getPackage()
                )
            );
        } catch (\Throwable $e) {
            $this->io->writeError(
                \sprintf(
                    '<info>Event <comment>%s</comment> to package <comment>%s</comment> is not installed. Error: <comment>%s</comment>.</info>',
                    $event->getName(),
                    $event->getPackage(),
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @param EventModel $model
     */
    private function registerEvent(EventModel $model)
    {
        $function = $model->getVersion() === $model::VERSION_COMPATIBLE ? 'registerEventHandlerCompatible' : 'registerEventHandler';

        EventManager::getInstance()->{$function}(
            $model->getModule(),
            $model->getEvent(),
            'main',
            $model->getClass(),
            $model->getMethod(),
            $model->getSort() ?? 100,
            $model->getPackage()
        );
    }
}
