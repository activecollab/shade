<div id="sidebar">
  <div id="help_book_cover">
    <a href="index.html"><img src="../../assets/images/books/<{$current_book->getShortName()}>/_cover_small.png"></a>
  </div>

  <ol>
<{foreach $pages as $page}>
  <li class="<{if $current_page && $current_page->getShortName() == $page->getShortName()}>selected<{/if}>"><a href="<{$page->getShortName()}>.html"><{$page->getTitle()}></a></li>
<{/foreach}>
  </ol>

  <div id="release_notes">
    <a href="./../release-notes/index.html">activeCollab Release Notes</a>
  </div>
</div>