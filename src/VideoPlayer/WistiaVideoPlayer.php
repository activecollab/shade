<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\VideoPlayer;

use ActiveCollab\Shade\Element\Video;

/**
 * Wistia video player.
 *
 * @package ActiveCollab\Shade\Shade\VideoPlayer
 * @see http://wistia.com/
 */
class WistiaVideoPlayer extends VideoPlayer
{
    /**
     * @param  Video  $video
     * @return string
     */
    function renderPlayer(Video $video)
    {
        return '<div id="wistia_' . $video->getProperty('wistia_code') . '" class="wistia_embed" style="width:640px;height:360px;"> </div>
    <script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js"></script>
    <script>
      wistiaEmbed = Wistia.embed("' . $video->getProperty('wistia_code') . '", {
        videoFoam: true
      });

      if (location.href.indexOf("index.html") === -1) {
        wistiaEmbed.play();
      }
    </script>';
    }
}
