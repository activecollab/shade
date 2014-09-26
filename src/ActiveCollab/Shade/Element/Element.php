<?php

  namespace ActiveCollab\Shade\Element;

  use ActiveCollab\Shade, ActiveCollab\Shade\Project, ActiveCollab\Shade\ElementFileParser;

  /**
   * Framework level help element implementation
   *
   * @package Shade
   */
  abstract class Element
  {
    use ElementFileParser;

    /**
     * @var Project
     */
    private $project;

    /**
     * Construct and load help element
     *
     * @param Project $project
     * @param string  $path
     * @param bool    $load
     */
    public function __construct(Project $project, $path, $load = true)
    {
      $this->project = $project;
      $this->path = $path;

      if ($load) {
        $this->load();
      }
    }

    /**
     * @var string
     */
    protected $path;

    /**
     * @return string
     */
    public function getPath()
    {
      return $this->path;
    }

    /**
     * Get folder name
     *
     * @return string
     */
    public function getFolderName()
    {
      return basename($this->path);
    }

    /**
     * @return Project
     */
    public function &getProject()
    {
      return $this->project;
    }

    /**
     * Book's short name
     *
     * @var string
     */
    protected $short_name;

    /**
     * Return book's short name
     *
     * @return string
     */
    public function getShortName()
    {
      if ($this->short_name === null) {
        $this->short_name = str_replace('_', '-', basename($this->path));
      }

      return $this->short_name;
    }

    /**
     * Return element title
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Return page level in the build structure
     *
     * @return int
     */
    public function getPageLevel()
    {
      return 1;
    }
  }
