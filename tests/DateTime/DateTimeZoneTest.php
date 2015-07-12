<?php

namespace TzDate\Test\DateTime;

use TzDate\DateTime\DateTimeZone;

class DateTimeZoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function certainCityNames()
    {
        return array(
            array('Calcutta', 'Asia/Kolkata', 'Calcutta'),
            array('Kolkata', 'Asia/Kolkata', 'Kolkata'),
            array('Buenos Aires', 'America/Argentina/Buenos_Aires', 'Buenos Aires'),
            array('buenosaires', 'America/Argentina/Buenos_Aires', 'Buenos Aires'),
            array('San Francisco', 'America/Los_Angeles', 'San Francisco'),
            array('sanfrancisco', 'America/Los_Angeles', 'San Francisco'),
            array('sf', 'America/Los_Angeles', 'San Francisco'),
            array('losangeles', 'America/Los_Angeles', 'Los Angeles'),
            array('la', 'America/Los_Angeles', 'Los Angeles'),
        );
    }

    /**
     * @dataProvider certainCityNames
     * @param string $input
     * @param string $identifier
     * @param string $cityName
     */
    public function testCertainCityNames($input, $identifier, $cityName)
    {
        $this->doTestConstructor($input, $identifier, $cityName);
    }

    /**
     * @return array
     */
    public function defaultIdentifiers()
    {
        $data = array();
        foreach (DateTimeZone::listIdentifiers() as $identifier) {
            $parts = explode('/', $identifier);
            $cityName = str_replace('_', ' ', array_pop($parts));
            $data[] = array($identifier, $identifier, $cityName);
            $data[] = array(strtolower(str_replace(' ', '', $identifier)), $identifier, $cityName);
        }
        return $data;
    }

    /**
     * @dataProvider defaultIdentifiers
     * @param string $input
     * @param string $identifier
     * @param string $cityName
     */
    public function testDefaultIdentifiers($input, $identifier, $cityName)
    {
        $this->doTestConstructor($input, $identifier, $cityName);
    }

    /**
     * @return array
     */
    public function defaultCityNames()
    {
        $data = array();
        foreach (DateTimeZone::listIdentifiers() as $identifier) {
            $parts = explode('/', $identifier);
            $cityName = str_replace('_', ' ', array_pop($parts));
            $data[] = array($cityName, $identifier, $cityName);
            $data[] = array(strtolower(str_replace(' ', '', $cityName)), $identifier, $cityName);
        }
        return $data;
    }

    /**
     * @dataProvider defaultCityNames
     * @param string $input
     * @param string $identifier
     * @param string $cityName
     */
    public function testDefaultCityNames($input, $identifier, $cityName)
    {
        $this->doTestConstructor($input, $identifier, $cityName);
    }

    private function doTestConstructor($input, $identifier, $cityName)
    {
        try {
            $dtz = new DateTimeZone($input);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }
        $this->assertSame($identifier, $dtz->getName());
        $this->assertSame($cityName, $dtz->getCityName());
    }
}
