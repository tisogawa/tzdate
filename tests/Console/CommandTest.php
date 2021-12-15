<?php

namespace TzDate\Test\Console;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use TzDate\Console\Command;

class CommandTest extends TestCase
{
    public function testDefaultArgumentsAndOptions(): void
    {
        $tester = $this->initCommandTester();
        $tester->execute([
            'command' => 'tzdate',
        ]);

        $this->assertSame('now', $tester->getInput()->getArgument('datetime'));
        $this->assertSame([], $tester->getInput()->getOption('timezone'));
        $this->assertSame('Y-m-d H:i:s', $tester->getInput()->getOption('format'));

        try {
            $tester->getInput()->getArgument('foo');
            $this->fail('"foo" is not an available command argument');
        } catch (InvalidArgumentException) {
        }
        try {
            $tester->getInput()->getOption('foo');
            $this->fail('"foo" is not an available command option');
        } catch (InvalidArgumentException) {
        }

    }

    public function testDefaultExecution(): void
    {
        $tester = $this->initCommandTester();
        $tester->execute([
            'command' => 'tzdate',
        ]);

        $lines = explode(PHP_EOL, trim($tester->getDisplay()));
        $found = 0;
        foreach ($lines as $line) {
            if (str_starts_with($line, 'San Francisco') ||
                str_starts_with($line, date_default_timezone_get())
            ) {
                $found++;
            }
        }
        $this->assertSame(2, $found);
    }

    public function initCommandTester(): CommandTester
    {
        $application = new Application();
        $application->add(new Command());
        $command = $application->find('tzdate');
        return new CommandTester($command);
    }
}
