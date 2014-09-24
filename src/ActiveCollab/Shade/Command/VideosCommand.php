<?php

  namespace ActiveCollab\Shade\Command;

  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface, Symfony\Component\Console\Helper\Table;
  use ActiveCollab\Shade\Project;

  /**
   * List project videos
   *
   * @package ActiveCollab\Shade\Command
   */
  class VideosCommand extends Command
  {
    /**
     * Configure the command
     */
    protected function configure()
    {
      $this->setName('videos')->setDescription('List videos from a project');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $project = new Project(getcwd());

      if($project->isValid()) {
        $videos = $project->getVideos();

        if ($videos->count()) {
          $table = new Table($output);
          $table->setHeaders([ 'Name', 'Group', 'Title', 'Play Time', 'Wistia Code' ]);

          foreach ($videos as $video) {
            $table->addRow([ $video->getShortName(), $video->getGroupName(), $video->getTitle(), $video->getPlayTime(), $video->getProperty('wistia_code') ]);
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