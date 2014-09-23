<!DOCTYPE HTML>
<html lang="en">
<head>
  <title>activeCollab Help Center</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="../../assets/stylesheets/main.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
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
<div id="wrapper_pages">
  <div id="header_pages">
    <div class="logo">
      <a href="./../../index.html">activeCollab Help Center</a>
      <h1>activeCollab Help Center</h1>
    </div>
    <div class="navigation">
      <ul>
        <li><a href="./../../index.html">Home</a></li>
        <li><a href="./../../whats-new/index.html">What's New?</a></li>
        <li class="active"><a href="./../../books/index.html">Books</a></li>
        <li><a href="./../../videos/index.html">Videos</a></li>
      </ul>
    </div>
    <div class="header_space"></div>
  </div>

  <div id="content">
    <{include "book_sidebar.tpl"}>

    <div id="help_book_pages">
      <div class="help_book_page">
        <h1><{$current_page->getTitle()}></h1>
        <div class="help_book_page_content"><{$current_page->renderBody() nofilter}></div>
        <div class="help_book_footer">
          <div class="help_book_footer_inner">
            <div class="help_book_footer_prev"><a href="#">&laquo; Prev</a></div>
            <div class="help_book_footer_top"><a href="#" onclick="window.scrollTo(0, 0); return false;">Back to the Top</a></div>
            <div class="help_book_footer_next"><a href="#">Next &raquo;</a></div>
          </div>
        </div>
      </div>
    </div>
    <div class="clear"></div>
  </div>

  <{include "footer.tpl"}>
</div>
</body>
</html>