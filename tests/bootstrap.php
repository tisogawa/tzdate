<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TzDate\Config\Config;
use TzDate\DateTime\DateTimeZone;

Config::setAdditionalConfig(json_decode(file_get_contents(__DIR__ . '/res/config.json'), true));
$config = new Config();
DateTimeZone::setCityNamesAndIdentifiersMap($config->get('cities'));
DateTimeZone::setCityNameAliases($config->get('aliases'));
