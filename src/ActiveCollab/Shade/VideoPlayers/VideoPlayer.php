<?php

  namespace ActiveCollab\Shade\VideoPlayer;

  use ActiveCollab\Shade\Element\Video;

  /**
   * Abstract video player renderer
   *
   * @package ActiveCollab\Shade
   */
  abstract class VideoPlayer
  {
    /**
     * @param Video $video
     * @return string
     */
    abstract function renderPlayer(Video $video);
  }