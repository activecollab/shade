<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Command;

use ActiveCollab\Shade\Project;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LocalesCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('locales')
            ->setDescription('List project locales');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->getProject();

        if ($project->isValid()) {
            $default_locale = $project->getDefaultLocale();

            $table = new Table($output);
            $table->setHeaders(['Code', 'Name', 'Is Default?']);

            foreach ($project->getLocales() as $code => $name) {
                $table->addRow([$code, $name, ($code === $default_locale ? 'Yes' : 'No')]);
            }

            $table->render();
        } else {
            $output->writeln('<error>This is not a valid Shade project</error>');
        }
    }
}
