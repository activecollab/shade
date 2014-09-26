<{include "header.tpl"}>

<div id="content_wrapper" class="container">
  <{include "release_sidebar.tpl"}>

  <div id="content">
  <{if $current_release}>
    <article>
      <h1><abbr title="Version">v</abbr><{$current_release->getTitle()}></h1>
      <div class="body"><{$current_release->renderBody() nofilter}></div>
      <{include "prev_top_next.tpl"}>
    </article>
  </div>
  <{else}>
    <h1>Release Not Found</h1>
  <{/if}>
  </div>
</div>

<{include "footer.tpl"}>