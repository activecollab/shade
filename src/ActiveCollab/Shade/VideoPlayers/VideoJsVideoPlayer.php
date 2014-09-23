<?php

  namespace ActiveCollab\Shade\VideoPlayer;

  use ActiveCollab\Shade\Element\Video;

  /**
   * Video.js video player
   *
   * @package ActiveCollab\Shade\VideoPlayer
   * @see http://www.videojs.com/
   */
  class VideoJsVideoPlayer extends VideoPlayer
  {
    /**
     * @param Video $video
     * @return string
     */
    function renderPlayer(Video $video)
    {
      return 'Video.JS HERE!';
    }
  }