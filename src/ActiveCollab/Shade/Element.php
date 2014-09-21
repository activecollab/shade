<?php

  namespace Shade;

  /**
   * Framework level help element implementation
   *
   * @package Shade
   */
  abstract class Element
  {
    /**
     * Properties separator
     */
    const PROPERTIES_SEPARATOR = '================================================================';

    /**
     * Name of the module or framework that this book belongs to
     *
     * @var string
     */
    protected $module;

    /**
     * Book's path
     *
     * @var string
     */
    protected $path;

    /**
     * Load indicator
     *
     * @var bool
     */
    protected $is_loaded = false;

    /**
     * Book's short name
     *
     * @var string
     */
    protected $short_name;

    /**
     * List of properties
     *
     * @var string
     */
    protected $properties = [];

    /**
     * Body text
     *
     * @var string
     */
    protected $body;

    /**
     * Construct and load help element
     *
     * @param string $module
     * @param string $path
     * @param bool   $load
     */
    public function __construct($module, $path, $load = true)
    {
      $this->module = $module;
      $this->path = $path;

      if ($load) {
        $this->load();
      }
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
     * Return module name
     *
     * @return string
     */
    public function getModuleName()
    {
      return $this->module;
    }

    /**
     * Return true if $user can view this element
     *
     * @param  User $user
     * @return bool
     */
    public function canView(User $user)
    {
      $groups = AngieApplication::help()->getUserGroups($user);

      $show_to = $this->getProperty('show_to');

      if ($show_to) {
        $show_to_groups = array_map('trim', explode(',', $show_to));

        foreach ($show_to_groups as $show_to_group) {
          if (in_array($show_to_group, $groups)) {
            return true;
          }
        }

        return false; // Not visible to any of the Show To groups
      }

      $hide_from = $this->getProperty('hide_from');

      if ($hide_from) {
        $hide_from_groups = array_map('trim', explode(',', $hide_from));

        foreach ($hide_from_groups as $hide_from_group) {
          if (in_array($hide_from_group, $groups)) {
            return false;
          }
        }
      }

      return true;
    }

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
     * Return property value
     *
     * @param  string $name
     * @param  mixed  $default
     * @return string
     */
    public function getProperty($name, $default = null)
    {
      return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }

    /**
     * Return element title
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Return element body
     *
     * @return string
     */
    public function getBody()
    {
      return $this->body;
    }

    /**
     * Return book URL
     *
     * @return string
     */
    public function getUrl()
    {
      return AngieApplication::help()->getUrl($this);
    }

    /**
     * Return true if we loaded element's definition
     *
     * @return bool
     */
    public function isLoaded()
    {
      return $this->is_loaded;
    }

    /**
     * Get index file path
     *
     * @return string
     */
    public function getIndexFilePath()
    {
      return is_dir($this->path) ? $this->path . '/index.md' : $this->path;
    }

    /**
     * Load element's definition
     *
     * @throws FileDnxError
     */
    public function load()
    {
      if (empty($this->is_loaded)) {
        $index_file = $this->getIndexFilePath();

        if (is_file($index_file)) {
          $this->body = file_get_contents($index_file);

          $separator_pos = strpos($this->body, self::PROPERTIES_SEPARATOR);

          if ($separator_pos === false) {
            if (substr($this->body, 0, 1) == '*') {
              $properties_string = $this->body;
              $this->body = '';
            } else {
              $properties_string = '';
            }
          } else {
            $properties_string = trim(substr($this->body, 0, $separator_pos));
            $this->body = trim(substr($this->body, $separator_pos + strlen(self::PROPERTIES_SEPARATOR)));
          }

          if ($properties_string) {
            $properties_lines = explode("\n", $properties_string);

            if (count($properties_lines)) {
              foreach ($properties_lines as $properties_line) {
                $properties_line = trim(trim($properties_line, '*')); // Clean up

                if ($properties_line) {
                  $colon_pos = strpos($properties_line, ':');

                  if ($colon_pos !== false) {
                    $this->loadProperty(trim(substr($properties_line, 0, $colon_pos)), trim(substr($properties_line, $colon_pos + 1)));
                  }
                }
              }
            }
          }

          $this->body = trim($this->body);
        } else {
          throw new FileDnxError($index_file);
        }

        $this->is_loaded = true;
      }
    }

    /**
     * Load property value
     *
     * @param string $name
     * @param string $value
     */
    private function loadProperty($name, $value)
    {
      $this->properties[Angie\Inflector::underscore(str_replace(' ', '', $name))] = $value;
    }

    // ---------------------------------------------------
    //  Interfaces
    // ---------------------------------------------------

    /**
     * Return object ID
     *
     * @return int|string
     */
    public function getId()
    {
      return $this->getShortName();
    }

    /**
     * Describe parent object to be used in search result
     *
     * @return array
     */
    public function searchSerialize()
    {
      if ($this instanceof Book) {
        $verbose_type = lang('Book');
      } elseif ($this instanceof BookPage) {
        $verbose_type = lang('Page');
      } elseif ($this instanceof HelpWhatsNewArticle) {
        $verbose_type = lang('Article');
      } elseif ($this instanceof HelpVideo) {
        $verbose_type = lang('Video');
      } else {
        $verbose_type = lang('Help');
      }

      return [
        'id' => $this->getShortName(),
        'type' => get_class($this),
        'verbose_type' => $verbose_type,
        'name' => $this->getTitle(),
        'permalink' => $this->getUrl(),
        'is_crossed_over' => false,
      ];
    }

  }
