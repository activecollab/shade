<?php

  namespace ActiveCollab\Shade\ElementFinder;

  use ActiveCollab\Shade, ActiveCollab\Shade\Element\Book, ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\Video, ActiveCollab\Shade\Element\WhatsNewArticle, DirectoryIterator, ActiveCollab\Shade\NamedList;

  /**
   * Default element finder implementation
   *
   * @package Shade\ElementFinder
   */
  class DefaultElementFinder extends ElementFinder
  {
    /**
     * @return Book[]|void
     */
    function getBooks()
    {
      $result = new NamedList();

      if (is_dir($this->project->getBooksPath())) {
        foreach (new DirectoryIterator($this->project->getBooksPath()) as $file) {
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
     * @return Video[]|NamedList
     */
    function getVideos()
    {
      $files = [];

      if (is_dir($this->project->getVideosPath())) {
        foreach (new DirectoryIterator($this->project->getVideosPath()) as $file) {
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
     * @return WhatsNewArticle[]|NamedList
     */
    function getWhatsNewArticles()
    {
      $files = [];

      if (is_dir($this->project->getWhatsNewArticlesPath())) {
        foreach (new DirectoryIterator($this->project->getWhatsNewArticlesPath()) as $version_dir) {
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
  }