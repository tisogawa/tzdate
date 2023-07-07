<?php
declare(strict_types=1);

namespace TzDate\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

class Application extends BaseApplication
{
    public function getDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();
        return $inputDefinition;
    }

    protected function getCommandName(InputInterface $input): string
    {
        return 'tzdate';
    }

    protected function getDefaultCommands(): array
    {
        return array_merge(
            parent::getDefaultCommands(),
            [new Command()]
        );
    }
}
