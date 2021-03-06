<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Command;

use ActiveCollab\Shade\Project\Project;
use ActiveCollab\Shade\Shade;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TodoCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('todo')
            ->addOption('locale', null, InputOption::VALUE_REQUIRED)
            ->setDescription('Find and show to-do notes from project elements');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->getProject();

        if ($project->isValid()) {
            Shade::initSmarty($project, Shade::getBuildTheme($project->getDefaultBuildTheme()));

            $this->renderEverything($project, $input->getOption('locale'));

            $todo_notes = Shade::getTodo();

            if (count($todo_notes)) {
                $table = new Table($output);
                $table->setHeaders(
                    [
                        'Message',
                        'File',
                    ]
                );

                $path_len = strlen(rtrim($project->getPath(), '/'));

                foreach ($todo_notes as $todo_note) {
                    $table->addRow([$todo_note['message'], substr($todo_note['file'], $path_len + 1)]);
                }

                $table->render();

                $output->writeln('');

                if (count($todo_notes) === 1) {
                    $output->writeln('1 todo note found');
                } else {
                    $output->writeln(count($todo_notes) . ' todo notes found');
                }
            } else {
                $output->writeln('0 todo notes found');
            }
        } else {
            $output->writeln('<error>This is not a valid Shade project</error>');
        }
    }

    /**
     * @param \ActiveCollab\Shade\Project\Project $project
     * @param string|null                         $locale
     */
    private function renderEverything(Project &$project, $locale)
    {
        foreach ($project->getBooks($locale) as $book) {
            $book->renderBody();

            foreach ($book->getPages() as $page) {
                $page->renderBody();
            }
        }

        foreach ($project->getWhatsNewArticles($locale) as $article) {
            $article->renderBody();
        }

        foreach ($project->getReleases($locale) as $release) {
            $release->renderBody();
        }

        foreach ($project->getVideos($locale) as $video) {
            $video->renderBody();
        }
    }
}
