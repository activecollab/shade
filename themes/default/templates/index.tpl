<!DOCTYPE HTML>
<html lang="en">
<head>
  <{assign var="page_level" value=0}>

  <title>activeCollab Help Center</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="assets/stylesheets/main.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
</head>

<body>
<div id="wrapper">
  <div id="header">
    <div class="illustration">
      <a href="./index.html"><img src="assets/images/header_illustration.png" alt="Illustration" /></a>
    </div>

    <h1>Welcome to activeCollab Help Center</h1>

    <div class="search_help">
      <form action="https://www.google.com/search" method="get" target="_blank">
        <input type="hidden" value="activecollab.com/help" name="sitesearch">
        <input type="text" placeholder="Search Help for Answers" name="q">
      </form>
    </div>
  </div>

  <div id="search_results" style="display: none;">
    <div id="no_results">
      <span class="no_results_image"><img src="assets/images/circle_warning.png" alt="" /></span>
      <span class="no_results_title">Sorry, no results for:</span>
      <span class="no_results_query">Setting for the remote update of the server</span>
    </div>

    <div id="returned_results">
      <h3>Search returned <b>6</b> results:</h3>
      <ol>
        <li><a href="#">How do I add new people to my activeCollab account?</a></li>
        <li><a href="#">Is there a way i can see what are my employees working on?</a></li>
        <li><a href="#">What is fastest way to add many tasks to project?</a></li>
        <li><a href="#">Will my password expire?</a></li>
        <li><a href="#">Will i be able to replace user on a project with another one?</a></li>
      </ol>
    </div>
  </div>

  <div id="content">
    <div id="help_shortcuts">
      <ul>
        <li>
          <a href="./whats-new/index.html">
            <span class="help_shortcut_title">What's New?</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-bell.png" alt="" /></span>
            <span class="help_shortcut_details">Get up to date about new features<br>and modules</span>
          </a>
        </li>
        <li>
          <a href="./books/index.html">
            <span class="help_shortcut_title">User Manuals &amp; Guides</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-manuals.png" alt="" /></span>
            <span class="help_shortcut_details">A-Z manuals which will let you know<br>activeCollab in depth</span>
          </a>
        </li>
        <li>
          <a href="./videos/index.html">
            <span class="help_shortcut_title">Instructional Videos</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-videos.png" alt="" /></span>
            <span class="help_shortcut_details">See great tutorials and cheat sheets to<br>optimize your experience with aC</span>
          </a>
        </li>
      </ul>
    </div>

    <div id="help_common_questions">
      <h3>Commonly Asked Questions</h3>

      <ul>
      <{foreach $common_questions as $common_question}>
        <li><a href="<{$common_question.page_url}>"><{$common_question.question}></a></li>
      <{/foreach}>
      </ul>
    </div>

    <div id="help_contact">
      <ul>
        <li>
          <a href="https://www.activecollab.com/contact.html">
            <span class="help_shortcut_title">Report a Bug</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-bug.png" alt="" /></span>
            <span class="help_shortcut_details">System gives you a hard time :(<br>Please let us know and we'll help ASAP.</span>
          </a>
        </li>
        <li>
          <a href="https://www.activecollab.com/contact.html">
            <span class="help_shortcut_title">Suggest a Feature</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-bulb.png" alt="" /></span>
            <span class="help_shortcut_details">Have an idea for a killer feature that would save you a lot of time?</span>
          </a>
        </li>
        <li>
          <a href="https://www.activecollab.com/contact.html">
            <span class="help_shortcut_title">Praise activeCollab</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-heart.png" alt="" /></span>
            <span class="help_shortcut_details">Get in touch<br>if you (HEART) activeCollab.</span>
          </a>
        </li>
        <li>
          <a href="https://www.activecollab.com/contact.html">
            <span class="help_shortcut_title">Ask a Question</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-mail.png" alt="" /></span>
            <span class="help_shortcut_details">Have a question?<br>Our support team is here to help.</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <{include "footer.tpl"}>
</div>
</body>
</html>