<div id="sidebar">
<{foreach $whats_new_articles_by_version as $version => $articles}>
  <h3>New in <{$version}></h3>
  <ol>
  <{foreach $articles as $article}>
    <li class="<{if $current_whats_new_article && $current_whats_new_article->getShortName() == $article->getShortName()}>selected<{/if}>"><a href="<{$article->getShortName()}>.html"><{$article->getTitle()}></a></li>
  <{/foreach}>
  </ol>
<{/foreach}>

  <div class="text-center">
    <a href="./../release-notes/index.html">Release Notes</a>
  </div>
</div>