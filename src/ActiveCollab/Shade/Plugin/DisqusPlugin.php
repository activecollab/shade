<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Plugin;

use ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\Element, ActiveCollab\Shade\Element\Release, ActiveCollab\Shade\Element\WhatsNewArticle;

/**
 * Add Disqus comments to the generated pages.
 *
 * @package ActiveCollab\Shade\Plugin
 */
class DisqusPlugin extends Plugin
{
    /**
     * @return bool|string
     */
    function isEnabled()
    {
        list($id, $url, $item_id) = $this->getSettings();

        if ($id && $url) {
            return 'Yes, Account ID: ' . $id;
        } else {
            return false;
        }
    }

    /**
     * Return Disqus settings.
     *
     * @return array
     */
    private function getSettings()
    {
        $identifier_prefix = $this->project->getConfigurationOption('disqus_identifier_prefix');

        if (empty($identifier_prefix)) {
            $identifier_prefix = '_shade';
        }

        return [$this->project->getConfigurationOption('disqus_account_id'), $this->project->getConfigurationOption('disqus_url_prefix'), $identifier_prefix];
    }

    /**
     * Returns in <help_book_page_comments> tag.
     *
     * @param  Element $element
     * @return string
     */
    function renderComments(Element $element)
    {
        list($disqus_account_id, $disqus_url_prefix, $disqus_identifier_prefix) = $this->getSettings();

        if ($disqus_account_id && $disqus_url_prefix) {
            if ($element instanceof BookPage) {
                $url = $disqus_url_prefix . '/books/' . $element->getBookName() . '/' . $element->getSlug() . '.html';
                $identifier = $disqus_identifier_prefix . '/books/' . $element->getBookName() . '/' . $element->getSlug();
            } elseif ($element instanceof WhatsNewArticle) {
                $url = $disqus_url_prefix . '/whats-new/' . $element->getShortName() . '.html';
                $identifier = $disqus_identifier_prefix . '/whats-new/' . $element->getShortName();
            } else {
                return '';
            }

            $title = var_export($element->getTitle(), true);

            return <<<EOS
<div id="disqus_thread"></div>
<script type="text/javascript">
var disqus_shortname = "$disqus_account_id";
var disqus_identifier = "$identifier";
var disqus_title = $title;
var disqus_url = "$url";

(function() {
var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
dsq.src = "//" + disqus_shortname + ".disqus.com/embed.js";
(document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
})();
</script>
EOS;
        } else {
            return '';
        }
    }
}
