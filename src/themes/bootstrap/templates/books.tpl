<{include "header.tpl"}>

<div id="content_wrapper" class="container">
  <div id="content">
    <h1>User Manuals &amp; Guides</h1>

    <p>Following manuals are available:</p>
    <ul>
      <{foreach $books as $book}>
      <li>
        <a href="<{$book->getShortName()}>/index.html"><{$book->getTitle()}></a> &mdash; <span class="book_description"><{$book->getDescription()}></span>
      </li>
      <{/foreach}>
    </ul>
  </div>
</div>

<{include "footer.tpl"}>