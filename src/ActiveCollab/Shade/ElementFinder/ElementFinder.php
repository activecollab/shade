<?php

  namespace Shade\ElementFinder;

  use Shade\Element\Book;

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
     * @return \Shade\Element\BookPage[]|null
     */
    abstract function getBookPages(Book $book);

    /**
     * @return \Shade\Element\Video[]|null
     */
    abstract function getVideos();

    /**
     * Return a video
     *
     * @param string $name
     * @return \Shade\Element\Video|null
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
     * @return \Shade\Element\WhatsNewArticle[]|null
     */
    abstract function getWhatsNewArticles();

    /**
     * @param string $name
     * @return \Shade\Element\WhatsNewArticle[]|null
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
  }