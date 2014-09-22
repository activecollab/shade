<?php

  namespace ActiveCollab\Shade;

  use Shade\Element\Book, Shade\Element\Video;
  use ActiveCollab\Shade\Error\ParseJsonError;
  use Shade\ElementFinder\DefaultElementFinder;

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

    /**
     * Return all project stories
     *
     * @return Book[]
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
     * Return project videos
     *
     * @return Video[]
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
     * @var \Shade\ElementFinder\ElementFinder
     */
    private $finder;

    /**
     * @return \Shade\ElementFinder\ElementFinder
     */
    function &getFinder()
    {
      if (empty($this->finder)) {
        $this->finder = new DefaultElementFinder();
      }

      return $this->finder;
    }
  }