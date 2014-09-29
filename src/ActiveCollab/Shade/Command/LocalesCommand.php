<?php

  namespace ActiveCollab\Shade\Command;

  use ActiveCollab\Shade\Element\Book;
  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface, Symfony\Component\Console\Helper\Table;
  use ActiveCollab\Shade\Project;
  use Symfony\Component\Console\Input\InputArgument;

  /**
   * Show book details
   *
   * @package ActiveCollab\Shade\Command
   */
  class LocalesCommand extends Command
  {
    /**
     * Configure the command
     */
    protected function configure()
    {
      $this->setName('locales')->setDescription('List project locales');
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
        $default_locale = $project->getDefaultLocale();

        $table = new Table($output);
        $table->setHeaders([ 'Code', 'Name', 'Is Default?' ]);

        foreach ($project->getLocales() as $code => $name) {
          $table->addRow([ $code, $name, ($code === $default_locale ? 'Yes' : 'No') ]);
        }

        $table->render();
      } else {
        $output->writeln('<error>This is not a valid Shade project</error>');
      }
    }
  }