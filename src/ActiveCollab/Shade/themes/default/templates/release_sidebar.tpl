<div id="sidebar">
  <{foreach $releases_by_major_version as $version => $releases}>
  <p class="release_notes_version">Version <{$version}></p>
  <ol class="release_notes">
    <{foreach $releases as $release}>
    <li class="<{if $current_release && $current_release->getVersionNumber() == $release->getVersionNumber()}>selected<{/if}>"><a href="<{$release->getSlug()}>.html"><{$release->getTitle()}></a></li>
    <{/foreach}>
  </ol>
  <{/foreach}>

  <div id="release_notes">
    <a href="./../whats-new/index.html">What's New</a>
  </div>
</div>