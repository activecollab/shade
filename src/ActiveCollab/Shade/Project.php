<?php

  namespace ActiveCollab\Shade;

  use ActiveCollab\Shade\Error\ParseJsonError;

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
     * Configuration data
     *
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
     * @return Story[]
     */
    function getStories() {
      $result = [];

      if(is_dir("$this->path/stories")) {
        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("$this->path/stories")) as $file) {

          /**
           * @var \DirectoryIterator $file
           */
          if(substr($file->getBasename(), 0, 1) != '.' && $file->getExtension() == 'narr') {
            $result[] = new Story($file->getPathname());
          } // if
        } // foreach
      }

      return $result;
    }

    /**
     * Return story by name
     *
     * @param string $name
     * @return Story|null
     */
    function getStory($name) {
      foreach($this->getStories() as $story) {
        if($story->getName() === $name) {
          return $story;
        }
      }

      return null;
    }

    /**
     * Return true if this is a valid project
     *
     * @return bool
     */
    function isValid() {
      return is_dir($this->path) && is_file($this->path . '/project.json');
    }
  }