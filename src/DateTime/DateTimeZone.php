<?php

namespace TzDate\DateTime;

use DateTimeZone as BaseDateTimeZone;

class DateTimeZone extends BaseDateTimeZone
{
    /** @var array */
    private static $cityNamesAndIdentifiersMap = array();
    /** @var array */
    private static $cityNameAliases = array();

    /** @var string */
    private $cityName;

    /**
     * @param array $map
     */
    public static function setCityNamesAndIdentifiersMap(array $map)
    {
        self::$cityNamesAndIdentifiersMap = $map;
    }

    /**
     * @param array $data
     */
    public static function setCityNameAliases(array $data)
    {
        self::$cityNameAliases = $data;
    }

    /**
     * @param BaseDateTimeZone $timezone
     * @return string
     */
    public static function getCityNameFromTimezone(BaseDateTimeZone $timezone)
    {
        $parts = explode('/', $timezone->getName());
        return str_replace('_', ' ', array_pop($parts));
    }

    /**
     * Constructor
     *
     * @param string $timezone
     * @throws InvalidTimezoneValueException
     */
    public function __construct($timezone)
    {
        $identifier = $this->searchIdentifier($timezone);
        try {
            parent::__construct($identifier['identifier']);
        } catch (\Exception $e) {
            throw new InvalidTimezoneValueException(sprintf(
                '"%s" does not appear to be a valid timezone value.', $identifier['identifier']
            ));
        }
        if (isset($identifier['city_name'])) {
            $this->cityName = $identifier['city_name'];
        }
    }

    /**
     * @return string
     */
    public function getCityName()
    {
        if (isset($this->cityName)) {
            return $this->cityName;
        }
        return self::getCityNameFromTimezone($this);
    }

    /**
     * @param string $timezone
     * @return array
     */
    private function searchIdentifier($timezone)
    {
        static $cityNames, $identifiers;
        if (!isset($cityNames) && !isset($identifiers)) {
            $cityNames = array();
            $identifiers = array();
            foreach (DateTimeZone::listIdentifiers() as $identifier) {
                $parts = explode('/', $identifier);
                $index = strtolower(str_replace('_', '', array_pop($parts)));
                $cityNames[$index] = array(
                    'identifier' => $identifier,
                );
                $index = strtolower(str_replace('_', '', $identifier));
                $identifiers[$index] = array(
                    'identifier' => $identifier,
                );
            }
            foreach (self::$cityNamesAndIdentifiersMap as $cityName => $identifier) {
                $index = strtolower(str_replace(array('_', ' '), array('', ''), $cityName));
                $cityNames[$index] = array(
                    'city_name' => $cityName,
                    'identifier' => $identifier,
                );
            }
            foreach (self::$cityNameAliases as $alias => $cityName) {
                $search = strtolower(str_replace(array('_', ' '), array('', ''), $cityName));
                if (!isset($cityNames[$search])) {
                    throw new \RuntimeException(sprintf(
                        'City name "%s" for alias "%s" not found', $cityName, $alias
                    ));
                }
                $index = strtolower(str_replace(array('_', ' '), array('', ''), $alias));
                $cityNames[$index] = $cityNames[$search];
            }
        }
        $search = strtolower(str_replace(array('_', ' '), array('', ''), $timezone));
        if (isset($identifiers[$search])) {
            return $identifiers[$search];
        }
        if (isset($cityNames[$search])) {
            return $cityNames[$search];
        }
        return array(
            'identifier' => $timezone,
        );
    }
}
