<?php

  namespace ActiveCollab\Shade\Command;

  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface;
  use ActiveCollab\Shade\Project, ActiveCollab\Shade\Element\Book;

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
          foreach ($books as $book) {
            $output->writeln($book->getTitle() . ' [' . $book->getShortName() . ']');
          }

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
        $output->writeln('This is not a valid Shade project');
      }
    }
  }