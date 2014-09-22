<?php

  namespace ActiveCollab\Shade;

  use Shade\Element\Book;
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
      return isset($this->configuration['name']) && $this->configuration['name'] ? $this->configuration['name'] : basename($this->path);
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