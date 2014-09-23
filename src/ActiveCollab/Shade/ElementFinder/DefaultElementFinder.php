<?php

  namespace ActiveCollab\Shade\ElementFinder;

  use ActiveCollab\Shade\Element\Book, ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\Video, ActiveCollab\Shade\Element\WhatsNewArticle, DirectoryIterator, ActiveCollab\Shade\NamedList;

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

    }

    /**
     * @param Book $book
     * @return BookPage[]|null
     */
    function getBookPages(Book $book)
    {
      $file_names = [];

      foreach (new DirectoryIterator($book->getPath() . '/pages') as $file) {
        if ($file->isFile() && $file->getExtension() == 'md') {
          $file_names[] = $file->getPathname();
        }
      }

      sort($file_names);

      $result = new NamedList();

      foreach ($file_names as $file) {
        $page = new BookPage($this->project, $book, $file, true);

        if ($page->isLoaded()) {
          $result->add($page->getShortName(), $page);
        }
      }

      return $result;
    }

    /**
     * @return Video[]|null|void
     */
    function getVideos()
    {

    }

    /**
     * @return WhatsNewArticle[]|null|void
     */
    function getWhatsNewArticles()
    {

    }
  }