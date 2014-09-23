<?php

  namespace ActiveCollab\Shade\VideoPlayer;

  use ActiveCollab\Shade\Element\Video;

  /**
   * Wistia video player
   *
   * @package ActiveCollab\Shade\VideoPlayer
   * @see http://wistia.com/
   */
  class WistiaVideoPlayer extends VideoPlayer
  {
    /**
     * @param Video $video
     * @return string
     */
    function renderPlayer(Video $video)
    {
      return 'WISTIA HERE!';
    }
  }