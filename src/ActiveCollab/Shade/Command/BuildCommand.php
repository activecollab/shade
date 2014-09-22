<?php

  namespace ActiveCollab\Shade\Command;

  use ActiveCollab\Shade, ActiveCollab\Shade\Project, ActiveCollab\Shade\Theme;
  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface;
  use Symfony\Component\Console\Input\InputOption;

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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $project = new Project(getcwd());

      if ($project->isValid()) {
        $target_path = $this->getBuildTarget($input, $project);
        $theme = $this->getTheme($input, $project);

        if (!$this->isValidTargetPath($target_path)) {
          $output->writeln("Build target '$target_path' not found or not writable");
        }

        if (!($theme instanceof Theme)) {
          $output->writeln("Theme not found");
        }

        foreach ([ 'prepareTargetPath', 'buildLandingPage', 'buildWhatsNew', 'buildReleaseNotes', 'buildBooks', 'buildVideos' ] as $build_step) {
          if (!$theme->$build_step($input, $output, $project, $target_path, $theme)) {
            $output->writeln("Build process failed at step '$build_step'. Aborting…");
            return;
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

    private function prepareTargetPath(InputInterface $input, OutputInterface $output, Project $project, $target_path, Theme $theme)
    {

    }

    private function buildLandingPage()
    {

    }

    private function buildWhatsNew()
    {

    }

    private function buildReleaseNotes()
    {

    }

    private function buildBooks()
    {

    }

    private function buildVideos()
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

    private function getTheme(InputInterface $input, Project &$project)
    {
      $theme_name = $target = $input->getOption('theme');

      if (empty($theme_name)) {
        $theme_name = $project->getDefaultBuildTheme();
      }

      try {
        return Shade::getBuildTheme($theme_name);
      } catch(Shade\Error\ThemeNotFoundError $e) {
        return false;
      }
    }
  }