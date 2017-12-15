<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Error;

/**
 * Exception that is thrown when we fail to load an element definition file.
 *
 * @package ActiveCollab\Shade\Error
 */
class ElementFileNotFoundError extends Error
{
    /**
     * @param string      $expected_location
     * @param string|null $message
     */
    function __construct($expected_location, $message = null)
    {
        if (empty($message)) {
            $message = "File '$expected_location' was not found";
        }

        parent::__construct($message);
    }
}
