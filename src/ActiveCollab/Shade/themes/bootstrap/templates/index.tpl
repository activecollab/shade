<{include "header.tpl"}>

<div id="page_content" class="container">

  <h1><{$project->getName()}></h1>

  <{$project->renderBody() nofilter}>

  <h2>FAQ</h2>

  <ul>
    <{foreach $common_questions as $common_question}>
    <li><a href="books/<{$common_question.book}>/<{$common_question.page}>.html"><{$common_question.question}></a></li>
    <{/foreach}>
  </ul>
</div>

<{include "footer.tpl"}>