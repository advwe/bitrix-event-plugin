<?php

namespace Adv\BitrixEventsPlugin\Event;

use Adv\BitrixEventsPlugin\BitrixEventPluginException;

class EventCreateException extends \InvalidArgumentException implements BitrixEventPluginException
{

}