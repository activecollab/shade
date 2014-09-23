<?php

  namespace ActiveCollab\Shade\ElementFinder;

  use ActiveCollab\Shade, ActiveCollab\Shade\Element\Book, ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\Video, ActiveCollab\Shade\Element\WhatsNewArticle, DirectoryIterator, ActiveCollab\Shade\NamedList, ActiveCollab\Shade\Element\Release;

  /**
   * Default element finder implementation
   *
   * @package Shade\ElementFinder
   */
  class DefaultElementFinder extends ElementFinder
  {
    /**
     * Get path of books folder
     *
     * @return string
     */
    function getBooksPath()
    {
      return $this->project->getPath() . '/en_US.UTF-8/books';
    }

    /**
     * @return Book[]|void
     */
    function getBooks()
    {
      $result = new NamedList();

      if (is_dir($this->getBooksPath())) {
        foreach (new DirectoryIterator($this->getBooksPath()) as $file) {
          if (!$file->isDot() && $file->isDir()) {
            $book = new Book($this->project, $file->getPathname());

            if ($book->isLoaded()) {
              $result->add($book->getShortName(), $book);
            }
          }
        }
      }

      return $result;
    }

    /**
     * @param Book $book
     * @return BookPage[]|null
     */
    function getBookPages(Book $book)
    {
      $files = [];

      foreach (new DirectoryIterator($book->getPath() . '/pages') as $file) {
        if ($file->isFile() && $file->getExtension() == 'md') {
          $files[] = $file->getPathname();
        }
      }

      sort($files);

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
      return $this->project->getPath() . '/en_US.UTF-8/videos';
    }

    /**
     * @return Video[]|NamedList
     */
    function getVideos()
    {
      $files = [];

      if (is_dir($this->getVideosPath())) {
        foreach (new DirectoryIterator($this->getVideosPath()) as $file) {
          if ($file->isFile() && $file->getExtension() == 'md') {
            $files[] = $file->getPathname();
          }
        }

        sort($files);
      }

      $result = new NamedList();

      foreach ($files as $file) {
        $video = new Video($this->project, $file);

        if ($video->isLoaded()) {
          $result->add($video->getShortName(), $video);
        }
      }

      return $result;
    }

    /**
     * Return path to the folder where we expect to find what's new articles
     *
     * @return string
     */
    function getWhatsNewArticlesPath()
    {
      return $this->project->getPath() . '/en_US.UTF-8/whats_new';
    }

    /**
     * @return WhatsNewArticle[]|NamedList
     */
    function getWhatsNewArticles()
    {
      $files = [];

      if (is_dir($this->getWhatsNewArticlesPath())) {
        foreach (new DirectoryIterator($this->getWhatsNewArticlesPath()) as $version_dir) {
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

    // ---------------------------------------------------
    //  Releases
    // ---------------------------------------------------

    /**
     * Return path to the folder where we expect to find release entries
     *
     * @return string
     */
    function getReleasesPath()
    {
      return $this->project->getPath() . '/en_US.UTF-8/releases';
    }

    /**
     * @return Release[]
     */
    function getReleases()
    {
      $files = [];

      if (is_dir($this->getReleasesPath()))
      foreach (new DirectoryIterator($this->getReleasesPath()) as $file) {
        if ($file->isFile() && $file->getExtension() == 'md') {
          $version_number = substr($file->getFilename(), 0, strlen($file->getFilename()) - 3);

          if (Shade::isValidVersionNumber($version_number)) {
            $files[$version_number] = $file->getPathname();
          }
        }
      }

      uksort($files, function($a, $b) {
        return version_compare($b, $a);
      });

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