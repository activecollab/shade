<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Command;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Auto-update command.
 *
 * @package ActiveCollab\Shade\Command
 */
class UpdateCommand extends Command
{
    const MANIFEST_FILE = 'https://www.activecollab.com/labs/shade/manifest.json';

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('update')
            ->setDescription('Updates shade.phar to the latest version');
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        $manager->update($this->getApplication()->getVersion(), true);
    }
}
