<?php

  namespace ActiveCollab\Shade\Command;

  use ActiveCollab\Shade;
  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface, Symfony\Component\Console\Helper\Table;
  use ActiveCollab\Shade\Project;

  /**
   * Show book details
   *
   * @package ActiveCollab\Shade\Command
   */
  class PluginsCommand extends Command
  {
    /**
     * Configure the command
     */
    protected function configure()
    {
      $this->setName('plugins')->setDescription('Show project plugin settings');
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
        $table = new Table($output);
        $table->setHeaders([ 'Plugin', 'Enabled?' ]);

        foreach (Shade::getPlugins($project) as $plugin) {
          $is_enabled = $plugin->isEnabled();

          if ($is_enabled === true) {
            $is_enabled = 'Yes';
          } elseif($is_enabled === false) {
            $is_enabled = '';
          }

          $table->addRow([ $plugin->getName(), $is_enabled ]);
        }

        $table->render();
      } else {
        $output->writeln('<error>This is not a valid Shade project</error>');
      }
    }
  }