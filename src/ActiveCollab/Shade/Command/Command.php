<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputOption;

abstract class Command extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this->addOption(
            'debug',
            '',
            InputOption::VALUE_NONE,
            'Run command in debug mode'
        );
    }
}
