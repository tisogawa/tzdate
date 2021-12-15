<?php

namespace TzDate\Test\DateTime;

use Exception;
use PHPUnit\Framework\TestCase;
use TzDate\DateTime\DateTimeZone;

class DateTimeZoneTest extends TestCase
{
    public function certainCityNames(): array
    {
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

    /**
     * @dataProvider certainCityNames
     * @param string $input
     * @param string $identifier
     * @param string $cityName
     */
    public function testCertainCityNames(string $input, string $identifier, string $cityName): void
    {
        $this->doTestConstructor($input, $identifier, $cityName);
    }

    /**
     * @return array
     */
    public function defaultIdentifiers(): array
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

    /**
     * @dataProvider defaultIdentifiers
     * @param string $input
     * @param string $identifier
     * @param string $cityName
     */
    public function testDefaultIdentifiers(string $input, string $identifier, string $cityName): void
    {
        $this->doTestConstructor($input, $identifier, $cityName);
    }

    public function defaultCityNames(): array
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

    /**
     * @dataProvider defaultCityNames
     * @param string $input
     * @param string $identifier
     * @param string $cityName
     */
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
