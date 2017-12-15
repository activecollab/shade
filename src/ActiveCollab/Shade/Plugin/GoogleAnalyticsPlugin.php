<?php

namespace ActiveCollab\Shade\Plugin;

/**
 * Insert Google Analytics tracking code
 *
 * @package ActiveCollab\Shade\Plugin
 */
class GoogleAnalyticsPlugin extends Plugin
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
        return $this->project->getConfigurationOption('google_analytics_account_id');
    }

    /**
     * Returns in <head> tag
     *
     * @return string
     */
    function renderHead()
    {
        if ($google_analytics_account_id = $this->getAccountId()) {
            return "<script type=\"text/javascript\">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', '" . $google_analytics_account_id . "']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
      })();
    </script>";
        } else {
            return '';
        }
    }
}