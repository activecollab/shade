<?php

  namespace ActiveCollab\Shade\Command;

  use ActiveCollab\Shade;
  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface, Symfony\Component\Console\Helper\Table;
  use ActiveCollab\Shade\Project;

  /**
   * List to-do notes
   *
   * @package ActiveCollab\Shade\Command
   */
  class TodoCommand extends Command
  {
    /**
     * Configure the command
     */
    protected function configure()
    {
      $this->setName('todo')->setDescription('Find and show to-do notes from project elements');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      ini_set('date.timezone', 'UTC');

      $project = new Project(getcwd());

      if($project->isValid()) {
        Shade::initSmarty($project, Shade::getBuildTheme($project->getDefaultBuildTheme()));

        $this->renderEverything($project);

        $todo_notes = Shade::getTodo();

        if (count($todo_notes)) {
          $table = new Table($output);
          $table->setHeaders([ 'Message', 'File' ]);

          $path_len = strlen(rtrim($project->getPath(), '/'));

          foreach ($todo_notes as $todo_note) {
            $table->addRow([ $todo_note['message'], substr($todo_note['file'], $path_len + 1) ]);
          }

          $table->render();

          $output->writeln('');

          if (count($todo_notes) === 1) {
            $output->writeln('1 todo note found');
          } else {
            $output->writeln(count($todo_notes) . ' todo notes found');
          }
        } else {
          $output->writeln('0 todo notes found');
        }
      } else {
        $output->writeln('<error>This is not a valid Shade project</error>');
      }
    }

    /**
     * @param Project $project
     */
    private function renderEverything(Project &$project)
    {
      foreach ($project->getBooks() as $book) {
        $book->renderBody();

        foreach ($book->getPages() as $page) {
          $page->renderBody();
        }
      }

      foreach ($project->getWhatsNewArticles() as $article) {
        $article->renderBody();
      }

      foreach ($project->getReleases() as $release) {
        $release->renderBody();
      }

      foreach ($project->getVideos() as $video) {
        $video->renderBody();
      }
    }
  }