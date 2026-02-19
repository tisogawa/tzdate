<?php
declare(strict_types=1);

namespace TzDate\DateTime;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    private static string $timezoneToRestore;

    public static function setUpBeforeClass(): void
    {
        self::$timezoneToRestore = date_default_timezone_get();
    }

    protected function tearDown(): void
    {
        date_default_timezone_set(self::$timezoneToRestore);
    }

    /**
     * @throws \Exception
     */
    public static function timezones(): array
    {
        $date_times = [
            '2015-01-01 00:00:00',
            '2015-07-01 00:00:00',
        ];
        $timezones = [
            'Asia/Tokyo',
            'America/Los_Angeles',
            'America/Phoenix',
            'Europe/London',
            'UTC',
        ];

        $result = [];
        foreach ($date_times as $date_time) {
            $dt = new \DateTime($date_time, new \DateTimezone('UTC'));
            $local_values = [];
            foreach ($timezones as $timezone) {
                $local_values[$timezone] = $dt->setTimezone(new \DateTimeZone($timezone))->format('Y-m-d H:i:s');
            }
            foreach ($timezones as $timezone) {
                $result["$timezone @ $date_time"] = [
                    $timezone,
                    $dt->setTimezone(new \DateTimeZone($timezone))->format('Y-m-d H:i:s'),
                    $local_values,
                ];
            }
        }
        return $result;
    }

    /**
     * @throws \TzDate\DateTime\InvalidTimeValueException
     * @throws \TzDate\DateTime\InvalidTimezoneValueException
     * @noinspection PhpUnusedParameterInspection
     */
    #[DataProvider('timezones')]
    public function testConstructor(string $timezone, string $datetime_string, array $local_values): void
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
     * @throws \TzDate\DateTime\InvalidTimeValueException
     * @throws \TzDate\DateTime\InvalidTimezoneValueException
     */
    #[DataProvider('timezones')]
    public function testSetTimezone(string $timezone, string $datetime_string, array $local_values): void
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
