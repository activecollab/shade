<?php

  namespace ActiveCollab\Shade\Command;

  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface, Symfony\Component\Console\Helper\Table;
  use ActiveCollab\Shade\Project;

  /**
   * List project releases
   *
   * @package ActiveCollab\Shade\Command
   */
  class ReleasesCommand extends Command
  {
    /**
     * Configure the command
     */
    protected function configure()
    {
      $this->setName('releases')->setDescription('List releases from a project');
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
        $releases = $project->getReleases();

        if (count($releases)) {
          $table = new Table($output);
          $table->setHeaders([ 'Version' ]);

          foreach ($releases as $release) {
            $table->addRow([ $release->getVersionNumber() ]);
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