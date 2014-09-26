<div id="sidebar">
  <div class="book-icon text-center">
    <a href="index.html"><img src="../../assets/images/books/<{$current_book->getShortName()}>/_cover_small.png"></a>
  </div>

  <p>Table of Contents:</p>
  <ol>
<{foreach $pages as $page}>
  <li class="<{if $current_page && $current_page->getShortName() == $page->getShortName()}>selected<{/if}>"><a href="<{$page->getShortName()}>.html"><{$page->getTitle()}></a></li>
<{/foreach}>
  </ol>
</div>