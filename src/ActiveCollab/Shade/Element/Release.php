<?php

  namespace ActiveCollab\Shade\Element;

  use ActiveCollab\Shade\Project;

  /**
   * Release element (for release notes)
   *
   * @package ActiveCollab\Shade\Element
   */
  class Release extends Element
  {
    /**
     * Application version number
     *
     * @var string
     */
    private $version_number;

    /**
     * Construct and load help element
     *
     * @param Project $project
     * @param string  $version_number
     * @param string  $path
     * @param bool    $load
     */
    public function __construct(Project $project, $version_number, $path, $load = true)
    {
      $this->version_number = $version_number;

      parent::__construct($project, $path, $load);
    }

    /**
     * @return string
     */
    function getTitle()
    {
      return $this->getVersionNumber();
    }

    /**
     * Return in which version change was introduced
     *
     * @return string
     */
    public function getVersionNumber()
    {
      return $this->version_number;
    }

    /**
     * Return page slug
     *
     * @return string
     */
    public function getSlug()
    {
      return str_replace('.', '-', $this->version_number);
    }
  }