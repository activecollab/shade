<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\VideoPlayer;

use ActiveCollab\Shade\Element\Video, ActiveCollab\Shade\Project;

/**
 * Abstract video player renderer.
 *
 * @package ActiveCollab\Shade
 */
abstract class VideoPlayer
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
     * @param  Video  $video
     * @return string
     */
    abstract function renderPlayer(Video $video);
}
