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
  class BookCommand extends Command
  {
    /**
     * Configure the command
     */
    protected function configure()
    {
      $this->setName('book')->addArgument('name', InputArgument::REQUIRED, 'Short name of the book')->setDescription('Show book details');
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
        $book = $project->getBook($input->getArgument('name'));

        if ($book instanceof Book) {
          $table = new Table($output);
          $table->setHeaders([ 'Property', 'Value' ]);

          $table->addRow([ 'Short Name', $book->getShortName() ]);
          $table->addRow([ 'Title', $book->getTitle() ]);

          $pages = $book->getPages();

          if ($pages->count() > 0) {
            $page_titles = [];

            foreach ($pages as $page) {
              $page_titles[] = $page->getTitle();
            }

            $table->addRow([ 'Pages', implode("\n", $page_titles) ]);
          } else {
            $table->addRow([ 'Pages', '--' ]);
          }

          $table->render();
        } else {
          $output->writeln('<error>Book "' . $input->getArgument('name') . ' not found"</error>');
        }
      } else {
        $output->writeln('<error>This is not a valid Shade project</error>');
      }
    }
  }