<div id="footer">
  <div class="footer_space"></div>
  <div class="rights"><p>&copy; <{if $copyright_since}><{$copyright_since}>-<{/if}><{'Y'|date}> &middot; <{$copyright}>, All rights reserved.</p></div>

  <div class="social">
    <p>Stay up to date with all new features:</p>
    <ul class="links">
      <li><a href="https://twitter.com/activecollab" target="_blank"><img title="Twitter" alt="Twitter" src="<{theme_asset name="images/icon_twitter.png" page_level=$page_level}>"></a></li>
      <li><a href="https://www.facebook.com/activecollab" target="_blank"><img title="Facebook" alt="Facebook" src="<{theme_asset name="images/icon_facebook.png" page_level=$page_level}>"></a></li>
      <li><a href="https://plus.google.com/+activecollab" target="_blank"><img title="Google+" alt="Google+" src="<{theme_asset name="images/icon_google.png" page_level=$page_level}>"></a></li>
    </ul>
  </div>
</div>

<{foreach $plugins as $plugin}>
  <{$plugin->renderFoot() nofilter}>
<{/foreach}>