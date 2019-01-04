<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Command;

use ActiveCollab\Shade\Project\Project;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List what's new articles from this project.
 *
 * @package ActiveCollab\Shade\Shade\Command
 */
class CommonQuestionsCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('faq')
            ->addOption('locale', null, InputOption::VALUE_REQUIRED)
            ->setDescription('Show the list of common questions and pages that have the answers');
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
            $common_questions = $project->getCommonQuestions($input->getOption('locale'));

            if (count($common_questions)) {
                $table = new Table($output);
                $table->setHeaders(['Question', 'Book', 'Page', 'Position']);

                foreach ($common_questions as $common_question) {
                    $table->addRow([$common_question['question'], $common_question['book'], $common_question['page'], $common_question['position']]);
                }

                $table->render();

                $output->writeln('');

                if (count($common_questions) === 1) {
                    $output->writeln('1 question found');
                } else {
                    $output->writeln(count($common_questions) . ' questions found');
                }
            } else {
                $output->writeln('0 questions found');
            }
        } else {
            $output->writeln('<error>This is not a valid Shade project</error>');
        }
    }
}
