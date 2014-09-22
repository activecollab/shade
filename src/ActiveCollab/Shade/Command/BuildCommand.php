<?php

  namespace ActiveCollab\Shade\Command;

  use ActiveCollab\Shade, ActiveCollab\Shade\Project, ActiveCollab\Shade\Theme;
  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface;
  use Symfony\Component\Console\Input\InputOption, Smarty, Exception;

  /**
   * Build help
   *
   * @package ActiveCollab\Shade\Command
   */
  class BuildCommand extends Command
  {
    /**
     * Configure the command
     */
    protected function configure()
    {
      $this
        ->setName('build')
        ->addOption('target', null, InputOption::VALUE_OPTIONAL, 'Where do you want Shade to build the help?')
        ->addOption('theme', null, InputOption::VALUE_OPTIONAL, 'Name of the theme that should be used to build help')
        ->addOption('skip-books', null, InputOption::VALUE_OPTIONAL, 'Comma separated list of books that should be skipped')
        ->setDescription('Build a help');
    }

    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      ini_set('date.timezone', 'UTC');

      $project = new Project(getcwd());

      if ($project->isValid()) {
        $target_path = $this->getBuildTarget($input, $project);
        $theme = $this->getTheme($input, $project);

        if (!$this->isValidTargetPath($target_path)) {
          $output->writeln("Build target '$target_path' not found or not writable");
          return;
        }

        if (!($theme instanceof Theme)) {
          $output->writeln("Theme not found");
          return;
        }

        $this->smarty = Shade::getSmarty($project, $theme);

        foreach ([ 'prepareTargetPath', 'buildLandingPage', 'buildWhatsNew', 'buildReleaseNotes', 'buildBooks', 'buildVideos' ] as $build_step) {
          try {
            if (!$this->$build_step($input, $output, $project, $target_path, $theme)) {
              $output->writeln("Build process failed at step '$build_step'. Aborting...");
              return;
            }
          } catch (Exception $e) {
            $output->writeln('Exception: ' . $e->getMessage());
            $output->writeln($e->getTraceAsString());
          }

        }

//          $this->createDir($destination_path, $output);
//          $this->copyStructure(HelpFramework::PATH . '/static/assets', "$destination_path/assets", $output, true);
//
//          Shade::setUrlGenerator(function(HelpElement $element) {
//            if($element instanceof HelpBook) {
//              return 'https://activecollab.com/help/books/'.$element->getShortName().'/index.html';
//            } elseif($element instanceof HelpBookPage) {
//              return 'https://activecollab.com/help/books/'.$element->getBookName().'/'.$element->getSlug().'.html';
//            } elseif($element instanceof HelpWhatsNewArticle) {
//              return 'https://activecollab.com/help/whats-new/'.$element->getSlug().'.html';
//            } elseif($element instanceof HelpVideo) {
//              return 'https://activecollab.com/help/videos/index.html#'.$element->getSlug();
//            } else {
//              return '#'.$element->getShortName();
//            } // if
//          });
//
//          AngieApplication::help()->setImageUrlGenerator(function($current_element, $name) {
//            if($current_element instanceof HelpBookPage) {
//              $params['src'] = 'https://activecollab.com/help/assets/images/books/'.$current_element->getBookName().'/'.$name;
//            } elseif($current_element instanceof HelpBook) {
//              $params['src'] = 'https://activecollab.com/help/assets/images/books/'.$current_element->getShortName().'/'.$name;
//            } elseif($current_element instanceof HelpVideo) {
//              $params['src'] = 'https://activecollab.com/help/assets/images/videos/'.$name;
//            } elseif($current_element instanceof HelpWhatsNewArticle) {
//              $params['src'] = 'https://activecollab.com/help/assets/images/whats-new/'.$current_element->getVersionNumber().'/'.$name;
//            } else {
//              $params['src'] = 'Unknown';
//            } // if
//
//            return '<div class="center">' . HTML::openTag('img', $params) . '</div>';
//          });

//          AngieApplication::help()->setOnUserGroupsCallback(function($user, &$groups) {
//            $groups[] = 'Website Visitor';
//          });

//        $this->build_landing_page($destination_path, $output);
//        $this->build_whats_new($destination_path, $output);
//        $this->build_release_notes($destination_path, $output);
//        $this->build_books($destination_path, $ignored_books, $output);
//        $this->build_videos($destination_path, $output);
      } else {
        $output->writeln('This is not a valid Shade project');
      }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Project $project
     * @param $target_path
     * @param Theme $theme
     * @return bool
     */
    public function prepareTargetPath(InputInterface $input, OutputInterface $output, Project $project, $target_path, Theme $theme)
    {
      Shade::clearDir($target_path, function($path) use (&$output) {
        $output->writeln("$path deleted");
      });

      Shade::copyDir($theme->getPath() . '/assets', "$target_path/assets", function($path) use (&$output) {
        $output->writeln("$path copied");
      });

      return true;
    }

    /**
     * Build index.html page
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Project $project
     * @param $target_path
     * @param Theme $theme
     */
    public function buildLandingPage(InputInterface $input, OutputInterface $output, Project $project, $target_path, Theme $theme)
    {
      $this->smarty->assign('common_questions', Shade::getCommonQuestions());

      Shade::writeFile("$target_path/index.html", $this->smarty->fetch('index.tpl'), function($path) use (&$output) {
        $output->writeln("File '$path' created");
      });

      return true;

//      return $this->smarty->fetch('index.tpl');
//
//      $landing_page_template = file_get_contents(HelpFramework::PATH . '/static/templates/index.html');
//
//      $common_questions_list = '';
//
//      $common_questions = AngieApplication::help()->getCommonQuestions();
//
//      if(is_foreachable($common_questions)) {
//        foreach($common_questions as $common_question) {
//          $common_questions_list .= '<li><a href="'.$common_question['page_url'].'">'.$common_question['question'].'</a></li>';
//        } // foreach
//      } // if
//
//      $landing_page = str_replace('--COMMON-QUESTIONS-LIST--', $common_questions_list, $landing_page_template);
//      $this->createFile("$destination_path/index.html", $landing_page, $output, true);
    }

    public function buildWhatsNew()
    {

    }

    public function buildReleaseNotes()
    {

    }

    public function buildBooks()
    {

    }

    public function buildVideos()
    {

    }

    /**
     * Return build target path
     *
     * @param InputInterface $input
     * @param Project $project
     * @return string
     */
    private function getBuildTarget(InputInterface $input, Project &$project)
    {
      $target = $input->getOption('target');

      if (empty($target)) {
        $target = $project->getDefaultBuildTarget();
      }

      return (string) $target;
    }

    /**
     * Return true if target path is valid
     *
     * @param string $target_path
     * @return bool
     */
    private function isValidTargetPath($target_path)
    {
      return $target_path && is_dir($target_path);
    }

    /**
     * @param InputInterface $input
     * @param Project $project
     * @return Theme
     * @throws Shade\Error\ThemeNotFoundError
     */
    private function getTheme(InputInterface $input, Project &$project)
    {
      $theme_name = $target = $input->getOption('theme');

      if (empty($theme_name)) {
        $theme_name = $project->getDefaultBuildTheme();
      }

      return Shade::getBuildTheme($theme_name);
    }

  }