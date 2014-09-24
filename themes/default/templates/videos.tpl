<!DOCTYPE HTML>
<html lang="en">
<head>
  <title>activeCollab Help Center</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="../assets/stylesheets/main.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
  <script type="text/javascript" src="../assets/javascript/jwplayer/jwplayer.js" ></script>
  <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-66802-3']);
    _gaq.push(['_trackPageview']);

    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(ga, s);
    })();
  </script>
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
        <div class="illustration"><img src="../assets/images/illustration-videos.png" alt="Video Illustration" /></div>
        <h1>Welcome to activeCollab Video tutorials</h1>
      </div>
    </div>
    <div class="header_space"></div>
  </div>

  <div id="content">
    <div id="help_video_groups">
      <{foreach $video_groups as $video_group => $video_group_caption}>
        <div class="help_video_group<{if $video_group_caption@last}> last<{/if}>">
          <h3><{$video_group_caption}></h3>
          <div class="help_video_icon"><img src="../assets/images/circle-starting.png"></div>
          <ul>
          <{foreach $videos as $video}>
            <{if $video->getGroupName() == $video_group}>
              <li data-source-url="<{$video->getSourceUrl()}>" data-source-high-res-url="<{$video->getSourceUrl('2X')}>" data-slug="<{$video->getSlug()}>"><{$video->getTitle()}></li>
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

<script type="text/javascript">
  $('#wrapper_videos').each(function() {
    var wrapper = $(this);

    var player = false;
    var player_url = '../assets/flash/jwplayer/player.swf';

    wrapper.on('click', '#content .help_video_group ul li', function() {
      var list_item = $(this);

      if(list_item.is('.playing')) {
        if(list_item.is('.paused')) {
          list_item.removeClass('paused');
          jwplayer('help_video_player').play();
        } else {
          list_item.addClass('paused');
          jwplayer('help_video_player').pause();
        } // if

        return;
      } // if

      wrapper.find('#content .help_video_group ul li').removeClass('playing');

      list_item.addClass('playing');

      if(player === false) {
        player = wrapper.find('#help_video_player').height('400px').width('600px');
      } // if

      var player_settings = {
        'file' : list_item.data('sourceUrl'),
        'flashplayer' : player_url,
        'height' : 360,
        'width' : 640,
        'events' : {
          'onReady' : function() {
            this.play();
          }
        }
      };

      var source_url = list_item.data('sourceUrl');

      var source_high_res_url = list_item.data('sourceHighResUrl');

      if(source_high_res_url) {
        player_settings['levels'] = [
          { bitrate: 500, file: source_url, width: 360 },
          { bitrate: 2000, file: source_high_res_url, width: 720 }
        ];
      } else {
        player_settings['file'] = source_url;
      } // if

      player_settings['file'] = source_url;

      jwplayer('help_video_player').setup(player_settings);

      window.location.hash = list_item.data('slug');
    });

    var url_hash = window.location.hash;

    var selected_video = wrapper.find('li[data-slug="'+url_hash.replace('#', '')+'"]');
    if(selected_video.length > 0) {
      selected_video.click();
    } // if

    // when we click the image placeholder, play the first item in the list
    $('#help_video_player').click(function () {
      $('#help_video_groups .help_video_group:first ul:first li:first').click();
      return false;
    });
  });
</script>
</body>
</html>