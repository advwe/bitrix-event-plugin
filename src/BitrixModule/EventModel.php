<?php

namespace Adv\BitrixEventsPlugin;

/**
 * Class EventModel
 *
 * @package Adv\BitrixEventsPlugin
 */
class EventModel
{
    const VERSION_COMPATIBLE = 1;

    const EVENT_KEY   = 'event';
    const MODULE_KEY  = 'module';
    const CLASS_KEY   = 'class';
    const METHOD_KEY  = 'method';
    const SORT_KEY    = 'sort';
    const VERSION_KEY = 'version';

    protected $event   = '';
    protected $module  = '';
    protected $class   = '';
    protected $method  = '';
    protected $sort    = 0;
    protected $version = 1;

    /**
     * @param array $eventArray
     *
     * @return EventModel
     *
     * @throws BitrixEventPluginException
     */
    public static function factory(array $eventArray): EventModel
    {
        if (
            !$eventArray[self::EVENT_KEY]
            || !$eventArray[self::MODULE_KEY]
            || !$eventArray[self::CLASS_KEY]
            || !$eventArray[self::METHOD_KEY]
        ) {
            throw new BitrixEventPluginException('Event is wrong');
        }

        return (new static())->setEvent((string)$eventArray[self::EVENT_KEY])
                             ->setModule((string)$eventArray[self::MODULE_KEY])
                             ->setClass((string)$eventArray[self::CLASS_KEY])
                             ->setMethod((string)$eventArray[self::METHOD_KEY])
                             ->setSort((int)$eventArray[self::SORT_KEY])
                             ->setVersion((int)$eventArray[self::VERSION_KEY]);
    }

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
}
