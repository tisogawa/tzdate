<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use TzDate\Config\Config;
use TzDate\DateTime\DateTimeZone;

/** @noinspection PhpUnhandledExceptionInspection */
Config::setAdditionalConfig(json_decode(file_get_contents(__DIR__ . '/res/config.json'), true, 512, JSON_THROW_ON_ERROR));
$config = new Config();
DateTimeZone::setCityNamesAndIdentifiersMap($config->get('cities'));
DateTimeZone::setCityNameAliases($config->get('aliases'));
