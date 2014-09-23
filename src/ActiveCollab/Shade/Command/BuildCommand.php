<?php

  namespace ActiveCollab\Shade\Command;

  use ActiveCollab\Shade, ActiveCollab\Shade\Project, ActiveCollab\Shade\Theme, ActiveCollab\Shade\Element\Book, ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\WhatsNewArticle, ActiveCollab\Shade\Element\Video;
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
      $this->smarty->assign('common_questions', $project->getCommonQuestions());

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
        if (empty($current_whats_new_article)) {
          $current_whats_new_article = $whats_new_article; // First article is current article
        }

        $this->copyVersionImages($whats_new_article, $target_path, $output);
      }

      $whats_new_articles_by_version = $this->getWhatsNewArticlesByVersion($whats_new_articles);


      $this->smarty->assign([
        'whats_new_articles' => $whats_new_articles,
        'whats_new_articles_by_version' => $whats_new_articles_by_version,
        'current_whats_new_article' => $this->getCurrentArticleFromSortedArticles($whats_new_articles_by_version),
      ]);

      Shade::writeFile("$target_path/whats-new/index.html", $this->smarty->fetch('whats_new_article.tpl'), function($path) use (&$output) {
        $output->writeln("File '$path' created");
      });

      foreach ($whats_new_articles as $whats_new_article) {
        $this->smarty->assign('current_whats_new_article', $whats_new_article);

        Shade::writeFile("$target_path/whats-new/" . $whats_new_article->getShortName() . ".html", $this->smarty->fetch('whats_new_article.tpl'), function($path) use (&$output) {
          $output->writeln("File '$path' created");
        });
      }

      return true;

      // Build what's new articles
      $article_template = file_get_contents(HelpFramework::PATH . '/static/templates/articles.html');

      $articles = AngieApplication::help()->getWhatsNew();

      if(is_foreachable($articles)) {
        $articles_by_version = array();

        foreach($articles as $article) {
          $version = $article->getVersionNumber();

          if(empty($articles_by_version[$version])) {
            $articles_by_version[$version] = array($article);
          } else {
            $articles_by_version[$version][] = $article;
          }
        } // foreach

        $sidebar_menu = '';
        $whats_new_json = array();

        $rss_feed = '<?xml version="1.0" encoding="UTF-8" ?>
          <rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
            <channel>
              <title>activeCollab Help</title>
              <link>https://activecollab.com/help/whats-new</link>
              <description>What\'s New Articles</description>
              <pubDate>'.date('D, d M Y H:i:s O').'</pubDate>';

        if(is_foreachable($articles_by_version)) {
          foreach($articles_by_version as $version => $version_articles) {
            $sidebar_menu .= '<p>'.$version.'</p>';

            $sidebar_menu .= '<ol>';
            foreach($version_articles as $article) {
              $sidebar_menu .= '<li><a href="'.$article->getShortName().'.html">'.$article->getTitle().'</a></li>';

              // Build RSS feed
              $rss_feed .= '<item>
                <title>'.$article->getTitle().'</title>
                <link>https://activecollab.com/help/whats-new/'.$article->getShortName().'.html</link>
                <guid>https://activecollab.com/help/whats-new/'.$article->getShortName().'.html</guid>
                <description><![CDATA['.AngieApplication::help()->renderBody($article).']]></description>
              </item>';

              // Build JSON
              $whats_new_json[$version][] = array(
              'title' => $article->getTitle(),
              'body' => AngieApplication::help()->renderBody($article),
              'permalink' => 'https://activecollab.com/help/whats-new/'.$article->getShortName().'.html'
              );
            } // foreach
            $sidebar_menu .= '</ol>';
          } // foreach
        } // if

        $rss_feed .= '</channel></rss>';
        $this->createFile("$destination_path/whats-new/rss.xml", $rss_feed, $output, true);

        $this->createDir("$destination_path/whats-new/articles", $output);
        $this->createFile("$destination_path/whats-new/articles/articles.json", 'whatsNewArticles({'.json_encode($whats_new_json).'})', $output, true);

        $length = $articles->count();
        $keys = $articles->keys();

        $counter = 0;
        foreach($articles as $article) {
          $sidebar_menu_selected = '';
          if(strpos($sidebar_menu, $article->getShortName())) {
            $sidebar_menu_selected = str_replace('<li><a href="' . $article->getShortName() . '.html">', '<li class="selected"><a href="' . $article->getShortName() . '.html">', $sidebar_menu);
          } // if

          $article_page = str_replace('--SIDEBAR-MENU--', $sidebar_menu_selected, $article_template);

          // Generate prev/next links for current what's new article
          if(in_array($article->getShortName(), $keys)) {

            // First page
            if($counter == 0) {
              $prev_link = '#';
              $next_link = $length == 1 ? '#' : $articles[$keys[$counter + 1]]->getShortName().'.html';

              // Second page
            } elseif($counter == $length - 1) {
              $prev_link = $articles[$keys[$counter - 1]]->getShortName().'.html';
              $next_link = '#';

              // Other pages
            } else {
              $prev_link = $articles[$keys[$counter - 1]]->getShortName().'.html';
              $next_link = $articles[$keys[$counter + 1]]->getShortName().'.html';
            } // if
          } // if

          $prev = '<a href="'.$prev_link.'">&laquo; Prev</a>';
          $next = '<a href="'.$next_link.'">Next &raquo;</a>';

          // First page
          if($counter == 0) {
            $prev = '';
          } // if

          // Last page
          if($counter == $length - 1) {
            $next = '';
          } // if

          $content = '<div class="help_book_page">
            <h1>'.$article->getTitle().'</h1>
            <div class="help_book_page_content">'.AngieApplication::help()->renderBody($article).'</div>
            <div class="help_book_page_comments">
              <div id="disqus_thread"></div>
              <script type="text/javascript">
                var disqus_shortname = "activecollab";
                var disqus_identifier = "/help/whats-new/'.$article->getShortName().'";
                var disqus_title = "'.$article->getTitle().'";
                var disqus_url = "https://activecollab.com/help/whats-new/'.$article->getShortName().'.html";

                (function() {
                  var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
                  dsq.src = "//" + disqus_shortname + ".disqus.com/embed.js";
                  (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
                })();
              </script>
            </div>
            <div class="help_book_footer">
              <div class="help_book_footer_inner">
                <div class="help_book_footer_prev">'.$prev.'</div>
                <div class="help_book_footer_top"><a href="#" onclick="window.scrollTo(0, 0); return false;">Back to the Top</a></div>
                <div class="help_book_footer_next">'.$next.'</div>
              </div>
            </div>
          </div>';

          $article_page = str_replace('--CONTENT--', $content, $article_page);

          // Make main what's new article of the first one
          if($counter == 0) {
            $this->createFile($destination_path.'/whats-new/index.html', $article_page, $output, true);
          } // if

          $this->createFile($destination_path.'/whats-new/'.$article->getShortName().'.html', $article_page, $output, true);

          $counter++;
        } // foreach
      } // if
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
        Shade::copyDir("$version_path/images", "$target_path/assets/images/whats-new/$version_num", function($path) use (&$output) {
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