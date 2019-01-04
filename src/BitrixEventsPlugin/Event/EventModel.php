<?php

namespace Adv\BitrixEventsPlugin\Event;

/**
 * Class EventModel
 *
 * @package Adv\BitrixEventsPlugin\Event
 */
class EventModel
{
    const VERSION_COMPATIBLE = 1;

    const EVENT_KEY = 'event';
    const MODULE_KEY = 'module';
    const CLASS_KEY = 'class';
    const METHOD_KEY = 'method';
    const SORT_KEY = 'sort';
    const VERSION_KEY = 'version';
    const PACKAGE_KEY = 'package';
    const NAME_KEY = 'name';

    protected $event = '';
    protected $module = '';
    protected $class = '';
    protected $method = '';
    protected $sort = 0;
    protected $version = 1;
    protected $type = '';
    protected $package = '';
    protected $name = '';

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     *
     * @return EventModel
     */
    public function setEvent(string $event): EventModel
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @param string $module
     *
     * @return EventModel
     */
    public function setModule(string $module): EventModel
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return EventModel
     */
    public function setClass(string $class): EventModel
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return EventModel
     */
    public function setMethod(string $method): EventModel
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     *
     * @return EventModel
     */
    public function setSort(int $sort): EventModel
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return EventModel
     */
    public function setVersion(int $version): EventModel
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return EventModel
     */
    public function setType(string $type): EventModel
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getPackage(): string
    {
        return $this->package;
    }

    /**
     * @param string $package
     *
     * @return EventModel
     */
    public function setPackage(string $package): EventModel
    {
        $this->package = $package;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return EventModel
     */
    public function setName(string $name): EventModel
    {
        $this->name = $name;

        return $this;
    }
}
