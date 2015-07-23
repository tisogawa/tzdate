<?php

namespace TzDate\Console;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TzDate\Config\Config;
use TzDate\DateTime\DateTime;
use TzDate\DateTime\DateTimeZone;

class Command extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tzdate')
            ->addArgument('datetime', InputArgument::OPTIONAL, 'Date/time string', 'now')
            ->addArgument('datetime_timezone', InputArgument::OPTIONAL, 'Timezone for date/time', null)
            ->addOption('timezone', 'z', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Timezone name(s)')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'String expression to format date/time', 'Y-m-d H:i:s');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $datetime = $input->getArgument('datetime');
        $datetimeTimezone = $input->getArgument('datetime_timezone');
        $timezones = $input->getOption('timezone');
        $format = $input->getOption('format');

        $config = new Config();
        DateTimeZone::setCityNamesAndIdentifiersMap($config->get('cities'));
        DateTimeZone::setCityNameAliases($config->get('aliases'));

        if (!$timezones) {
            $timezones = $config->get('default_timezones');
        }
        if ($datetimeTimezone) {
            $timezones[] = $datetimeTimezone;
        }
        $timezones[] = date_default_timezone_get();

        /** @var DateTime[] $list */
        $list = array();
        $sort = array();
        foreach ($timezones as $timezone) {
            $dt = new DateTime($datetime, $datetimeTimezone);
            $dt->setTimezone($timezone);
            $cityName = $dt->getTimezoneCityName();
            $list[$cityName] = $dt;
            $sort[$cityName] = $dt->getOffset();
        }
        array_multisort($sort, SORT_ASC, $list);

        foreach ($list as $cityName => $dt) {
            $output->writeln(sprintf(
                $config->get('list_format'),
                $cityName,
                $dt->formatTimezoneOffset(),
                $dt->format('T'),
                $dt->format($format)
            ));
        }
    }
}
