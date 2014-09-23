<?php

  namespace ActiveCollab\Shade\Command;

  use ActiveCollab\Shade\Project;
  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface;

  /**
   * Create a new project
   *
   * @package ActiveCollab\Shade\Command
   */
  class ProjectCommand extends Command
  {
    /**
     * Configure the command
     */
    function configure()
    {
      $this->setName('project')->addArgument('name')->setDescription('Create a new project');
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
        $output->writeln('Project already initialized');
      } else {
        $configuration = [
          'name' => $input->getArgument('name'),
        ];

        if (empty($configuration['name'])) {
          $configuration['name'] = basename($project->getPath());
        }

        if (file_put_contents($project->getPath() . '/project.json', json_encode($configuration, JSON_PRETTY_PRINT))) {
          $output->writeln('Project initialized');
        } else {
          $output->writeln('<error>Failed to create a project configuration file</error>');
        }
      }
    }
  }