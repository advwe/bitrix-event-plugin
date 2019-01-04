<?php

namespace Adv\BitrixEventsPlugin\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Generator;

/**
 * Class EventRegistry
 *
 * @package Adv\BitrixEventsPlugin
 */
class EventRegistry
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var ArrayCollection
     */
    private $collection;

    /**
     * EventRegistry constructor.
     *
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->collection = new ArrayCollection();
        $this->type = $type;
    }

    /**
     * @param EventModel $event
     *
     * @return $this
     */
    public function register(EventModel $event)
    {
        $this->collection->set($this->getEventKey($event), $event->setType($this->type));

        return $this;
    }

    /**
     * @return ArrayCollection|EventModel[]
     */
    public function getCollection(): ArrayCollection
    {
        return $this->collection;
    }

    /**
     * @return Generator|EventModel[]
     */
    public function generator(): Generator
    {
        foreach ($this->collection as $event) {
            yield $event;
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param EventModel $event
     *
     * @return string
     */
    private function getEventKey(EventModel $event)
    {
        return $event->getPackage() . $event->getName();
    }
}
