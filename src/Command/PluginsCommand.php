<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Command;

use ActiveCollab\Shade\Shade;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginsCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('plugins')
            ->setDescription('Show project plugin settings');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->getProject();

        if ($project->isValid()) {
            $table = new Table($output);
            $table->setHeaders(['Plugin', 'Enabled?']);

            foreach (Shade::getPlugins($project) as $plugin) {
                $is_enabled = $plugin->isEnabled();

                if ($is_enabled === true) {
                    $is_enabled = 'Yes';
                } elseif ($is_enabled === false) {
                    $is_enabled = '';
                }

                $table->addRow([$plugin->getName(), $is_enabled]);
            }

            $table->render();
        } else {
            $output->writeln('<error>This is not a valid Shade project</error>');
        }
    }
}
