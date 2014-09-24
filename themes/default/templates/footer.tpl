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

<script type="text/javascript">
  $(document).ready(function() {
    var prev_link = $('div.help_book_footer_prev a');
    var next_link = $('div.help_book_footer_next a');

    /**
     * Hide both links
     */
    var hide_both = function() {
      if (prev_link.length) {
        prev_link.hide();
      }

      if (next_link.length) {
        next_link.hide();
      }
    }

    /**
     * Hide prev link
     */
    var hide_prev = function() {
      if (prev_link.length) {
        prev_link.hide();
      }
    }

    /**
     * Hide next link
     */
    var hide_next = function() {
      if (next_link.length) {
        next_link.hide();
      }
    }

    var sidebar = $('#sidebar');

    if (sidebar.length) {
      var prev = null, next = null, prev_item, finish_with_next = false;

      sidebar.find('li').each(function() {
        var item = $(this);

        if (finish_with_next) {
          next = item;
          return false; // and break
        }

        if (item.is('.selected')) {
          prev = prev_item;
          finish_with_next = true;
        }

        prev_item = item; // Remember for the next iteration
      });

      if (prev && prev.length) {
        prev_link.attr('href', prev.find('a').attr('href'));
      } else {
        hide_prev();
      }

      if (next && next.length) {
        next_link.attr('href', next.find('a').attr('href'));
      } else {
        hide_next();
      }
    } else {
      hide_both();
    }
  });
</script>