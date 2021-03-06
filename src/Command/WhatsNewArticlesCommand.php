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

class WhatsNewArticlesCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('whats_new')
            ->addOption('locale', null, InputOption::VALUE_REQUIRED)
            ->setDescription("List what's new articles from a project");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->getProject();

        if ($project->isValid()) {
            $articles = $project->getWhatsNewArticles($input->getOption('locale'));

            if ($articles->count()) {
                $table = new Table($output);
                $table->setHeaders(['Name', 'Version', 'Title']);

                foreach ($articles as $article) {
                    $table->addRow([$article->getShortName(), $article->getVersionNumber(), $article->getTitle()]);
                }

                $table->render();

                $output->writeln('');

                if ($articles->count() === 1) {
                    $output->writeln('1 article found');
                } else {
                    $output->writeln($articles->count() . ' articles found');
                }
            } else {
                $output->writeln('0 articles found');
            }
        } else {
            $output->writeln('<error>This is not a valid Shade project</error>');
        }
    }
}
