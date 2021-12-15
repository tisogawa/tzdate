<?php
declare(strict_types=1);

namespace TzDate\DateTime;

use DateTimeZone as BaseDateTimeZone;
use Exception;
use RuntimeException;

class DateTimeZone extends BaseDateTimeZone
{
    private static array $cityNamesAndIdentifiersMap = [];
    private static array $cityNameAliases = [];
    private string|null $cityName = null;

    public static function setCityNamesAndIdentifiersMap(array $map): void
    {
        self::$cityNamesAndIdentifiersMap = $map;
    }

    public static function setCityNameAliases(array $data): void
    {
        self::$cityNameAliases = $data;
    }

    public static function getCityNameFromTimezone(BaseDateTimeZone $timezone): string
    {
        $parts = explode('/', $timezone->getName());
        return str_replace('_', ' ', array_pop($parts));
    }

    /**
     * @throws \TzDate\DateTime\InvalidTimezoneValueException
     */
    public function __construct(string $timezone)
    {
        $identifier = $this->searchIdentifier($timezone);
        try {
            parent::__construct($identifier['identifier']);
        } catch (Exception) {
            throw new InvalidTimezoneValueException(sprintf(
                '"%s" does not appear to be a valid timezone value.', $identifier['identifier']
            ));
        }
        if (isset($identifier['city_name'])) {
            $this->cityName = $identifier['city_name'];
        }
    }

    public function getCityName(): string
    {
        return $this->cityName ?? self::getCityNameFromTimezone($this);
    }

    private function searchIdentifier(string $timezone): array
    {
        static $cityNames, $identifiers;
        if (!isset($cityNames) && !isset($identifiers)) {
            $cityNames = [];
            $identifiers = [];
            foreach (self::listIdentifiers() as $identifier) {
                $parts = explode('/', $identifier);
                $index = strtolower(str_replace('_', '', array_pop($parts)));
                $cityNames[$index] = [
                    'identifier' => $identifier,
                ];
                $index = strtolower(str_replace('_', '', $identifier));
                $identifiers[$index] = [
                    'identifier' => $identifier,
                ];
            }
            foreach (self::$cityNamesAndIdentifiersMap as $cityName => $identifier) {
                $index = strtolower(str_replace(['_', ' '], ['', ''], $cityName));
                $cityNames[$index] = [
                    'city_name'  => $cityName,
                    'identifier' => $identifier,
                ];
            }
            foreach (self::$cityNameAliases as $alias => $cityName) {
                $search = strtolower(str_replace(['_', ' '], ['', ''], $cityName));
                if (!isset($cityNames[$search])) {
                    throw new RuntimeException(sprintf(
                        'City name "%s" for alias "%s" not found', $cityName, $alias
                    ));
                }
                $index = strtolower(str_replace(['_', ' '], ['', ''], $alias));
                $cityNames[$index] = $cityNames[$search];
            }
        }
        $search = strtolower(str_replace(['_', ' '], ['', ''], $timezone));
        return $identifiers[$search] ?? $cityNames[$search] ?? [
                'identifier' => $timezone,
            ];
    }
}
