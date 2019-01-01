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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VideosCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('videos')
            ->addOption('locale', null, InputOption::VALUE_REQUIRED)
            ->setDescription('List videos from a project');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->getProject();

        if ($project->isValid()) {
            $videos = $project->getVideos($input->getOption('locale'));

            if ($videos->count()) {
                $table = new Table($output);
                $table->setHeaders(['Name', 'Group', 'Title', 'Play Time', 'Wistia Code']);

                foreach ($videos as $video) {
                    $table->addRow([$video->getShortName(), $video->getGroupName(), $video->getTitle(), $video->getPlayTime(), $video->getProperty('wistia_code')]);
                }

                $table->render();

                $output->writeln('');

                if ($videos->count() === 1) {
                    $output->writeln('1 video found');
                } else {
                    $output->writeln($videos->count() . ' videos found');
                }
            } else {
                $output->writeln('0 videos found');
            }
        } else {
            $output->writeln('<error>This is not a valid Shade project</error>');
        }
    }
}
