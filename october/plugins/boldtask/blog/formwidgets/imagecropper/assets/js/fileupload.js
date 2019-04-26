/*
 * MediaFinder plugin
 *
 * Data attributes:
 * - data-control="mediafinder" - enables the plugin on an element
 * - data-option="value" - an option with a value
 *
 * JavaScript API:
 * $('a#someElement').recordFinder({ option: 'value' })
 *
 * Dependences:
 * - Some other plugin (filename.js)
 */

+(function($) {
    "use strict";
    function initCropper() {
        var focusEl = document.querySelector(
            '[data-field-name="main_image_focus"]'
        );
        var preset = focusEl.querySelector("input").value.split(" ");
        focusEl.style.display = "none";
        var img = document.getElementById("crop-image");
        var canvasDOM = document.getElementById("my-canvas");
        var canvasWidth = canvasDOM.offsetWidth;
        var canvasHeight = canvasDOM.offsetHeight;
        canvasDOM.width = canvasDOM.offsetWidth;
        canvasDOM.height = canvasDOM.offsetHeight;
        var imgWidth = img.offsetWidth;
        var imgHeight = img.offsetHeight;

        var canvas = canvasDOM,
            context = canvas.getContext("2d"),
            rect = preset.length
                ? {
                      startX: parseFloat(preset[0], 10),
                      startY: parseFloat(preset[1], 10),
                      w: parseFloat(preset[2], 10),
                      h: parseFloat(preset[3], 10)
                  }
                : {},
            drag = false,
            imageName,
            left = 0,
            top = 0,
            right = 1,
            bottom = 1,
            clearCanvas = function() {
                // context.clearRect(0, 0, canvasWidth, canvasHeight);
                context.fillStyle = "#ffffff";
                context.fillRect(0, 0, canvasWidth, canvasHeight);
            };

        if (Object.keys(preset).length) {
            drawImg();
            context.fillStyle = "rgba(255, 0, 0, 0.3)";
            console.log(rect);
            context.fillRect(
                rect.startX * imgWidth,
                rect.startY * imgHeight,
                rect.w * imgWidth - rect.startX * imgWidth,
                rect.h * imgHeight - rect.startY * imgHeight
            );
        }
        function drawImg() {
            clearCanvas();
            context.drawImage(img, 0, 0, imgWidth, imgHeight);
        }

        function updateResult(left, top, right, bottom) {
            var dataFocus = left + " " + top + " " + right + " " + bottom;
            focusEl.querySelector("input").value = dataFocus;
        }

        // Image for loading
        img.addEventListener("load", drawImg, false);

        // To enable drag and drop
        canvas.addEventListener(
            "dragover",
            function(evt) {
                evt.preventDefault();
            },
            false
        );

        // Detect mousedown
        canvas.addEventListener(
            "mousedown",
            function(e) {
                rect.startX = e.layerX;
                rect.startY = e.layerY;
                drag = true;
            },
            false
        );

        // Detect mouseup
        canvas.addEventListener(
            "mouseup",
            function(e) {
                drag = false;
            },
            false
        );

        // Draw, if mouse button is pressed
        canvas.addEventListener(
            "mousemove",
            function(e) {
                if (drag) {
                    drawImg();

                    rect.w = e.layerX - rect.startX;
                    rect.h = e.layerY - rect.startY;
                    left = rect.startX / imgWidth;
                    top = rect.startY / imgHeight;
                    right = (rect.w + rect.startX) / imgWidth;
                    bottom = (rect.h + rect.startY) / imgHeight;
                    updateResult(
                        left.toFixed(2),
                        top.toFixed(2),
                        right.toFixed(2),
                        bottom.toFixed(2)
                    );
                    context.fillStyle = "rgba(255, 0, 0, 0.3)";
                    console.log(rect.startX, rect.startY, rect.w, rect.h);
                    console.log(left, top, right, bottom);
                    context.fillRect(rect.startX, rect.startY, rect.w, rect.h);
                }
            },
            false
        );
    }
    var imageChanged = new Event("init-cropper");
    window.addEventListener("init-cropper", initCropper);
    var Base = $.oc.foundation.base,
        BaseProto = Base.prototype;

    var MediaFinder = function(element, options) {
        this.$el = $(element);
        this.options = options || {};

        $.oc.foundation.controlUtils.markDisposable(element);
        Base.call(this);
        this.init();
    };

    MediaFinder.prototype = Object.create(BaseProto);
    MediaFinder.prototype.constructor = MediaFinder;

    MediaFinder.prototype.init = function() {
        console.log("cropper initig");
        if (this.options.isMulti === null) {
            this.options.isMulti = this.$el.hasClass("is-multi");
        }

        if (this.options.isPreview === null) {
            this.options.isPreview = this.$el.hasClass("is-preview");
        }

        if (this.options.isImage === null) {
            this.options.isImage = this.$el.hasClass("is-image");
        }

        this.$el.one("dispose-control", this.proxy(this.dispose));

        if (this.options.thumbnailWidth > 0) {
            this.$el.css("maxWidth", this.options.thumbnailWidth + "px");
        } else if (this.options.thumbnailHeight > 0) {
            this.$el.css("maxHeight", this.options.thumbnailHeight + "px");
        }

        // Stop here for preview mode
        if (this.options.isPreview) {
            return;
        }

        this.$el.on(
            "click",
            ".find-button",
            this.proxy(this.onClickFindButton)
        );
        this.$el.on(
            "click",
            ".find-remove-button",
            this.proxy(this.onClickRemoveButton)
        );

        this.$findValue = $("[data-find-value]", this.$el);
    };

    MediaFinder.prototype.dispose = function() {
        this.$el.off(
            "click",
            ".find-button",
            this.proxy(this.onClickFindButton)
        );
        this.$el.off(
            "click",
            ".find-remove-button",
            this.proxy(this.onClickRemoveButton)
        );
        this.$el.off("dispose-control", this.proxy(this.dispose));
        this.$el.removeData("oc.mediaFinder");

        this.$findValue = null;
        this.$el = null;

        // In some cases options could contain callbacks,
        // so it's better to clean them up too.
        this.options = null;

        BaseProto.dispose.call(this);
    };

    MediaFinder.prototype.setValue = function(value) {
        // set value and trigger change event, so that wrapping implementations
        // like mlmediafinder can listen for changes.
        this.$findValue.val(value).trigger("change");
    };

    MediaFinder.prototype.onClickRemoveButton = function() {
        this.setValue("");

        this.evalIsPopulated();
    };

    MediaFinder.prototype.onClickFindButton = function() {
        var self = this;

        new $.oc.mediaManager.popup({
            alias: "ocmediamanager",
            cropAndInsertButton: true,
            onInsert: function(items) {
                if (!items.length) {
                    alert("Please select image(s) to insert.");
                    return;
                }

                if (items.length > 1) {
                    alert("Please select a single item.");
                    return;
                }

                var path, publicUrl;
                for (var i = 0, len = items.length; i < len; i++) {
                    path = items[i].path;
                    publicUrl = items[i].publicUrl;
                }

                self.setValue(path);

                if (self.options.isImage) {
                    $("[data-find-image]", self.$el)
                        .attr("src", publicUrl)
                        .on("load", function() {
                            console.log("image loaded");
                            window.dispatchEvent(imageChanged);
                        });
                }

                self.evalIsPopulated();

                this.hide();
            }
        });
    };

    MediaFinder.prototype.evalIsPopulated = function() {
        var isPopulated = !!this.$findValue.val();
        this.$el.toggleClass("is-populated", isPopulated);
        $("[data-find-file-name]", this.$el).text(
            this.$findValue.val().substring(1)
        );
    };

    MediaFinder.DEFAULTS = {
        isMulti: null,
        isPreview: null,
        isImage: null
    };

    // PLUGIN DEFINITION
    // ============================

    var old = $.fn.mediaFinder;

    $.fn.mediaFinder = function(option) {
        var args = arguments;

        return this.each(function() {
            var $this = $(this);
            var data = $this.data("oc.mediaFinder");
            var options = $.extend(
                {},
                MediaFinder.DEFAULTS,
                $this.data(),
                typeof option == "object" && option
            );
            if (!data)
                $this.data(
                    "oc.mediaFinder",
                    (data = new MediaFinder(this, options))
                );
            if (typeof option == "string") data[option].apply(data, args);
        });
    };

    $.fn.mediaFinder.Constructor = MediaFinder;

    $.fn.mediaFinder.noConflict = function() {
        $.fn.mediaFinder = old;
        return this;
    };

    $(document).render(function() {
        $('[data-control="mediafinder"]').mediaFinder();
    });

    $(window).on("oc.updateUi", function() {
        window.dispatchEvent(imageChanged);
        // code
    });
})(window.jQuery);
