<?php

namespace Adv\BitrixEventsPlugin\Event;

use Adv\BitrixEventsPlugin\BitrixEventPluginException;
use UnexpectedValueException;

class NotFoundEventTypeException extends UnexpectedValueException implements BitrixEventPluginException
{

}
