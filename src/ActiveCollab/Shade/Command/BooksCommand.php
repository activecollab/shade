<?php

  namespace ActiveCollab\Shade\Command;

  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface;

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
      $output->writeln('Hello World');
    }
  }