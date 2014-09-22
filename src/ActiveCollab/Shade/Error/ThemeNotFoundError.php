<?php

  namespace ActiveCollab\Shade\Error;

  /**
   * Exception that is thrown when we fail to parse JSON
   *
   * @package ActiveCollab\Shade\Error
   */
  class ThemeNotFoundError extends Error
  {
    /**
     * @param string $name
     * @param string $expected_location
     * @param string|null $message
     */
    function __construct($name, $expected_location, $message = null)
    {
      if (empty($message)) {
        $message = "Theme '$name' was not found at '$expected_location'";
      }

      parent::__construct($message);
    }
  }