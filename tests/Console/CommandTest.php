<?php

namespace TzDate\Test\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use TzDate\Console\Command;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultArgumentsAndOptions()
    {
        $tester = $this->initCommandTester();
        $tester->execute(array(
            'command' => 'tzdate',
        ));

        $this->assertSame('now', $tester->getInput()->getArgument('datetime'));
        $this->assertSame(array(), $tester->getInput()->getOption('timezone'));
        $this->assertSame('Y-m-d H:i:s', $tester->getInput()->getOption('format'));

        try {
            $tester->getInput()->getArgument('foo');
            $this->fail('"foo" is not an available command argument');
        } catch (\InvalidArgumentException $e) {
        }
        try {
            $tester->getInput()->getOption('foo');
            $this->fail('"foo" is not an available command option');
        } catch (\InvalidArgumentException $e) {
        }

    }

    public function testDefaultExecution()
    {
        $tester = $this->initCommandTester();
        $tester->execute(array(
            'command' => 'tzdate',
        ));

        $lines = explode(PHP_EOL, trim($tester->getDisplay()));
        $found = 0;
        foreach ($lines as $line) {
            if (strpos($line, 'San Francisco') === 0 ||
                strpos($line, date_default_timezone_get()) === 0
            ) {
                $found++;
            }
        }
        $this->assertSame(2, $found);
    }

    public function initCommandTester()
    {
        $application = new Application();
        $application->add(new Command());
        $command = $application->find('tzdate');
        return new CommandTester($command);
    }
}
