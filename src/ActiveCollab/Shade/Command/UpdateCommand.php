<?php

namespace ActiveCollab\Shade\Command;

use Herrera\Phar\Update\Manager, Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface;

/**
 * Auto-update command
 *
 * @package ActiveCollab\Shade\Command
 */
class UpdateCommand extends Command
{
    const MANIFEST_FILE = 'https://www.activecollab.com/labs/shade/manifest.json';

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('update')->setDescription('Updates shade.phar to the latest version');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        $manager->update($this->getApplication()->getVersion(), true);
    }
}
