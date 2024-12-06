<?php
declare(strict_types=1);

namespace TzDate\DateTime;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DateTimeZoneTest extends TestCase
{
    public static function certainCityNames(): array
    {
        /** @noinspection SpellCheckingInspection */
        return [
            ['Calcutta', 'Asia/Kolkata', 'Calcutta'],
            ['Kolkata', 'Asia/Kolkata', 'Kolkata'],
            ['Buenos Aires', 'America/Argentina/Buenos_Aires', 'Buenos Aires'],
            ['buenosaires', 'America/Argentina/Buenos_Aires', 'Buenos Aires'],
            ['San Francisco', 'America/Los_Angeles', 'San Francisco'],
            ['sanfrancisco', 'America/Los_Angeles', 'San Francisco'],
            ['sf', 'America/Los_Angeles', 'San Francisco'],
            ['losangeles', 'America/Los_Angeles', 'Los Angeles'],
            ['la', 'America/Los_Angeles', 'Los Angeles'],
        ];
    }

    #[DataProvider('certainCityNames')]
    public function testCertainCityNames(string $input, string $identifier, string $cityName): void
    {
        $this->doTestConstructor($input, $identifier, $cityName);
    }

    /**
     * @return array
     */
    public static function defaultIdentifiers(): array
    {
        /** @noinspection DuplicatedCode */
        $data = [];
        foreach (DateTimeZone::listIdentifiers() as $identifier) {
            $parts = explode('/', $identifier);
            $cityName = str_replace('_', ' ', array_pop($parts));
            $data[] = [$identifier, $identifier, $cityName];
            $data[] = [strtolower(str_replace(' ', '', $identifier)), $identifier, $cityName];
        }
        return $data;
    }

    #[DataProvider('defaultIdentifiers')]
    public function testDefaultIdentifiers(string $input, string $identifier, string $cityName): void
    {
        $this->doTestConstructor($input, $identifier, $cityName);
    }

    public static function defaultCityNames(): array
    {
        /** @noinspection DuplicatedCode */
        $data = [];
        foreach (DateTimeZone::listIdentifiers() as $identifier) {
            $parts = explode('/', $identifier);
            $cityName = str_replace('_', ' ', array_pop($parts));
            $data[] = [$cityName, $identifier, $cityName];
            $data[] = [strtolower(str_replace(' ', '', $cityName)), $identifier, $cityName];
        }
        return $data;
    }

    #[DataProvider('defaultCityNames')]
    public function testDefaultCityNames(string $input, string $identifier, string $cityName): void
    {
        $this->doTestConstructor($input, $identifier, $cityName);
    }

    private function doTestConstructor(string $input, string $identifier, string $cityName): void
    {
        try {
            $dtz = new DateTimeZone($input);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->assertSame($identifier, $dtz->getName());
        $this->assertSame($cityName, $dtz->getCityName());
    }
}
