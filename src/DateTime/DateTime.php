<?php

namespace TzDate\DateTime;

use DateTime as BaseDateTime;
use DateTimeZone as BaseDateTimeZone;
use Exception;

class DateTime extends BaseDateTime
{
    private string|null $timezoneCityName = null;

    /**
     * Constructor
     *
     * @param string|int $time
     * @param string|BaseDateTimeZone|null $timezone
     * @throws InvalidTimeValueException
     * @throws InvalidTimezoneValueException
     */
    public function __construct(string|int $time = 'now', string|BaseDateTimeZone|null $timezone = null)
    {
        if ($timezone === null) {
            $timezone = new DateTimeZone(date_default_timezone_get());
        } elseif (!$timezone instanceof BaseDateTimeZone) {
            $timezone = new DateTimeZone($timezone);
            $this->timezoneCityName = $timezone->getCityName();
        }
        if (is_numeric($time)) {
            $time = '@' . round($time);
        }
        try {
            parent::__construct($time, $timezone);
        } catch (InvalidTimezoneValueException $e) {
            throw $e;
        } catch (Exception) {
            throw new InvalidTimeValueException(sprintf(
                '"%s" could not be parsed as a date/time value.', $time
            ));
        }
        if ($this->getOffset() !== $timezone->getOffset($this)
            || $this->getTimezone()->getName() !== $timezone->getName()
        ) {
            $this->setTimezone($timezone);
        }
    }

    /**
     * @param string|BaseDateTimeZone $timezone
     * @return $this
     * @throws \TzDate\DateTime\InvalidTimezoneValueException
     */
    public function setTimezone($timezone): DateTime
    {
        if (!$timezone instanceof BaseDateTimeZone) {
            $timezone = new DateTimeZone($timezone);
            $this->timezoneCityName = $timezone->getCityName();
        }
        return parent::setTimezone($timezone);
    }

    public function getTimezoneCityName(): string
    {
        return $this->timezoneCityName ?? DateTimeZone::getCityNameFromTimezone($this->getTimezone());
    }

    public function formatTimezoneOffset(): string
    {
        $offset = $this->getOffset();
        return ($offset < 0 ? '-' : '+') . gmdate('H:i', abs($offset));
    }
}
