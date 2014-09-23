<?php

  namespace ActiveCollab\Shade;

  use ActiveCollab\Shade, ActiveCollab\Shade\Element\Book, ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\Video, ActiveCollab\Shade\Element\WhatsNewArticle;
  use ActiveCollab\Shade\Error\ParseJsonError;
  use ActiveCollab\Shade\ElementFinder\DefaultElementFinder;

  /**
   * Narrative project
   *
   * @package Narrative
   */
  final class Project {

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $configuration = [];

    /**
     * Create a new project instance
     *
     * @param string $path
     * @throws |ActiveCollab\Narrative\Error\ParseJsonError
     */
    function __construct($path) {
      $this->path = $path;

      if($this->isValid()) {
        $configuration_json = file_get_contents($this->path . '/project.json');
        $this->configuration = json_decode($configuration_json, true);

        if($this->configuration === null) {
          throw new ParseJsonError($configuration_json, json_last_error());
        }

        if(empty($this->configuration)) {
          $this->configuration = [];
        }
      }
    }

    /**
     * Return project name
     *
     * @return string
     */
    function getName() {
      return $this->getConfigurationOption('name', basename($this->path));
    }

    /**
     * Return true if this project is multilingual
     *
     * @return bool
     */
    function isMultilingual()
    {
      return (boolean) $this->getConfigurationOption('is_multilingual');
    }

    /**
     * Return default build target
     *
     * @return string|null
     */
    function getDefaultBuildTarget()
    {
      return $this->getConfigurationOption('default_build_target');
    }

    /**
     * Return default locale, for multilingual projects
     *
     * @return string|null
     */
    function getDefaultLocale()
    {
      return $this->getConfigurationOption('default_locale');
    }

    /**
     * Return name of the default build theme
     *
     * @return string
     */
    function getDefaultBuildTheme()
    {
      return $this->getConfigurationOption('default_build_theme', 'default');
    }

    /**
     * Return configuration option
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getConfigurationOption($name, $default = null)
    {
      return isset($this->configuration[$name]) && $this->configuration[$name] ? $this->configuration[$name] : $default;
    }

    /**
     * Return project path
     *
     * @return string
     */
    function getPath() {
      return $this->path;
    }

    // ---------------------------------------------------
    //  Books
    // ---------------------------------------------------

    /**
     * Get path of books folder
     *
     * @return string
     */
    function getBooksPath()
    {
      return $this->getPath() . '/en_US.UTF-8/books';
    }

    /**
     * Return all project stories
     *
     * @return Book[]|NamedList
     */
    function getBooks() {
      return $this->getFinder()->getBooks();
    }

    /**
     * Get book by short name
     *
     * @param string $name
     * @return Book|null
     */
    function getBook($name) {
      return $this->getFinder()->getBook($name);
    }

    /**
     * @param Book $book
     * @return BookPage[]|NamedList
     */
    function getBookPages(Book $book)
    {
      return $this->getFinder()->getBookPages($book);
    }

    /**
     * Return array of common questions
     *
     * @return array
     */
    public function getCommonQuestions()
    {
      $result = [];

      foreach ($this->getBooks() as $book) {
        $book->populateCommonQuestionsList($result);
      }

      usort($result, function ($a, $b) {
        if ($a['position'] == $b['position']) {
          return 0;
        }

        return ($a['position'] < $b['position']) ? -1 : 1;
      });

      return $result;
    }

    // ---------------------------------------------------
    //  What's New Articles
    // ---------------------------------------------------

    /**
     * Return path to the folder where we expect to find what's new articles
     *
     * @return string
     */
    function getWhatsNewArticlesPath()
    {
      return $this->getPath() . '/en_US.UTF-8/whats_new';
    }

    /**
     * @return WhatsNewArticle[]|NamedList
     */
    function getWhatsNewArticles()
    {
      return $this->getFinder()->getWhatsNewArticles();
    }

    /**
     * Return what's new article
     *
     * @param string $name
     * @return WhatsNewArticle|null
     */
    function getWhatsNewArticle($name)
    {
      return $this->getFinder()->getWhatsNewArticle($name);
    }

    // ---------------------------------------------------
    //  Video
    // ---------------------------------------------------

    /**
     * Return path to the folder where we expect to find videos
     *
     * @return string
     */
    function getVideosPath()
    {
      return $this->getPath() . '/en_US.UTF-8/videos';
    }

    /**
     * @var array
     */
    private $video_groups = false;

    /**
     * Return array of video groups
     *
     * @return array
     */
    public function getVideoGroups()
    {
      if ($this->video_groups === false) {
        $this->video_groups = $this->getConfigurationOption('video_groups');

        if (!is_array($this->video_groups)) {
          $this->video_groups = [
            Video::GETTING_STARTED => Shade::lang('Getting Started'),
          ];
        }
      }

      return $this->video_groups;
    }

    /**
     * Return project videos
     *
     * @return Video[]|NamedList
     */
    function getVideos()
    {
      return $this->getFinder()->getVideos();
    }

    /**
     * @param string $name
     * @return Video|null
     */
    function getVideo($name)
    {
      return $this->getFinder()->getVideo($name);
    }

    /**
     * Return true if this is a valid project
     *
     * @return bool
     */
    function isValid() {
      return is_dir($this->path) && is_file($this->path . '/project.json');
    }

    /**
     * @var \ActiveCollab\Shade\ElementFinder\ElementFinder
     */
    private $finder;

    /**
     * @return \ActiveCollab\Shade\ElementFinder\ElementFinder
     */
    function &getFinder()
    {
      if (empty($this->finder)) {
        $this->finder = new DefaultElementFinder($this);
      }

      return $this->finder;
    }
  }