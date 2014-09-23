<div id="sidebar">
<{foreach $whats_new_articles_by_version as $version => $articles}>
  <p><{$version}></p>
  <ol>
  <{foreach $articles as $article}>
    <li class="<{if $current_whats_new_article && $current_whats_new_article->getShortName() == $article->getShortName()}>selected<{/if}>"><a href="<{$article->getShortName()}>.html"><{$article->getTitle()}></a></li>
  <{/foreach}>
  </ol>
<{/foreach}>

  <div id="release_notes">
    <a href="./../release-notes/index.html">Release Notes</a>
  </div>
</div>