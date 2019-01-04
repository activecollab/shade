<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReleasesCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('releases')
            ->addOption('locale', null, InputOption::VALUE_REQUIRED)
            ->setDescription('List releases from a project');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->getProject();

        if ($project->isValid()) {
            $releases = $project->getReleases($input->getOption('locale'));

            if (count($releases)) {
                $table = new Table($output);
                $table->setHeaders(['Version']);

                foreach ($releases as $release) {
                    $table->addRow([$release->getVersionNumber()]);
                }

                $table->render();

                $output->writeln('');

                if (count($releases) === 1) {
                    $output->writeln('1 release found');
                } else {
                    $output->writeln(count($releases) . ' releases found');
                }
            } else {
                $output->writeln('0 releases found');
            }
        } else {
            $output->writeln('<error>This is not a valid Shade project</error>');
        }
    }
}
