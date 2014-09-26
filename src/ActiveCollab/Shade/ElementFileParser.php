<?php

  namespace ActiveCollab\Shade;

  use ActiveCollab\Shade, ActiveCollab\Shade\Error\ElementFileNotFoundError, Exception;

  /**
   * Parse element definition file
   *
   * @package ActiveCollab\Shade
   */
  trait ElementFileParser
  {
    /**
     * @var string
     */
    private $properties_separator = '================================================================';

    /**
     * Load indicator
     *
     * @var bool
     */
    protected $is_loaded = false;

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
     * Return element body
     *
     * @return string
     */
    public function getBody()
    {
      return $this->body;
    }

    /**
     * Render body of a given element
     *
     * @return string
     * @throws Exception
     */
    public function renderBody()
    {
      $smarty =& Shade::getSmarty();

      $template = $smarty->createTemplate($this->getIndexFilePath());

      SmartyHelpers::setCurrentElement($this);
      SmartyHelpers::setCurrentProject($this->getProject());

      $content = $template->fetch();

      SmartyHelpers::resetCurrentElementAndProject();

      $separator_pos = strpos($content, $this->properties_separator);

      if ($separator_pos === false) {
        if (substr($content, 0, 1) == '*') {
          $content = '*Content Not Provided*';
        }
      } else {
        $content = trim(substr($content, $separator_pos + strlen($this->properties_separator)));
      }

      return Shade::markdownToHtml($content);
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
     * Load element's definition
     *
     * @throws ElementFileNotFoundError
     */
    public function load()
    {
      if (empty($this->is_loaded)) {
        $index_file = $this->getIndexFilePath();

        if (is_file($index_file)) {
          $this->body = file_get_contents($index_file);

          $separator_pos = strpos($this->body, $this->properties_separator);

          if ($separator_pos === false) {
            if (substr($this->body, 0, 1) == '*') {
              $properties_string = $this->body;
              $this->body = '';
            } else {
              $properties_string = '';
            }
          } else {
            $properties_string = trim(substr($this->body, 0, $separator_pos));
            $this->body = trim(substr($this->body, $separator_pos + strlen($this->properties_separator)));
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
          throw new ElementFileNotFoundError($index_file);
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
      $this->properties[Shade::underscore(str_replace(' ', '', $name))] = $value;
    }

    /**
     * @var string
     */
    private $index_file_path;

    /**
     * Get index file path
     *
     * @return string
     */
    public function getIndexFilePath()
    {
      if (empty($this->index_file_path)) {
        $this->index_file_path = is_dir($this->getPath()) ? $this->getPath() . '/index.md' : $this->getPath();
      }

      return $this->index_file_path;
    }

    // ---------------------------------------------------
    //  Expectations
    // ---------------------------------------------------

    /**
     * @return \ActiveCollab\Shade\Project
     */
    abstract function &getProject();

    /**
     * @return string
     */
    abstract function getPath();
  }