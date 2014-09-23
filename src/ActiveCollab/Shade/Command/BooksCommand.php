<?php

  namespace ActiveCollab\Shade\Command;

  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface, Symfony\Component\Console\Helper\Table;
  use ActiveCollab\Shade\Project;

  /**
   * List books
   *
   * @package ActiveCollab\Shade\Command
   */
  class BooksCommand extends Command
  {
    /**
     * Configure the command
     */
    protected function configure()
    {
      $this->setName('books')->setDescription('List books from a project');
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
        $books = $project->getBooks();

        if ($books->count()) {
          $table = new Table($output);
          $table->setHeaders([ 'Name', 'Title', 'Pages' ]);

          foreach ($books as $book) {
            $table->addRow([ $book->getShortName(), $book->getTitle(), $book->getPages()->count() ]);
          }

          $table->render();

          $output->writeln('');

          if ($books->count() === 1) {
            $output->writeln('1 book found');
          } else {
            $output->writeln($books->count() . ' books found');
          }
        } else {
          $output->writeln('0 books found');
        }
      } else {
        $output->writeln('<error>This is not a valid Shade project</error>');
      }
    }
  }