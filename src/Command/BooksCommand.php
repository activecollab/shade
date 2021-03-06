<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List books.
 *
 * @package ActiveCollab\Shade\Shade\Command
 */
class BooksCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('books')
            ->addOption('locale', null, InputOption::VALUE_REQUIRED)
            ->setDescription('List books from a project');
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->getProject();

        if ($project->isValid()) {
            $books = $project->getBooks($input->getOption('locale'));

            if (count($books)) {
                $table = new Table($output);
                $table->setHeaders(['Name', 'Title', 'Pages', 'Position']);

                foreach ($books as $book) {
                    $table->addRow([$book->getShortName(), $book->getTitle(), $book->getPages()->count(), $book->getPosition()]);
                }

                $table->render();

                $output->writeln('');

                if (count($books) === 1) {
                    $output->writeln('1 book found');
                } else {
                    $output->writeln(count($books) . ' books found');
                }
            } else {
                $output->writeln('0 books found');
            }
        } else {
            $output->writeln('<error>This is not a valid Shade project</error>');
        }
    }
}
