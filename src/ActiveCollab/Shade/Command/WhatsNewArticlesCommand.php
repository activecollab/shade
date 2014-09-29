<?php

  namespace ActiveCollab\Shade\Command;

  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface, Symfony\Component\Console\Helper\Table, Symfony\Component\Console\Input\InputOption;
  use ActiveCollab\Shade\Project;

  /**
   * List what's new articles from this project
   *
   * @package ActiveCollab\Shade\Command
   */
  class WhatsNewArticlesCommand extends Command
  {
    /**
     * Configure the command
     */
    protected function configure()
    {
      $this
        ->setName('whats_new')
        ->addOption('locale', null, InputOption::VALUE_REQUIRED)
        ->setDescription("List what's new articles from a project");
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
        $articles = $project->getWhatsNewArticles($input->getOption('locale'));

        if ($articles->count()) {
          $table = new Table($output);
          $table->setHeaders([ 'Name', 'Version', 'Title' ]);

          foreach ($articles as $article) {
            $table->addRow([ $article->getShortName(), $article->getVersionNumber(), $article->getTitle() ]);
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