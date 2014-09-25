<?php

  namespace ActiveCollab\Shade\Plugin;

  use ActiveCollab\Shade\Project, ActiveCollab\Shade\Element\Element;

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
     * Return a value that indicates that this plugin is enabled (account ID, true etc)
     */
    function isEnabled()
    {
      return false;
    }

    /**
     * @return string
     */
    function getName()
    {
      return array_pop(explode('\\', get_class($this)));
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
     * Render after <body> tag is open
     */
    function renderBody()
    {
    }

    /**
     * Render in comments section
     *
     * @param Element $element
     */
    function renderComments(Element $element)
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