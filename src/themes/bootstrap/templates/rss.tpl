<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel>
    <title>activeCollab Help</title>
    <link>https://activecollab.com/help/whats-new</link>
    <description>What\'s New Articles</description>
    <pubDate><{'D, d M Y H:i:s O'|date}></pubDate>'
   </channel>
<{foreach $whats_new_articles_by_version as $v => $articles}>
  <{foreach $articles as $article}>
  <item>
    <title><{$article->getTitle()}></title>
    <link>https://activecollab.com/help/whats-new/<{$article->getShortName()}>.html</link>
    <guid>https://activecollab.com/help/whats-new/<{$article->getShortName()}>.html</guid>
    <description><![CDATA[<{$article->renderBody() nofilter}>]]></description>
  </item>
  <{/foreach}>
<{/foreach}>
</rss>