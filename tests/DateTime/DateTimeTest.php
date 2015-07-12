<?php

namespace TzDate\Test\DateTime;

use TzDate\DateTime\DateTime;
use TzDate\DateTime\DateTimeZone;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    private static $timezoneToRestore;

    public static function setUpBeforeClass()
    {
        self::$timezoneToRestore = date_default_timezone_get();
    }

    protected function tearDown()
    {
        date_default_timezone_set(self::$timezoneToRestore);
    }

    public function timezones()
    {
        $date_times = array(
            '2015-01-01 00:00:00',
            '2015-07-01 00:00:00',
        );
        $timezones = array(
            'Asia/Tokyo',
            'America/Los_Angeles',
            'America/Phoenix',
            'Europe/London',
            'UTC',
        );

        $result = array();
        foreach ($date_times as $date_time) {
            $dt = new \DateTime($date_time, new \DateTimezone('UTC'));
            $local_values = array();
            foreach ($timezones as $timezone) {
                $local_values[$timezone] = $dt->setTimezone(new \DateTimeZone($timezone))->format('Y-m-d H:i:s');
            }
            foreach ($timezones as $timezone) {
                $result["{$timezone} @ {$date_time}"] = array(
                    $timezone,
                    $dt->setTimezone(new \DateTimeZone($timezone))->format('Y-m-d H:i:s'),
                    $local_values,
                );
            }
        }
        return $result;
    }

    /**
     * @dataProvider timezones
     *
     * @param string $timezone
     * @param string $datetime_string
     */
    public function testConstructor($timezone, $datetime_string)
    {
        date_default_timezone_set($timezone);

        $dt = new DateTime($datetime_string);
        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $dt = new DateTime($datetime_string, $timezone);
        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $dt = new DateTime($datetime_string, new DateTimezone($timezone));
        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $dt = new DateTime(strtotime($datetime_string));
        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $dt = new DateTime(strtotime($datetime_string), $timezone);
        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $dt = new DateTime(strtotime($datetime_string), new DateTimezone($timezone));
        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $dt = new DateTime(date(DATE_ATOM, strtotime($datetime_string)));
        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $dt = new DateTime(date(DATE_ATOM, strtotime($datetime_string)), $timezone);
        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        $dt = new DateTime(date(DATE_ATOM, strtotime($datetime_string)), new DateTimezone($timezone));
        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));
    }

    /**
     * @dataProvider timezones
     *
     * @param string $timezone
     * @param string $datetime_string
     * @param array $local_values
     */
    public function testSetTimezone($timezone, $datetime_string, array $local_values)
    {
        date_default_timezone_set($timezone);

        $dt = new DateTime($datetime_string);
        $this->assertSame($timezone, $dt->getTimezone()->getName());
        $this->assertSame($datetime_string, $dt->format('Y-m-d H:i:s'));

        foreach ($local_values as $local_timezone => $local_datetime_string) {
            $dt->setTimezone($local_timezone);
            $this->assertSame($local_timezone, $dt->getTimezone()->getName());
            $this->assertSame($local_datetime_string, $dt->format('Y-m-d H:i:s'));

            $dt->setTimezone(new DateTimezone($local_timezone));
            $this->assertSame($local_timezone, $dt->getTimezone()->getName());
            $this->assertSame($local_datetime_string, $dt->format('Y-m-d H:i:s'));
        }
    }
}
