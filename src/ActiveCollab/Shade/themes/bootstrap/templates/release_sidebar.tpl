<div id="sidebar" class="releases">
  <{foreach $releases_by_major_version as $version => $releases}>
  <h3>Version <{$version}></h3>
  <ol>
    <{foreach $releases as $release}>
    <li class="<{if $current_release && $current_release->getVersionNumber() == $release->getVersionNumber()}>selected<{/if}>"><a href="<{$release->getSlug()}>.html"><{$release->getTitle()}></a></li>
    <{/foreach}>
  </ol>
  <{/foreach}>

  <div class="text-center">
    <a href="./../whats-new/index.html">What's New</a>
  </div>
</div>