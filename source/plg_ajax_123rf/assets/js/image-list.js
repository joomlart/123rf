;
(function ($, window, document, undefined) {
    // Create the defaults once
    var pluginName = 'fitRowImages',
        defaults = {
            baseHeight: 200,
            margin: 5,
            click: null,
            click_height: 500
        };

    // The actual plugin constructor
    function Plugin(element, options) {
        this.element = $(element);

        this.options = $.extend({}, defaults, options);

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype.init = function () {
        var self = this;
        this.inner = this.element.children().first();
        // Init images
        this.initImages();

        // event for resize, image change
        this.resizeTimeout = this.imageTimeout = 0;

        // when container with change, relayout the list
        this.wraperWidth = this.element.width();

        // listen on dom change
        setInterval(function () {
            self.imagesObserver();
        }, 1000);

        // handle click and show content as google images
        if (this.options.click) {
            this.images.on('click', function () {
              self.showDetails(this);
              /*
                var $item = $(this).closest('.img-item');
                if ($item.hasClass('active')) {
                    self.hideDetails();
                } else {
                    self.hideDetails();
                    $item.addClass('active');
                    self.showDetails(this);
                }
                */
            });
        }
    };

    Plugin.prototype.initImages = function () {
        var self = this;
        this.items = this.inner.find('.img-item')
        this.images = this.inner.find('.img-item img');
        this.images.css('margin', this.options.margin);
        this.layout();
        // image load, relayout
        this.images.off('load').on('load', function () {
            clearTimeout(self.imageTimeout);
            self.imageTimeout = setTimeout(function () {
                self.layout();
            }, 200);
        });
    }

    Plugin.prototype.layout = function () {
        var self = this;
        this.wraperWidth = this.element.width();
        this.inner.width(this.wraperWidth + this.options.margin);

        var row = [], w = 0, iw = 0, r = 0;
        for (var i = 0; i < this.images.length; i++) {
            var img = this.images[i], $img = $(img);
            if (!IsImageOk(img)) continue;
            iw = $img.width() * this.options.baseHeight / $img.height();
            if ((2 * w + iw) < this.wraperWidth * 2) {
                row.push(img);
                w += iw;
            } else {
                // push row items in a wrapper
                var h = (this.wraperWidth - row.length * 2 * this.options.margin) / w * this.options.baseHeight;
                $(row).css('height', h).data('row', r++);
                // make new row
                row = [];
                row.push(img);
                w = iw;
            }
        }

        if (row.length) {
            var h = (this.wraperWidth - row.length * 2 * this.options.margin) / w * this.options.baseHeight;
            // last row, force scale less than 120%
            if (h > this.options.baseHeight * 1.2) h = this.options.baseHeight;
            $(row).css('height', h).data('row', r++);
        }
    };

    Plugin.prototype.imagesObserver = function () {
        var self = this;
        var testimgs = this.inner.find('.img-item');
        if (this.items.length != testimgs.length || !this.items.first().is(testimgs.first())) {
            this.initImages();
            return ;
        }

        // check if container width change
        if (this.wraperWidth != this.element.width()) {
          this.layout();
        }
    }

    Plugin.prototype.showDetails = function (img) {
        var self = this;
        var $img = $(img),
            $container = $('<div class="img-container"><span class="btn-close"></span></div>'),
            $item = $img.closest('.img-item'),
            $lastitem = this.inner.find('.img-item.active');

        if ($item.is($lastitem)) {
          this.slideUp($item);
          return ;
        }
        // update style
        $container.css({
            width: '100%',
            height: 0,
            position: 'absolute',
            top: 'auto',
            left: 0
        });
        $container.appendTo ($item);

       if ($lastitem.length > 0 && $lastitem.find('img:first-child').data('row') == $img.data('row')) {
          // just remove the current, add new one
          this.fadeOut($lastitem);
          this.fadeIn($item);
        } else {
          // animation,
          if ($lastitem.length) {
            // slide up
            this.slideUp($lastitem);
          }
          this.slideDown($item);
        }

        $container.find('.btn-close').click(function () {
            self.slideUp($(this).closest('.img-item'));
        });

        this.options.click($item, $container);
    }

    Plugin.prototype.slideUp = function ($item) {
        var $container = $item.find('.img-container'),
          h = $item.height() - $container.height();
        $item.removeClass('active');
        $container.animate({height: 0}, {
          step: function (now){
            $item.height(h+now);
          },
          complete: function () {
            $container.remove();
            $item.height('auto');
          },
          queue: false
        });
    }

    Plugin.prototype.slideDown = function ($item) {
        var $container = $item.find('.img-container'),
          h = $item.height();
        $item.addClass('active');
        $container.animate({height: this.options.click_height}, {
          step: function (now){
            $item.height(h+now);
          },
          complete: function () {
          },
          queue: false
        });
    }

    Plugin.prototype.fadeOut = function ($item) {
        var $container = $item.find('.img-container');
        $item.removeClass('active');
        $container.animate({opacity: 0}, {
          complete: function () {
            $container.remove();
            $item.height('auto');
          },
          queue: false
        });
    }

    Plugin.prototype.fadeIn = function ($item) {
        var $container = $item.find('.img-container');
        $item.addClass('active').height($item.height() + this.options.click_height);
        $container.css({opacity: 0, height: this.options.click_height});
        $container.animate({opacity: 1}, {
          queue: false
        });
    }

    var IsImageOk = function (img) {
        // During the onload event, IE correctly identifies any images that
        // weren’t downloaded as not complete. Others should too. Gecko-based
        // browsers act like NS4 in that they report this incorrectly.
        if (!img.complete) {
            return false;
        }

        // However, they do have two very useful properties: naturalWidth and
        // naturalHeight. These give the true size of the image. If it failed
        // to load, either of these should be zero.

        if (typeof img.naturalWidth !== "undefined" && img.naturalWidth === 0) {
            return false;
        }

        // No other way of checking: assume it’s ok.
        return true;
    }

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
        });
    }

})(jQuery, window, document);
