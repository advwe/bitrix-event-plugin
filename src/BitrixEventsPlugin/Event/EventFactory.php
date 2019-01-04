<?php

namespace Adv\BitrixEventsPlugin\Event;

use Adv\BitrixEventsPlugin\Plugin;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\Installer\PackageEvent;
use Composer\Package\Package;

/**
 * Class EventFactory
 *
 * @package Adv\BitrixEventsPlugin\Event
 */
final class EventFactory
{
    const EXTRAS_KEY = 'events';

    /**
     * @param PackageEvent $event
     * @param EventRegistry $registry
     */
    public function registerEvent(PackageEvent $event, EventRegistry $registry)
    {
        $package = $this->getPackageFromOperation($event->getOperation());

        if (null === $package || !$this->checkPackageToHasEvent($package)) {
            return;
        }

        if (\in_array(
            $registry->getType(),
            [
                EventTypeRegistry::EVENT_TYPE_UPDATE,
                EventTypeRegistry::EVENT_TYPE_DELETE
            ],
            true
        )) {
            $registry->register(
                (new EventModel())->setType(EventTypeRegistry::EVENT_TYPE_DELETE)->setPackage($package->getName())
            );
        }

        foreach ($this->buildEventsFromPackage($package->getName(), $package->getExtra()) as $event) {
            $registry->register($event);
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
     * @param string $package
     * @param array $extra
     *
     * @return \Generator|EventModel[]
     */
    protected function buildEventsFromPackage(string $package, array $extra): \Generator
    {
        foreach ($extra[Plugin::PACKAGE_NAME][self::EXTRAS_KEY] as $name => $event) {
            yield $this->buildEvent(
                \array_merge(
                    $event,
                    [
                        'name' => $name,
                        EventModel::PACKAGE_KEY => $package
                    ]
                )
            );
        }
    }

    /**
     * @param Package $package
     *
     * @return bool
     */
    public function checkPackageToHasEvent(Package $package): bool
    {
        return (bool)($package->getExtra()[Plugin::PACKAGE_NAME] ?? false && $package->getExtra()[Plugin::PACKAGE_NAME][self::EXTRAS_KEY] ?? false);
    }

    /**
     * @param array $fields
     *
     * @return EventModel
     */
    public function buildEvent(array $fields): EventModel
    {
        if (
            !$fields[EventModel::EVENT_KEY]
            || !$fields[EventModel::MODULE_KEY]
            || !$fields[EventModel::CLASS_KEY]
            || !$fields[EventModel::METHOD_KEY]
        ) {
            throw new EventCreateException(
                \sprintf(
                    'Event %s from %s is wrong',
                    $fields[EventModel::NAME_KEY],
                    $fields[EventModel::PACKAGE_KEY]
                )
            );
        }

        return (new EventModel())
            ->setEvent((string)$fields[EventModel::EVENT_KEY])
            ->setModule((string)$fields[EventModel::MODULE_KEY])
            ->setClass((string)$fields[EventModel::CLASS_KEY])
            ->setMethod((string)$fields[EventModel::METHOD_KEY])
            ->setSort((int)$fields[EventModel::SORT_KEY])
            ->setVersion((int)$fields[EventModel::VERSION_KEY])
            ->setPackage((string)$fields[EventModel::PACKAGE_KEY])
            ->setName((string)$fields[EventModel::NAME_KEY]);
    }
}
