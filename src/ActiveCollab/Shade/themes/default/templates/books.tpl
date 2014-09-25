<{include "header.tpl"}>

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