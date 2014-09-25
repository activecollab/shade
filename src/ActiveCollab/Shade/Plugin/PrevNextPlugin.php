<?php

  namespace ActiveCollab\Shade\Plugin;

  /**
   * Implement Prev and Next page navigation (based on links that are in the sidebar)
   *
   * @package ActiveCollab\Shade\Plugin
   */
  class PrevNextPlugin extends Plugin
  {
    /**
     * Returns in <head> tag
     *
     * @return string
     */
    function renderFoot()
    {
      return "<script type=\"text/javascript\">
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
      </script>";
    }
  }