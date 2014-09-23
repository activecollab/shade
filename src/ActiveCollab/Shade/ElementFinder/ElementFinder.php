<?php

  namespace ActiveCollab\Shade\ElementFinder;

  use ActiveCollab\Shade\Project, ActiveCollab\Shade\Element\Book, ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\Video, ActiveCollab\Shade\Element\WhatsNewArticle;

  /**
   * Element finder definition
   *
   * @package Shade\ElementFinder
   */
  abstract class ElementFinder
  {
    /**
     * @var Project
     */
    protected $project;

    /**
     * @param Project $project
     */
    function __construct(Project &$project)
    {
      $this->project = $project;
    }

    /**
     * @return Book[]
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
     * @return BookPage[]|null
     */
    abstract function getBookPages(Book $book);

    /**
     * @return Video[]|null
     */
    abstract function getVideos();

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
     * @return WhatsNewArticle[]|null
     */
    abstract function getWhatsNewArticles();

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
  }