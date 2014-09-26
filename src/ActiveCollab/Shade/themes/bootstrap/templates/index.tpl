<{include "header.tpl"}>

<div id="content_wrapper" class="container">
  <div id="sidebar">
    <p>Shade builds help portals from static files.</p>

    <p class="text-center" style="margin: 25px 0">
      <button type="button" class="btn btn-success btn-lg">Download v1.0.0</button>
    </p>

    <p>Common Questions:</p>
    <ul>
    <{foreach $common_questions as $common_question}>
      <li><a href="books/<{$common_question.book}>/<{$common_question.page}>.html"><{$common_question.question}></a></li>
    <{/foreach}>
    </ul>
  </div>

  <div id="content">
    <h1><{$project->getName()}></h1>
    <{$project->renderBody() nofilter}>
  </div>
</div>

<{include "footer.tpl"}>