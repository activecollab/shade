<!DOCTYPE HTML>
<html lang="en">
<head>
  <title>activeCollab Help Center</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="../assets/stylesheets/main.css" />
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
<div id="wrapper">
  <div id="header">
    <div class="illustration">
      <a href="../index.html"><img src="../assets/images/header_illustration.png" alt="Illustration" /></a>
    </div>

    <h1>User Manuals &amp; Guides</h1>

    <div class="search_help">
      <form action="https://www.google.com/search" method="get" target="_blank">
        <input type="hidden" value="activecollab.com/help" name="sitesearch">
        <input type="text" placeholder="Search Help for Answers" name="q">
      </form>
    </div>
  </div>

  <div id="content">
    <div id="help_books">
      <ul>
      <{foreach $books as $book}>
        <li>
          <a href="<{$book->getShortName()}>/index.html">
            <span class="book_cover"><img src="../assets/images/books/<{$book->getShortName()}>/_cover_dot.png"></span>
            <span class="book_name"><{$book->getTitle()}></span>
            <span class="book_description"><{$book->getDescription()}></span>
          </a>
        </li>
      <{/foreach}>
      </ul>
    </div>
  </div>

  <{include "footer.tpl"}>
</div>
</body>
</html>