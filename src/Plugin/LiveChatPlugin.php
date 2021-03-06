<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Plugin;

/**
 * Insert Google Analytics tracking code.
 *
 * @package ActiveCollab\Shade\Shade\Plugin
 */
class LiveChatPlugin extends Plugin
{
    /**
     * @return bool|string
     */
    function isEnabled()
    {
        if ($google_tag_manager_id = $this->getAccountId()) {
            return 'Yes, Account ID: ' . $google_tag_manager_id;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    private function getAccountId()
    {
        return $this->project->getConfigurationOption('live_chat_id');
    }

    /**
     * Returns after #footer.
     *
     * @return string
     */
    function renderFoot()
    {
        if ($live_chat_id = $this->getAccountId()) {
            return <<<EOS
<script type="text/javascript">
var __lc = {};
__lc.license = {$live_chat_id};

(function() {
var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
})();
</script>
EOS;
        } else {
            return '';
        }
    }
}
