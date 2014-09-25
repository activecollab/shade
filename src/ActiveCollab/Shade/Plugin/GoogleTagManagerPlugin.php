<?php

  namespace ActiveCollab\Shade\Plugin;

  /**
   * Insert Google Tag Manager
   *
   * @package ActiveCollab\Shade\Plugin
   */
  class GoogleTagManagerPlugin extends Plugin
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
      return $this->project->getConfigurationOption('google_tag_manager_id');
    }

    /**
     * Returns in <head> tag
     *
     * @return string
     */
    function renderBody()
    {
      if ($google_tag_manager_id = $this->getAccountId()) {
        return <<<EOS
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id={$google_tag_manager_id}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$google_tag_manager_id}');</script>
<!-- End Google Tag Manager -->
EOS;
      } else {
        return '';
      }
    }
  }