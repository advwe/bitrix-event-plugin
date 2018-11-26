<?php

namespace Adv\BitrixEventsPlugin;


use Composer\Installer\PackageEvent;

/**
 * Interface ProcessEventInterface
 *
 * @package Adv\BitrixEventsPlugin
 */
interface ProcessEventInterface
{
    /**
     * @param PackageEvent $event
     *
     * @return void
     */
    public function processEvent(PackageEvent $event);
}
