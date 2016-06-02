<?php

namespace Kkkw;

use Illuminate\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

/**
 *
 */
class AnsibleDynamicInventoryApplication extends Application
{

    public function getCommandName(InputInterface $input)
    {
        return 'ansible-dynamic-inventory';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new AnsibleDynamicInventoryCommand();
        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();
        return $inputDefinition;
    }
}
