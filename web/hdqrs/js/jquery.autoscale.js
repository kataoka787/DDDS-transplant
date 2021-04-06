/*  
===============================================================================
autoscale - jQuery plugin to scale elements to browser window on resize.
            I deveoped this to scale images in particular.
Copyright 2007 - Doug Sparling
...............................................................................
IE Resize Based on WResize by Andrea Ercolino
-------------------------------------------------------------------------------
LICENSE: http://www.opensource.org/licenses/mit-license.php
WEBSITE:
===============================================================================
*/

(function($) {
  $.fn.autoscale = function(el,settings)  {
    settings = jQuery.extend({
      marginWidth: settings,
    }, settings);
    var version = '0.1.0';
    var resize = {fired: false, width: 0};

    // iterate and do the resizing
    this.each(function()  {
      if (this == window) {
        contentResize(); // resizes on window load but not on resize
        $(this).resize(handleWResize); // resizes on resizes but not load
      } else {
        contentResize();
      }
    });

    function resizeOnce() {
      if ($.browser.msie) {
        if (!resize.fired) {
          resize.fired = true;
        } else {
          var version = parseInt($.browser.version, 10);
          resize.fired = false;
          if (version < 7) {
            return false;
          } else if (version == 7) {
            //a vertical resize is fired once, an horizontal resize twice
            var width = $( window ).width();
            if (width != resize.width) {
              resize.width = width;
              return false;
            }
          }
        }
      }
      return true;
    }

    function handleWResize(e)  {
      if (resizeOnce()) {
        return contentResize();
      }
    }

    function contentResize() {
      var w = $(window);
      var H = w.height();
      var W = w.width();

      if ( $(el).length >= 1 )  {
        var width = W * settings.marginWidth / 100;
        $(el).css({width: width}); }
      }

    return this;
  };
}) (jQuery);
