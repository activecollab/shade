<?php

  namespace ActiveCollab\Shade\Plugin;

  use ActiveCollab\Shade\Project;

  /**
   * Abstract plugin
   *
   * @package ActiveCollab\Shade\Plugin
   */
  abstract class Plugin
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
     * Returns in <head> tag
     *
     * @return string
     */
    function renderHead()
    {

    }

    /**
     * Render footer (after #footer and before <body> is closed)
     *
     * @return string
     */
    function renderFoot()
    {

    }
  }