<?php

  namespace ActiveCollab\Shade\Plugin;

  /**
   * Insert Google Analytics tracking code
   *
   * @package ActiveCollab\Shade\Plugin
   */
  class LiveChatPlugin extends Plugin
  {
    /**
     * Returns after #footer
     *
     * @return string
     */
    function renderFoot()
    {
      if ($live_chat_id = $this->project->getConfigurationOption('live_chat_id')) {
        return "<script type=\"text/javascript\">
          var __lc = {};
          __lc.license = " . $live_chat_id . ";

          (function() {
            var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
            lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
          })();
        </script>";
      } else {
        return '';
      }
    }
  }