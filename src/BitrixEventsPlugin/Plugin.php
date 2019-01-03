<?php

namespace Adv\BitrixEventsPlugin;

use Adv\BitrixEventsPlugin\Event\EventFactory;
use Adv\BitrixEventsPlugin\Event\EventProcessor;
use Adv\BitrixEventsPlugin\Event\EventTypeRegistry;
use Composer\Composer;
use Composer\EventDispatcher\Event;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use Exception;

/**
 * Class Plugin
 *
 * @author  Dmitry Panychev <thor_work@yahoo.com>
 *
 * @package Adv\BitrixEventsPlugin
 */
class Plugin implements PluginInterface
{
    const PACKAGE_NAME = 'adv/bitrix-event-plugin';

    /**
     * @var EventProcessor
     */
    private $eventProcessor;
    /**
     * @var EventFactory
     */
    private $eventFactory;
    /**
     * @var EventTypeRegistry
     */
    private $eventTypeRegistry;

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
        $this->eventFactory = new EventFactory();
        $this->eventTypeRegistry = new EventTypeRegistry();
        $this->eventProcessor = new EventProcessor($this->eventTypeRegistry);
    }

    /**
     * @param Composer $composer
     * @param IOInterface $io
     *
     * @throws Exception
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $eventDispatcher = $composer->getEventDispatcher();
        $this->eventProcessor->setIo($io);

        /**
         * Package watchers. Extract events from packages.
         */
        $eventDispatcher->addListener(PackageEvents::POST_PACKAGE_INSTALL, $this->handle(EventTypeRegistry::EVENT_TYPE_INSTALL));
        $eventDispatcher->addListener(PackageEvents::POST_PACKAGE_UPDATE, $this->handle(EventTypeRegistry::EVENT_TYPE_UPDATE));
        $eventDispatcher->addListener(PackageEvents::POST_PACKAGE_UNINSTALL, $this->handle(EventTypeRegistry::EVENT_TYPE_DELETE));
        /**
         * Events manitpulations - after autoloading.
         * Update is not forced autoloading - it's run after a update
         */
        $eventDispatcher->addListener(ScriptEvents::POST_AUTOLOAD_DUMP, $this->processEvents());
        $eventDispatcher->addListener(ScriptEvents::POST_UPDATE_CMD, $this->processEvents());
    }

    /**
     * @param string $type
     *
     * @return callable
     */
    protected function handle(string $type): callable
    {
        return function (PackageEvent $event) use ($type) {
            $this->eventFactory->registerEvent($event, $this->eventTypeRegistry->get($type));
        };
    }

    /**
     * @return callable
     */
    protected function processEvents(): callable
    {
        return function (Event $event) {
            $extras = $event->getComposer()->getPackage()->getExtra();

            if ($extras['bitrix-dir']) {
                $this->eventProcessor->getBitrixFinder()->unshiftDefaultPath($extras['bitrix-dir']);
            }

            $this->eventProcessor->process();
        };
    }
}
