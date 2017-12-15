<?php

namespace ActiveCollab\Shade\VideoPlayer;

use ActiveCollab\Shade\Project, ActiveCollab\Shade\Element\Video;

/**
 * Abstract video player renderer
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
     * @param Video $video
     * @return string
     */
    abstract function renderPlayer(Video $video);
}