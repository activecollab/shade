<?php

  namespace ActiveCollab\Shade\Command;

  use ActiveCollab\Shade, ActiveCollab\Shade\Project, ActiveCollab\Shade\Theme, ActiveCollab\Shade\Element\Book, ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\WhatsNewArticle, ActiveCollab\Shade\Element\Video, ActiveCollab\Shade\Element\Release;
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

        $this->smarty =& Shade::initSmarty($project, $theme);

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
      } else {
        $output->writeln('<error>This is not a valid Shade project</error>');
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
     * @param string $target_path
     * @param Theme $theme
     * @return bool
     * @throws Exception
     * @throws \SmartyException
     */
    public function buildLandingPage(InputInterface $input, OutputInterface $output, Project $project, $target_path, Theme $theme)
    {
      $this->smarty->assign([
        'common_questions' => $project->getCommonQuestions(),
        'page_level' => 0,
      ]);

      Shade::writeFile("$target_path/index.html", $this->smarty->fetch('index.tpl'), function($path) use (&$output) {
        $output->writeln("File '$path' created");
      });

      return true;
    }

    /**
     * Build what's new section of the project
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Project $project
     * @param string $target_path
     * @param Theme $theme
     * @return bool
     * @throws Exception
     * @throws \SmartyException
     */
    public function buildWhatsNew(InputInterface $input, OutputInterface $output, Project $project, $target_path, Theme $theme)
    {
      Shade::createDir("$target_path/whats-new", function($path) use (&$output) {
        $output->writeln("Directory '$path' created");
      });

      Shade::createDir("$target_path/assets/images/whats-new", function($path) use (&$output) {
        $output->writeln("Directory '$path' created");
      });

      $whats_new_articles = $project->getWhatsNewArticles();

      foreach ($whats_new_articles as $whats_new_article) {
        $this->copyVersionImages($whats_new_article, $target_path, $output);
      }

      $whats_new_articles_by_version = $this->getWhatsNewArticlesByVersion($whats_new_articles);

      $this->smarty->assign([
        'whats_new_articles' => $whats_new_articles,
        'whats_new_articles_by_version' => $whats_new_articles_by_version,
        'current_whats_new_article' => $this->getCurrentArticleFromSortedArticles($whats_new_articles_by_version),
        'page_level' => 1,
      ]);

      Shade::writeFile("$target_path/whats-new/index.html", $this->smarty->fetch('whats_new_article.tpl'), function($path) use (&$output) {
        $output->writeln("File '$path' created");
      });

      Shade::writeFile("$target_path/whats-new/rss.xml", $this->smarty->fetch('rss.tpl'), function($path) use (&$output) {
        $output->writeln("File '$path' created");
      });

      foreach ($whats_new_articles as $whats_new_article) {
        $this->smarty->assign('current_whats_new_article', $whats_new_article);

        Shade::writeFile("$target_path/whats-new/" . $whats_new_article->getShortName() . ".html", $this->smarty->fetch('whats_new_article.tpl'), function($path) use (&$output) {
          $output->writeln("File '$path' created");
        });
      }

      return true;
    }

    /**
     * @param WhatsNewArticle $article
     * @param string $target_path
     * @param OutputInterface $output
     */
    private function copyVersionImages(WhatsNewArticle $article, $target_path, OutputInterface $output)
    {
      $version_num = $article->getVersionNumber();

      if (is_dir("$target_path/assets/images/whats-new/$version_num")) {
        return;
      }

      $version_path = dirname($article->getIndexFilePath());

      if (is_dir("$version_path/images")) {
        Shade::copyDir("$version_path/images", "$target_path/assets/images/whats-new/$version_num", null, function($path) use (&$output) {
          $output->writeln("$path copied");
        });
      }
    }

    /**
     * Return what's new articles sorted by version
     *
     * @param WhatsNewArticle[] $whats_new_articles
     * @return array
     */
    private function getWhatsNewArticlesByVersion($whats_new_articles)
    {
      $whats_new_articles_by_version = [];

      foreach ($whats_new_articles as $whats_new_article) {
        if (empty($whats_new_articles_by_version[$whats_new_article->getVersionNumber()])) {
          $whats_new_articles_by_version[$whats_new_article->getVersionNumber()] = [];
        }

        $whats_new_articles_by_version[$whats_new_article->getVersionNumber()][] = $whats_new_article;
      }

      uksort($whats_new_articles_by_version, function($a, $b) {
        return version_compare($b, $a);
      });

      return $whats_new_articles_by_version;
    }

    /**
     * Get first article from the list of sorter articles
     *
     * @param array $whats_new_articles_by_version
     * @return WhatsNewArticle
     */
    private function getCurrentArticleFromSortedArticles($whats_new_articles_by_version)
    {
      foreach ($whats_new_articles_by_version as $v => $articles) {
        foreach ($articles as $article) {
          return $article;
        }
      }

      return null;
    }

    /**
     * Build release notes
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Project $project
     * @param string $target_path
     * @param Theme $theme
     * @return bool
     * @throws Exception
     * @throws \SmartyException
     */
    public function buildReleaseNotes(InputInterface $input, OutputInterface $output, Project $project, $target_path, Theme $theme)
    {
      Shade::createDir("$target_path/release-notes", function($path) use (&$output) {
        $output->writeln("Directory '$path' created");
      });

      $releases = $project->getReleases();
      $releases_by_major_version = $this->getReleasesByMajorVersion($releases);

      $this->smarty->assign([
        'releases_by_major_version' => $releases_by_major_version,
        'current_release' => $this->getCurrentReleaseFromSortedReleases($releases_by_major_version),
        'page_level' => 1,
      ]);

      Shade::writeFile("$target_path/release-notes/index.html", $this->smarty->fetch('release.tpl'), function($path) use (&$output) {
        $output->writeln("File '$path' created");
      });

      foreach ($releases as $release) {
        $this->smarty->assign([
          'current_release' => $release
        ]);

        Shade::writeFile("$target_path/release-notes/" . $release->getSlug() . ".html", $this->smarty->fetch('release.tpl'), function($path) use (&$output) {
          $output->writeln("File '$path' created");
        });
      }

      return true;
    }

    /**
     * Return releases by major version
     *
     * @param Release[] $releases
     * @return array
     */
    private function getReleasesByMajorVersion($releases)
    {
      $result = [];

      foreach ($releases as $release) {
        $bits = explode('.', $release->getVersionNumber());

        while (count($bits) > 2) {
          array_pop($bits);
        }

        $major_version = implode('.', $bits);

        if (empty($result[$major_version])) {
          $result[$major_version] = [];
        }

        $result[$major_version][] = $release;
      }

      return $result;
    }

    /**
     * @param array $sorted_releases
     * @return Release|null
     */
    private function getCurrentReleaseFromSortedReleases($sorted_releases)
    {
      foreach ($sorted_releases as $releases) {
        foreach ($releases as $release) {
          return $release;
        }
      }

      return null;
    }

    /**
     * Build books and book pages
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Project $project
     * @param string $target_path
     * @param Theme $theme
     * @return bool
     * @throws Exception
     * @throws \SmartyException
     */
    public function buildBooks(InputInterface $input, OutputInterface $output, Project $project, $target_path, Theme $theme)
    {
      Shade::createDir("$target_path/books", function($path) use (&$output) {
        $output->writeln("Directory '$path' created");
      });

      Shade::createDir("$target_path/assets/images/books", function($path) use (&$output) {
        $output->writeln("Directory '$path' created");
      });

      $books = $project->getBooks();

      foreach ($books as $book) {
        $this->copyBookImages($book, $target_path, $output);
      }

      $this->smarty->assign([
        'books' => $books,
        'page_level' => 1,
      ]);

      Shade::writeFile("$target_path/books/index.html", $this->smarty->fetch('books.tpl'), function($path) use (&$output) {
        $output->writeln("File '$path' created");
      });

      foreach ($books as $book) {
        Shade::createDir("$target_path/books/" . $book->getShortName(), function($path) use (&$output) {
          $output->writeln("Directory '$path' created");
        });

        $pages = $book->getPages();

        $this->smarty->assign([
          'current_book' => $book,
          'page_level' => 2,
          'pages' => $pages,
          'current_page' => $this->getCurrentPage($pages),
          'sidebar_image' => '../../assets/images/books/' . $book->getShortName() . '/_cover_small.png'
        ]);

        Shade::writeFile("$target_path/books/" . $book->getShortName() . "/index.html", $this->smarty->fetch('book_page.tpl'), function($path) use (&$output) {
          $output->writeln("File '$path' created");
        });

        foreach ($pages as $page) {
          $this->smarty->assign([
            'current_page' => $page,
          ]);

          Shade::writeFile("$target_path/books/" . $book->getShortName() . "/" . $page->getShortName() . ".html", $this->smarty->fetch('book_page.tpl'), function($path) use (&$output) {
            $output->writeln("File '$path' created");
          });
        }
      }

      return true;
    }

    /**
     * @param Book $book
     * @param string $target_path
     * @param OutputInterface $output
     */
    private function copyBookImages(Book $book, $target_path, OutputInterface $output)
    {
      $book_path = $book->getPath();
      $book_name = $book->getShortName();

      if (is_dir("$book_path/images")) {
        Shade::copyDir("$book_path/images", "$target_path/assets/images/books/$book_name", null, function($path) use (&$output) {
          $output->writeln("$path copied");
        });
      }
    }

    /**
     * @param BookPage[] $pages
     * @return BookPage
     */
    private function getCurrentPage($pages)
    {
      foreach ($pages as $page) {
        return $page;
      }

      return null;
    }

    /**
     * Build videos
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Project $project
     * @param string $target_path
     * @param Theme $theme
     * @return bool
     * @throws Exception
     * @throws \SmartyException
     */
    public function buildVideos(InputInterface $input, OutputInterface $output, Project $project, $target_path, Theme $theme)
    {
      Shade::createDir("$target_path/videos", function($path) use (&$output) {
        $output->writeln("Directory '$path' created");
      });

      $this->smarty->assign([
        'video_groups' => $project->getVideoGroups(),
        'videos' => $project->getVideos(),
        'page_level' => 1,
      ]);

      Shade::writeFile("$target_path/videos/index.html", $this->smarty->fetch('videos.tpl'), function($path) use (&$output) {
        $output->writeln("File '$path' created");
      });

      return true;
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