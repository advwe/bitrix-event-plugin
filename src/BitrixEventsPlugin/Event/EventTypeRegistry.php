<?php

namespace Adv\BitrixEventsPlugin\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Generator;

/**
 * Class EventTypeRegistry
 *
 * @package Adv\ErpFileExchangeBundle\Pipeline
 */
class EventTypeRegistry
{
    const EVENT_TYPE_INSTALL = 'install';
    const EVENT_TYPE_UPDATE = 'update';
    const EVENT_TYPE_DELETE = 'delete';

    /**
     * @var Collection
     */
    private $collection;

    /**
     * PipelineRegistry constructor.
     */
    public function __construct()
    {
        $this->collection = new ArrayCollection(
            [
                self::EVENT_TYPE_INSTALL => new EventRegistry(self::EVENT_TYPE_INSTALL),
                self::EVENT_TYPE_UPDATE => new EventRegistry(self::EVENT_TYPE_UPDATE),
                self::EVENT_TYPE_DELETE => new EventRegistry(self::EVENT_TYPE_DELETE),
            ]
        );
    }

    /**
     * @param string $type
     * @param EventModel $event
     *
     * @return $this
     */
    public function register(string $type, EventModel $event)
    {
        $this->collection->get($type)->add($event->setType($type));

        return $this;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function has(string $type): bool
    {
        return $this->collection->offsetExists($type);
    }

    /**
     * @return Collection|EventRegistry[]
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @throws NotFoundEventTypeException
     *
     * @return Generator|EventModel[]
     */
    public function generator(): Generator
    {
        foreach ($this->getCollection() as $registry) {
            yield from $registry->generator();
        }
    }

    /**
     * @param string $type
     *
     * @throws NotFoundEventTypeException
     *
     * @return EventTypeRegistry
     */
    public function get(string $type): EventRegistry
    {
        $result = $this->collection->get($type);

        if (!$result) {
            throw new NotFoundEventTypeException(
                \sprintf(
                    'Cant find event type: %s',
                    $type
                )
            );
        }

        return $result;
    }
}
