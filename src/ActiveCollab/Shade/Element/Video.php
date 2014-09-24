<?php

  namespace ActiveCollab\Shade\Element;

  use ActiveCollab\Shade;

  /**
   * Framework level help video class
   *
   * @package Shade
   */
  class Video extends Element
  {
    const GETTING_STARTED = 'getting-started';

    /**
     * Return book's short name
     *
     * @return string
     */
    public function getShortName()
    {
      return $this->getSlug();
    }

    /**
     * Cached group name value
     *
     * @var string
     */
    private $group_name = false;

    /**
     * Return name of the group that this video belongs to
     *
     * @return string
     */
    public function getGroupName()
    {
      if ($this->group_name === false) {
        $this->group_name = $this->getProperty('group');

        if (empty($this->group_name)) {
          $this->group_name = self::GETTING_STARTED;
        }
      }

      return $this->group_name;
    }

    /**
     * Cached title
     *
     * @var string
     */
    protected $title;

    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
      if ($this->title === null) {
        $title = $this->getProperty('title');

        if (empty($title)) {
          $basename = basename($this->path);

          $first_dot = strpos($basename, '.');
          $second_dot = strpos($basename, '.', $first_dot + 1);

          $this->title = trim(substr($basename, $first_dot + 1, $second_dot - $first_dot - 1));
        } else {
          $this->title = $title;
        }
      }

      return $this->title;
    }

    /**
     * Cached slug value
     *
     * @var string
     */
    protected $slug;

    /**
     * Return page slug
     *
     * @return string
     */
    public function getSlug()
    {
      if ($this->slug === null) {
        $slug = $this->getProperty('slug');

        if (empty($slug)) {
          $this->slug = Shade::slug($this->getTitle());
        } else {
          $this->slug = $slug;
        }
      }

      return $this->slug;
    }

    /**
     * Return property description
     *
     * @return string
     */
    public function getDescription()
    {
      return $this->getProperty('description');
    }

    /**
     * Return source URL
     *
     * Only supported modifier at the moment is 2X
     *
     * @param  string $modifier
     * @return string
     */
    public function getSourceUrl($modifier = null)
    {
      if (empty($modifier)) {
        return $this->getProperty('url');
      } else {
        return $this->getProperty('url' . strtolower($modifier));
      }
    }

    /**
     * Cached play time value
     *
     * @var string
     */
    private $play_time = false;

    /**
     * Return video play time
     *
     * @return string
     */
    public function getPlayTime()
    {
      if ($this->play_time === false) {
        $this->play_time = $this->getProperty('play_time');

        if (empty($this->play_time)) {
          $this->play_time = '-:--';
        }
      }

      return $this->play_time;
    }
  }
