<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Plugin;

use ActiveCollab\Shade\Element\Element;
use ActiveCollab\Shade\ProjectInterface;

/**
 * Abstract plugin.
 *
 * @package ActiveCollab\Shade\Shade\Plugin
 */
abstract class Plugin implements PluginInterface
{
    protected $project;

    function __construct(ProjectInterface &$project)
    {
        $this->project = $project;
    }

    /**
     * Return a value that indicates that this plugin is enabled (account ID, true etc).
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
     * Returns in <head> tag.
     *
     * @return string
     */
    function renderHead()
    {
        return '';
    }

    /**
     * Render after <body> tag is open.
     */
    function renderBody()
    {
        return '';
    }

    /**
     * Render in comments section.
     *
     * @param  Element $element
     * @return string
     */
    function renderComments(Element $element)
    {
        return '';
    }

    /**
     * Render footer (after #footer and before <body> is closed).
     *
     * @return string
     */
    function renderFoot()
    {
        return '';
    }
}
