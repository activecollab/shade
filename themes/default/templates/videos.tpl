<!DOCTYPE HTML>
<html lang="<{$project->getShortLocale()}>">
<head>
  <title>activeCollab Help Center</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="../assets/stylesheets/main.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
</head>

<body>
<div id="wrapper_videos">
  <div id="header_videos">
    <div class="navigation">
      <ul>
        <li><a href="./../index.html">Home</a></li>
        <li><a href="./../whats-new/index.html">What's New?</a></li>
        <li><a href="./../books/index.html">Books</a></li>
        <li class="active"><a href="./../videos/index.html">Videos</a></li>
      </ul>
    </div>

    <div id="wrap_help_video_player">
      <div id="help_video_player">
        <{if $current_video}>
          <{$video_player->renderPlayer($current_video) nofilter}>
        <{else}>
          <div class="illustration"><img src="../assets/images/illustration-videos.png" alt="Video Illustration" /></div>
          <h1>Welcome to activeCollab Video tutorials</h1>
        <{/if}>
      </div>
    </div>
    <div class="header_space"></div>
  </div>

  <div id="content">
    <div id="help_video_groups">
      <{foreach $video_groups as $video_group => $video_group_caption}>
        <div class="help_video_group<{if $video_group_caption@last}> last<{/if}>">
          <h3><{$video_group_caption}></h3>
          <ul>
          <{foreach $videos as $video}>
            <{if $video->getGroupName() == $video_group}>
              <li class="<{if $current_video && $current_video->getShortName() == $video->getShortName()}>playing<{/if}>"><a href="<{$video->getSlug()}>.html"><{$video->getTitle()}></a></li>
            <{/if}>
          <{/foreach}>
          </ul>
        </div>
      <{/foreach}>
      <div class="clear"></div>
    </div>
  </div>

  <{include "footer.tpl"}>
</div>
</body>
</html>