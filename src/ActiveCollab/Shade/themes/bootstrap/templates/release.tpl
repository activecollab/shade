<{include "header.tpl"}>

<div id="content_wrapper" class="container">
  <{include "release_sidebar.tpl"}>

  <div id="content">
  <{if $current_release}>
    <article>
      <h1><abbr title="Version">v</abbr><{$current_release->getTitle()}></h1>
      <div class="body">
      <{if $current_release->getReleaseDate() instanceof DateTime}>
        <p class="release_date">Released on: <span><{$current_release->getReleaseDate()->format('Y-m-d')}></span></p>
      <{/if}>
        <{$current_release->renderBody() nofilter}>
      </div>
      <{include "prev_top_next.tpl"}>
    </article>
  </div>
  <{else}>
    <h1>Release Not Found</h1>
  <{/if}>
  </div>
</div>

<{include "footer.tpl"}>