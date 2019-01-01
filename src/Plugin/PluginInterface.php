<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Plugin;

use ActiveCollab\Shade\Element\Element;

interface PluginInterface
{
    /**
     * Return a value that indicates that this plugin is enabled (account ID, true etc).
     */
    function isEnabled();

    /**
     * @return string
     */
    function getName();

    /**
     * Returns in <head> tag.
     *
     * @return string
     */
    function renderHead();

    /**
     * Render after <body> tag is open.
     */
    function renderBody();

    /**
     * Render in comments section.
     *
     * @param Element $element
     */
    function renderComments(Element $element);

    /**
     * Render footer (after #footer and before <body> is closed).
     *
     * @return string
     */
    function renderFoot();
}
