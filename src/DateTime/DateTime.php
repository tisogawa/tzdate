<?php

namespace TzDate\DateTime;

use DateTime as BaseDateTime;
use DateTimeZone as BaseDateTimeZone;

class DateTime extends BaseDateTime
{
    /** @var string */
    private $timezoneCityName;

    /**
     * Constructor
     *
     * @param int|string $time
     * @param string|BaseDateTimeZone $timezone
     * @throws InvalidTimeValueException
     * @throws InvalidTimezoneValueException
     */
    public function __construct($time = 'now', $timezone = null)
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
        } catch (\Exception $e) {
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
     */
    public function setTimezone($timezone)
    {
        if (!$timezone instanceof BaseDateTimeZone) {
            $timezone = new DateTimeZone($timezone);
            $this->timezoneCityName = $timezone->getCityName();
        }
        return parent::setTimezone($timezone);
    }

    /**
     * @return string
     */
    public function getTimezoneCityName()
    {
        if (isset($this->timezoneCityName)) {
            return $this->timezoneCityName;
        }
        return DateTimeZone::getCityNameFromTimezone($this->getTimezone());
    }

    /**
     * @return string
     */
    public function formatTimezoneOffset()
    {
        $offset = $this->getOffset();
        return ($offset < 0 ? '-' : '+') . gmdate('H:i', abs($offset));
    }
}
