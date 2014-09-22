<?php

  namespace Shade\ElementFinder;

  /**
   * Element finder definition
   *
   * @package Shade\ElementFinder
   */
  abstract class ElementFinder
  {
    /**
     * @return \Shade\Element\Book[]
     */
    abstract function getBooks();


    /**
     * Get book by short name
     *
     * @param string $name
     * @return \Shade\Element\Book|null
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

    abstract function getBookStories();

    abstract function getVideos();

    abstract function getWhatsNewArticles();
  }