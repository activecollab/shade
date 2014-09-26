<{include "header.tpl"}>

<div id="content_wrapper" class="container">
  <div id="content" class="text-center">
    <{if $current_video}>
      <div id="video_player"><{$video_player->renderPlayer($current_video) nofilter}></div>
    <{/if}>

    <{foreach $video_groups as $video_group => $video_group_caption}>
    <div class="video_group text-left">
      <h3><{$video_group_caption}></h3>
      <ul>
      <{foreach $videos as $video}>
        <{if $video->getGroupName() == $video_group}>
        <li class="<{if $current_video && $current_video->getShortName() == $video->getShortName()}>playing<{/if}>"><a href="<{$video->getSlug()}>.html"><{$video->getTitle()}> [<{$video->getPlayTime()}>]</a> &mdash; <{$video->getDescription()}></li>
        <{/if}>
      <{/foreach}>
      </ul>
    </div>
    <{/foreach}>
  </div>
</div>
</div>

<{include "footer.tpl"}>