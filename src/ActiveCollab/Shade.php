<?php

  namespace ActiveCollab;

  use ActiveCollab\Shade\Error\TempNotFoundError;
  use ActiveCollab\Shade\Error\ThemeNotFoundError;
  use ActiveCollab\Shade\Project, ActiveCollab\Shade\Theme, ActiveCollab\Shade\Element\Element, ActiveCollab\Shade\NamedList;
  use ActiveCollab\Shade\SmartyHelpers;
  use Exception, RecursiveIteratorIterator, RecursiveDirectoryIterator, Smarty, ReflectionClass, ReflectionMethod, Michelf\MarkdownExtra, Urlify, Hyperlight\Hyperlight;

  /**
   * Main class for interaction with Shade projects
   */
  final class Shade
  {
    const VERSION = '1.0.0';

    /**
     * Name of the default video group
     */
    const GETTING_STARTED_VIDEO_GROUP = 'getting-started';

    /**
     * Cached array of articles
     *
     * @var array
     */
    private $whats_new_articles = [];

    /**
     * Return list of articles that $user can see
     *
     * @param  User|null                       $user
     * @return HelpWhatsNewArticle[]|NamedList
     */
    public function getWhatsNew(User $user = null)
    {
      $key = $user instanceof User ? $user->getId() : 'all';

      if (empty($this->whats_new_articles[$key])) {
        $articles = new NamedList();

        foreach (AngieApplication::getModules() as $module) {
          $whats_new_folders = get_folders($module->getPath() . '/help/whats_new');

          if ($whats_new_folders) {
            rsort($whats_new_folders);

            foreach ($whats_new_folders as $whats_new_folder) {
              $version_number = basename($whats_new_folder);

              if ($this->isValidVersionNumber($version_number)) {
                $whats_new_files = get_files($whats_new_folder, 'md');

                if ($whats_new_files) {
                  sort($whats_new_files);

                  foreach ($whats_new_files as $whats_new_file) {
                    $article = new HelpWhatsNewArticle($module->getName(), $version_number, $whats_new_file);

                    if ($article->isLoaded()) {
                      if ($user instanceof User && !$article->canView($user)) {
                        continue;
                      }

                      $articles->add($article->getShortName(), $article);
                    }
                  }
                }
              }
            }
          }
        }

        $this->whats_new_articles[$key] = $articles;
      }

      return $this->whats_new_articles[$key];
    }

    /**
     * Returns true if $version is a valid angie application version number
     *
     * @param  string  $version
     * @return boolean
     */
    public static function isValidVersionNumber($version)
    {
      if (strpos($version, '.') !== false) {
        $parts = explode('.', $version);

        if (count($parts) == 3) {
          foreach ($parts as $part) {
            if (!is_numeric($part)) {
              return false;
            }
          }

          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    }

    /**
     * Cached list of books
     *
     * @var array
     */
    private $books = [];

    /**
     * Return books that $user can access
     *
     * @param  User                 $user
     * @return HelpBook[]|NamedList
     */
    public function getBooks(User $user = null)
    {
      $key = $user instanceof User ? $user->getId() : 'all';

      if (empty($this->books[$key])) {
        $books = new NamedList();

        $possible_locations = array();

        foreach (AngieApplication::getFrameworks() as $framework) {
          $possible_locations[$framework->getName()] = $framework->getPath() . '/help/books';
        }

        foreach (AngieApplication::getModules() as $module) {
          $possible_locations[$module->getName()] = $module->getPath() . '/help/books';
        }

        foreach ($possible_locations as $module_name => $path) {
          $book_folders = get_folders($path);

          if ($book_folders) {
            foreach ($book_folders as $book_folder) {
              $book = new HelpBook($module_name, $book_folder);

              if ($book->isLoaded()) {
                if ($user instanceof User && !$book->canView($user)) {
                  continue; // Skip if user can't see this book
                }

                $books->add($book->getShortName(), $book);
              }
            }
          }
        }

        $books->sort(function ($a, $b) {
          if ($a instanceof HelpBook && $b instanceof HelpBook) {
            $order_a = (integer) $a->getProperty('position');
            $order_b = (integer) $b->getProperty('position');

            if ($order_a == $order_b) {
              return 0;
            }

            return ($order_a < $order_b) ? -1 : 1;
          }

          return 0;
        });

        $this->books[$key] = $books;
      }

      return $this->books[$key];
    }

    /**
     * Return array of common questions
     *
     * @return array
     */
    public static function getCommonQuestions()
    {
      return [];

//      if ($user instanceof User) {
//        $cache_key = $user->getCacheKey('common_help_questions');
//      } else {
//        $cache_key = 'common_help_questions';
//      }
//
//      return AngieApplication::cache()->get($cache_key, function () use ($user) {
//        $result = array();
//
//        foreach (AngieApplication::help()->getBooks($user) as $book) {
//          $book->populateCommonQuestionsList($result, $user);
//        }
//
//        usort($result, function ($a, $b) {
//          if ($a['position'] == $b['position']) {
//            return 0;
//          }
//
//          return ($a['position'] < $b['position']) ? -1 : 1;
//        });
//
//        return $result;
//      });
    } // getCommonQuestions

    /**
     * Return array of video groups
     *
     * @return NamedList
     */
    public function getVideoGroups()
    {
      return new NamedList(array(
      AngieHelpDelegate::GETTING_STARTED_VIDEO_GROUP => gettext('Getting Started'),
      ));
    } // getVideoGroups

    /**
     * Cached array of videos
     *
     * @var array
     */
    private $videos = array();

    /**
     * Return videos that $user can access
     *
     * @param  User                  $user
     * @return HelpVideo[]|NamedList
     */
    public function getVideos(User $user = null)
    {
      $key = $user instanceof User ? $user->getId() : 'all';

      if (empty($this->videos[$key])) {
        $videos = new NamedList();

        foreach (AngieApplication::getModules() as $module) {
          $video_files = get_files($module->getPath() . '/help/videos', 'md');

          if ($video_files) {
            foreach ($video_files as $video_file) {
              $video = new HelpVideo($module->getName(), $video_file);

              if ($video->isLoaded()) {
                if ($user instanceof User && !$video->canView($user)) {
                  continue;
                }

                $videos->add($video->getShortName(), $video);
              }
            }
          }
        }

        $this->videos[$key] = $videos;
      }

      return $this->videos[$key];
    } // getVideos

    /**
     * Return user groups that describe this user
     *
     * @param  User  $user
     * @return array
     */
    public function getUserGroups(User $user)
    {
      $groups = array(get_class($user));

      if (AngieApplication::isInDevelopment()) {
        $groups[] = 'Developer';
      }

      if (AngieApplication::isOnDemand()) {
        $groups[] = 'On Demand User';
      }

//      if ($this->user_groups_callback instanceof Closure) {
//        $this->user_groups_callback->__invoke($user, $groups);
//      }
      return $groups;
    } // getUserGroups

    /**
     * Closure that is called when user groups is called
     *
     * @var Closure|null
     */
    private $user_groups_callback;

    /**
     * Set user groups callback
     *
     * @param $callback
     * @throws InvalidInstanceError
     */
    public function setOnUserGroupsCallback($callback)
    {
      if ($callback instanceof Closure || $callback === null) {
        $this->user_groups_callback = $callback;
      } else {
        throw new InvalidInstanceError('callback', $callback, 'Closure');
      }
    } // setOnUserGroupsCallback

    /**
     * Render body of a given element
     *
     * @param  Element $element
     * @return string
     */
    public function renderBody(Project $project, Element $element)
    {
      $template = self::$smarty->createTemplate($element->getIndexFilePath());

      SmartyHelpers::setCurrentElement($element);

      $content = $template->fetch();

      HelpElementHelpers::setCurrentElement(null);

      $separator_pos = strpos($content, HelpElement::PROPERTIES_SEPARATOR);

      if ($separator_pos === false) {
        if (substr($content, 0, 1) == '*') {
          $content = '*Content Not Provided*';
        }
      } else {
        $content = trim(substr($content, $separator_pos + strlen(HelpElement::PROPERTIES_SEPARATOR)));
      }

      return HTML::markdownToHtml($content);
    } // renderBody

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * URL generator
     *
     * @var callable|null
     */
    private static $url_generator = null;

    /**
     * Set URL generator
     *
     * @param callable|null $generator
     * @throws Exception
     */
    public static function setUrlGenerator($generator)
    {
      if (is_callable($generator) || $generator === null) {
        self::$url_generator = $generator;
      } else {
        throw new Exception('Generator is not callable (or NULL)');
      }
    }

    /**
     * Return URL of an element
     *
     * @param  Element   $element
     * @return string
     * @throws Exception
     */
    public static function getUrl(Element $element)
    {
      if (self::$url_generator && self::$url_generator instanceof Closure) {
        return call_user_func(self::$url_generator, $element);
      } else {
//        if ($element instanceof HelpBook) {
//          return Router::assemble('help_book', array(
//          'book_name' => $element->getShortName()
//          ));
//        } elseif ($element instanceof HelpBookPage) {
//          return Router::assemble('help_book_page', array(
//          'book_name' => $element->getBookName(),
//          'page_name' => $element->getSlug(),
//          ));
//        } elseif ($element instanceof HelpWhatsNewArticle) {
//          return Router::assemble('help_whats_new_article', array(
//          'article_name' => $element->getSlug(),
//          ));
//        } elseif ($element instanceof HelpVideo) {
//          return Router::assemble('help_video', array(
//          'video_name' => $element->getSlug()
//          ));
//        } else {
//          throw new Exception('Element is expected to be an instance of \ActiveCollab\Shade\Element class');
//        }
      }
    }

    /**
     * @var callable|null
     */
    private static $image_url_generator = null;

    /**
     * Set URL generator
     *
     * @param  callable|null      $generator
     * @throws \Exception
     */
    public function setImageUrlGenerator($generator)
    {
      if (is_callable($generator) || $generator === null) {
        self::$image_url_generator = $generator;
      } else {
        throw new Exception('Generator is not callable (or NULL)');
      }
    }

    /**
     * Return image URL
     *
     * @param  Element $current_element
     * @param  string  $name
     * @return string
     */
    public static function getImageUrl($current_element, $name)
    {
      if (self::$image_url_generator) {
        return call_user_func(self::$image_url_generator, $current_element, $name);
      } else {
        if ($current_element instanceof HelpBookPage) {
          $params['src'] = AngieApplication::getImageUrl('books/' . $current_element->getBookName() . '/' . $name, $current_element->getModuleName(), 'help');
        } elseif ($current_element instanceof HelpBook) {
          $params['src'] = AngieApplication::getImageUrl('books/' . $current_element->getShortName() . '/' . $name, $current_element->getModuleName(), 'help');
        } elseif ($current_element instanceof HelpVideo) {
          $params['src'] = AngieApplication::getImageUrl('videos/' . $name, $current_element->getModuleName(), 'help');
        } elseif ($current_element instanceof HelpWhatsNewArticle) {
          $params['src'] = AngieApplication::getImageUrl('whats-new/' . $current_element->getVersionNumber() . '/' . $name, $current_element->getModuleName(), 'help');
        } else {
          return 'Unknown';
        }

        return '<div class="center">' . self::htmlTag('img', $params) . '</div>';
      }
    }

    /**
     * @param string $name
     * @return Theme
     * @throws ThemeNotFoundError
     */
    public static function getBuildTheme($name)
    {
      $theme_path = realpath(__DIR__ . "/../../themes/$name");

      if ($theme_path && is_dir($theme_path)) {
        return new Theme($theme_path);
      } else {
        throw new ThemeNotFoundError($name, $theme_path);
      }
    }

    /**
     * @var Smarty
     */
    private static $smarty = false;

    /**
     * Prepare and return Smarty instance
     *
     * @param Theme $theme
     * @param Project $project
     * @return Smarty
     * @throws TempNotFoundError
     */
    public static function &getSmarty(Project $project, Theme $theme)
    {
      if (self::$smarty === false) {
        self::$smarty = new Smarty();

        self::$smarty->setCompileDir(self::getTempPath());
        self::$smarty->setTemplateDir($theme->getPath() . '/templates');
        self::$smarty->compile_check = true;
        self::$smarty->left_delimiter = '<{';
        self::$smarty->right_delimiter = '}>';
        self::$smarty->registerFilter('variable', '\ActiveCollab\Shade::clean'); // {$foo nofilter}

        $helper_class = new ReflectionClass('\ActiveCollab\Shade\SmartyHelpers');

        foreach ($helper_class->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC) as $method) {
          $method_name = $method->getName();

          if (substr($method_name, 0, 6) === 'block_') {
            self::$smarty->registerPlugin('block', substr($method_name, 6), ['\ActiveCollab\Shade\SmartyHelpers', $method_name]);
          } elseif (substr($method_name, 9) === 'function_') {
            self::$smarty->registerPlugin('function', substr($method_name, 9), ['\ActiveCollab\Shade\SmartyHelpers', $method_name]);
          };
        }

        self::$smarty->assign([
          'copyright' => $project->getConfigurationOption('copyright', '--UNKNOWN--'),
          'copyright_since' => $project->getConfigurationOption('copyright_since'),
        ]);
      }

      return self::$smarty;
    }

    /**
     * @var string
     */
    static private $temp_path = false;

    /**
     * Return temp folder path
     *
     * @return string
     * @throws TempNotFoundError
     */
    public static function getTempPath()
    {
      if (self::$temp_path === false) {
        $path = realpath(__DIR__ . "/../../temp");

        if ($path && is_dir($path)) {
          self::$temp_path = $path;
        } else {
          throw new TempNotFoundError($path);
        }
      }

      return self::$temp_path;
    }

    /**
     * Check if we are in testing mode
     *
     * @return bool
     */
    public static function isTesting()
    {
      return true;
    }

    // ---------------------------------------------------
    //  Utilities
    // ---------------------------------------------------

    /**
     * Equivalent to htmlspecialchars(), but allows &#[0-9]+ (for unicode)
     *
     * @param string $str
     * @return string
     * @throws Exception
     */
    public static function clean($str)
    {
      if (is_scalar($str)) {
        $str = preg_replace('/&(?!#(?:[0-9]+|x[0-9A-F]+);?)/si', '&amp;', $str);
        $str = str_replace([ '<', '>', '"' ], [ '&lt;', '&gt;', '&quot;' ], $str);

        return $str;
      } elseif ($str === null) {
        return '';
      } else {
        throw new Exception('Input needs to be scalar value');
      }
    }

    /**
     * Returns an underscore-syntaxed ($like_this_dear_reader) version of the $camel_cased_word.
     *
     * @param string $camel_cased_word
     * @return string
     */
    public static function underscore($camel_cased_word)
    {
      $camel_cased_word = preg_replace('/([A-Z]+)([A-Z])/','\1_\2', $camel_cased_word);

      return strtolower(preg_replace('/([a-z])([A-Z])/','\1_\2', $camel_cased_word));
    }

    /**
     * Return slug from string
     *
     * @static
     * @param $string
     * @param string $space
     * @return mixed|string
     */
    public static function slug($string, $space = '-')
    {
      $string = URLify::transliterate($string);

      $string = preg_replace("/[^a-zA-Z0-9 -]/", '', $string);
      $string = strtolower($string);
      $string = str_replace(" ", $space, $string);

      while (strpos($string, '--') !== false) {
        $string = str_replace('--', '-', $string);
      }

      return trim($string);
    }

    /**
     * Open HTML tag
     *
     * @param  string               $name
     * @param  array                $attributes
     * @param  callable|string|null $content
     * @return string
     */
    public static function htmlTag($name, $attributes = null, $content = null)
    {
      if ($attributes) {
        $result = "<$name";

        foreach ($attributes as $k => $v) {
          if ($k) {
            if (is_bool($v)) {
              if ($v) {
                $result .= " $k";
              }
            } else {
              $result .= ' ' . $k . '="' . ($v ? self::clean($v) : $v) . '"';
            }
          }
        }

        $result .= '>';
      } else {
        $result = "<$name>";
      }

      if ($content) {
        if (is_callable($content)) {
          $result .= call_user_func($content);
        } else {
          $result .= $content;
        }

        $result .= "</$name>";
      }

      return $result;
    }

    /**
     * @param string $markdown
     * @return string
     */
    public static function markdownToHtml($markdown)
    {
      return MarkdownExtra::defaultTransform($markdown);
    }

    /**
     * Renders the full preview with line numbers and all necessary DOM
     *
     * @param string $content
     * @param string $syntax
     * @return string
     */
    public static function highlightCode($content, $syntax) {
      $content = trim($content);

      if ($syntax) {
        $hyperlight = new Hyperlight(strtolower($syntax));
        $content = $hyperlight->render($content);
      } else {
        $content = self::clean($content);
      }

      $number_of_lines = count(explode("\n", $content));

      $output = '<div class="syntax_higlighted source-code">';
      $output.= 	'<div class="syntax_higlighted_line_numbers lines"><pre>' . implode("\n", range(1, $number_of_lines)) . '</pre></div>';
      $output.=		'<div class="syntax_higlighted_source"><pre>' . $content . '</pre></div>';
      $output.= '</div>';

      return $output;
    }

    /**
     * @param string $source_path
     * @param string $target_path
     * @param callable|null $on_create_dir
     * @param callable|null $on_copy_file
     */
    public static function copyDir($source_path, $target_path, $on_create_dir = null, $on_copy_file = null)
    {
      if (!is_dir($target_path)) {
        mkdir($target_path, 0755);
      }

      /**
       * @var RecursiveDirectoryIterator[] $iterator
       */
      foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source_path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {

        if ($item->isDir()) {
          mkdir($target_path . DIRECTORY_SEPARATOR . $iterator->getSubPathname());

          if ($on_create_dir) {
            call_user_func($on_create_dir, $target_path . DIRECTORY_SEPARATOR . $iterator->getSubPathname());
          }
        } else {
          copy($item, $target_path . DIRECTORY_SEPARATOR . $iterator->getSubPathname());

          if ($on_copy_file) {
            call_user_func($on_copy_file, $item->getPath(), $target_path . DIRECTORY_SEPARATOR . $iterator->getSubPathname());
          }
        }
      }
    }

    /**
     * Clear all files and subfolders from $path
     *
     * @param string $path
     * @param callable|null $on_item_deleted
     * @param bool $is_subpath
     */
    public static function clearDir($path, $on_item_deleted = null, $is_subpath = false)
    {
      if (is_link($path)) {
        // Don't follow links
      } elseif (is_file($path)){
        if (unlink($path)) {
          if ($on_item_deleted) {
            call_user_func($on_item_deleted, $path);
          }
        }
      }  elseif (is_dir($path)) {
        foreach(glob(rtrim($path, '/') . '/*') as $index => $subdir_path) {
          Shade::clearDir($subdir_path, $on_item_deleted, true);
        }

        if ($is_subpath && rmdir($path)) {
          if ($on_item_deleted) {
            call_user_func($on_item_deleted, $path);
          }
        }
      }
    }

    /**
     * Write a new file with given content
     *
     * @param string $path
     * @param string $content
     * @param callable|null $on_file_created
     * @return int
     */
    public static function writeFile($path, $content, $on_file_created = null)
    {
      $overwrite = file_exists($path);

      if (file_put_contents($path, $content) && $on_file_created) {
        call_user_func($on_file_created, $path, $overwrite);
      }
    }
  }