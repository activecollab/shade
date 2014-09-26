<{include "header.tpl"}>

<div id="content_wrapper" class="container">
  <{include "whats_new_sidebar.tpl"}>

  <div id="content">
    <{if $current_whats_new_article}>
    <article>
      <h1><{$current_whats_new_article->getTitle()}></h1>
      <div class="body"><{$current_whats_new_article->renderBody() nofilter}></div>
      <{include "prev_top_next.tpl"}>
      <div class="comments">
      <{foreach $plugins as $plugin}>
        <{$plugin->renderComments($current_whats_new_article) nofilter}>
      <{/foreach}>
      </div>
    </article>
  </div>
  <{else}>
  <h1>Article Not Found</h1>
  <{/if}>
</div>
</div>

<{include "footer.tpl"}>