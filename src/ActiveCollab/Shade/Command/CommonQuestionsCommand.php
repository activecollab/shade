<?php

  namespace ActiveCollab\Shade\Command;

  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface, Symfony\Component\Console\Helper\Table;
  use ActiveCollab\Shade\Project;

  /**
   * List what's new articles from this project
   *
   * @package ActiveCollab\Shade\Command
   */
  class CommonQuestionsCommand extends Command
  {
    /**
     * Configure the command
     */
    protected function configure()
    {
      $this->setName('faq')->setDescription("Show the list of common questions and pages that have the answers");
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
        $common_questions = $project->getCommonQuestions();

        if (count($common_questions)) {
          $table = new Table($output);
          $table->setHeaders([ 'Question', 'Book', 'Page', 'Position' ]);

          foreach ($common_questions as $common_question) {
            $table->addRow([ $common_question['question'], $common_question['book'],$common_question['page'], $common_question['position'] ]);
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