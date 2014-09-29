<?php

  namespace ActiveCollab\Shade;

  use ActiveCollab\Shade, ActiveCollab\Shade\Element\Book, ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\Video, ActiveCollab\Shade\Element\WhatsNewArticle, ActiveCollab\Shade\Element\Release;
  use DirectoryIterator, Exception;

  /**
   * Element finder definition
   *
   * @package Shade\ElementFinder
   */
  class ElementFinder
  {
    /**
     * @var Project
     */
    protected $project;

    /**
     * @var callable[]
     */
    protected $finders = [];

    /**
     * @param Project $project
     */
    function __construct(Project &$project)
    {
      $this->project = $project;

      $this->finders = [

        /**
         * Find book files
         *
         * @param string $books_path
         */
        'findBookDirs' => function($books_path) {
          $dirs = [];

          if (is_dir($books_path)) {
            foreach (new DirectoryIterator($books_path) as $dir) {
              if (!$dir->isDot() && $dir->isDir()) {
                $dirs[] = $dir->getPathname();
              }
            }
          }

          return $dirs;
        },

        /**
         * Return array of pages that are in a given book
         *
         * @param string $pages_path
         */
        'findBookPageFiles' => function($pages_path) {
          $files = [];

          if (is_dir($pages_path)) {
            foreach (new DirectoryIterator($pages_path) as $file) {
              if ($file->isFile() && $file->getExtension() == 'md') {
                $files[] = $file->getPathname();
              }
            }

            sort($files);
          }

          return $files;
        },

        /**
         * Return array of video files
         *
         * @param string $videos_path
         */
        'findVideoFiles' => function($videos_path) {
          $files = [];

          if (is_dir($videos_path)) {
            foreach (new DirectoryIterator($videos_path) as $file) {
              if ($file->isFile() && $file->getExtension() == 'md') {
                $files[] = $file->getPathname();
              }
            }

            sort($files);
          }

          return $files;
        },

        /**
         * Return releases
         *
         * @param string $releases_path
         */
        'findReleaseFiles' => function($releases_path) {
          $files = [];

          if (is_dir($releases_path)) {
            foreach (new DirectoryIterator($releases_path) as $file) {
              if ($file->isFile() && $file->getExtension() == 'md') {
                $version_number = substr($file->getFilename(), 0, strlen($file->getFilename()) - 3);

                if (Shade::isValidVersionNumber($version_number)) {
                  $files[$version_number] = $file->getPathname();
                }
              }
            }

            uksort($files, function ($a, $b) {
              return version_compare($b, $a);
            });
          }

          return $files;
        },

        /**
         * Return what's new files
         *
         * @param array $whats_new_articles_path
         */
        'findWhatsNewFiles' => function($whats_new_articles_path) {
          $files = [];

          if (is_dir($whats_new_articles_path)) {
            foreach (new DirectoryIterator($whats_new_articles_path) as $version_dir) {
              if (!$version_dir->isDot() && $version_dir->isDir() && Shade::isValidVersionNumber($version_dir->getFilename())) {
                $version_num = $version_dir->getFilename();

                foreach (new DirectoryIterator($version_dir->getPathname()) as $file) {
                  if ($file->isFile() && $file->getExtension() == 'md') {
                    $files[$version_num][] = $file->getPathname();
                  }
                }
              }
            }

            if (count($files)) {
              ksort($files);

              foreach ($files as $version_num => $version_articles) {
                sort($files[$version_num]);
              }
            }
          }

          return $files;
        }
      ];
    }

    /**
     * Set a custom finder
     *
     * @param string $name
     * @param callable $callback
     * @throws Exception
     */
    public function setCustomFinder($name, $callback)
    {
      if (empty($this->finders[$name])) {
        throw new Exception("Unknown finder '$name'");
      }

      if (!is_callable($callback)) {
        throw new Exception("Callback needs to be callable");
      }

      $this->finders[$name] = $callback;
    }

    /**
     * Get path of books folder
     *
     * @return string
     */
    function getBooksPath()
    {
      return $this->project->getPath() . '/en/books';
    }

    /**
     * @return Book[]
     */
    function getBooks()
    {
      $dirs = call_user_func($this->finders['findBookDirs'], $this->getBooksPath());

      $result = [];

      foreach ($dirs as $dir) {
        $book = new Book($this->project, $dir);

        if ($book->isLoaded()) {
          $result[$book->getShortName()] = $book;
        }
      }

      if (count($result)) {
        uasort($result, function(Book $a, Book $b) {
          if ($a->getPosition() == $b->getPosition()) {
            return 0;
          } else {
            return $a->getPosition() > $b->getPosition() ? 1 : -1;
          }
        });
      }

      return $result;
    }

    /**
     * Get book by short name
     *
     * @param string $name
     * @return Book|null
     */
    function getBook($name)
    {
      foreach($this->getBooks() as $book) {
        if($book->getShortName() === $name) {
          return $book;
        }
      }

      return null;
    }

    /**
     * @param Book $book
     * @return BookPage[]|null
     */
    function getBookPages(Book $book)
    {
      $files = call_user_func($this->finders['findBookPageFiles'], $book->getPath() . '/pages');

      $result = new NamedList();

      foreach ($files as $file) {
        $page = new BookPage($this->project, $book, $file, true);

        if ($page->isLoaded()) {
          $result->add($page->getShortName(), $page);
        }
      }

      return $result;
    }

    /**
     * Return path to the folder where we expect to find videos
     *
     * @return string
     */
    function getVideosPath()
    {
      return $this->project->getPath() . '/en/videos';
    }

    /**
     * @var Video[]|NamedList
     */
    private $videos = false;

    /**
     * @return Video[]|NamedList
     */
    function getVideos()
    {
      if ($this->videos === false) {
        $files = call_user_func($this->finders['findVideoFiles'], $this->getVideosPath());

        $this->videos = new NamedList();

        foreach ($files as $file) {
          $video = new Video($this->project, $file);

          if ($video->isLoaded()) {
            $this->videos->add($video->getShortName(), $video);
          }
        }
      }

      return $this->videos;
    }

    /**
     * Return a video
     *
     * @param string $name
     * @return Video|null
     */
    function getVideo($name)
    {
      foreach($this->getVideos() as $video) {
        if($video->getShortName() === $name) {
          return $video;
        }
      }

      return null;
    }

    /**
     * Return path to the folder where we expect to find what's new articles
     *
     * @return string
     */
    function getWhatsNewArticlesPath()
    {
      return $this->project->getPath() . '/en/whats_new';
    }

    /**
     * @return WhatsNewArticle[]|NamedList
     */
    function getWhatsNewArticles()
    {
      $files = call_user_func($this->finders['findWhatsNewFiles'], $this->getWhatsNewArticlesPath());

      $result = new NamedList();

      foreach ($files as $version_num => $version_files) {
        foreach ($version_files as $file) {
          $article = new WhatsNewArticle($this->project, $version_num, $file);

          if ($article->isLoaded()) {
            $result->add($article->getShortName(), $article);
          }
        }
      }

      return $result;
    }

    /**
     * @param string $name
     * @return WhatsNewArticle[]|null
     */
    function getWhatsNewArticle($name)
    {
      foreach($this->getWhatsNewArticles() as $article) {
        if($article->getShortName() === $name) {
          return $article;
        }
      }

      return null;
    }

    /**
     * Return path to the folder where we expect to find release entries
     *
     * @return string
     */
    function getReleasesPath()
    {
      return $this->project->getPath() . '/en/releases';
    }

    /**
     * @return Release[]
     */
    function getReleases()
    {
      $files = call_user_func($this->finders['findReleaseFiles'], $this->getReleasesPath());

      $result = [];

      foreach ($files as $version_number => $file) {
        $release = new Release($this->project, $version_number, $file, true);

        if ($release->isLoaded()) {
          $result[$release->getVersionNumber()] = $release;
        }
      }

      return $result;
    }
  }