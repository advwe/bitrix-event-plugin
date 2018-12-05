<?php

namespace Adv\BitrixEventsPlugin;

use Composer\Composer;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
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
     * Plugin constructor.
     */
    public function __construct()
    {
        $this->eventProcessor = new EventProcessor();
    }

    /**
     * @param Composer    $composer
     * @param IOInterface $io
     *
     * @throws Exception
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $eventDispatcher = $composer->getEventDispatcher();
        $eventDispatcher->addListener(PackageEvents::POST_PACKAGE_INSTALL, $this->handle());
        $eventDispatcher->addListener(PackageEvents::POST_PACKAGE_UPDATE, $this->handle());
        $eventDispatcher->addListener(PackageEvents::POST_PACKAGE_UNINSTALL, $this->handle());
    }

    /**
     * @return callable
     *
     * @throws Exception
     */
    protected function handle(): callable
    {
        return function (PackageEvent $event) {
            $this->eventProcessor->processEvent($event);
        };
    }
}
