(function($)
{
     $.ui.plugin.add("draggable", "overlap", {
        start: function (event, ui)
        {
            var i = $(this).data("uiDraggable"), o = i.options;
            i.overlapTargets = [];

            if (typeof (o.overlap.items) === "function")
            {
                i.overlapTargets = o.overlap.items.apply(this);
            }
            else if ($.isArray(o.overlap.items))
            {
                i.overlapTargets = o.overlap.items;
            }
            else
            {
                //assume jQuery object
                o.overlap.items.each(function () {
                    var $t = $(this);
                    var offset = $t.offset();
                    i.overlapTargets.push({ top: offset.top, right: $t.width() + offset.left, bottom: $t.height() + offset.top, left: offset.left, element: $t });
                });
            }

            //assume that the dragged object isn't going to change it's shape while we are dragging it and cache the size
            i.dragElementSize = { width: $(this).outerWidth(), height: $(this).outerHeight() };
        },
        drag: function (event, ui)
        {
            //check for overlaps here
            var data = $(this).data("uiDraggable");
            var currentItem = { top: ui.offset.top, right: data.dragElementSize.width + ui.offset.left, bottom: data.dragElementSize.height + ui.offset.top, left: ui.offset.left };
            var overlapItems = [];
            for (var i = 0, l = data.overlapTargets.length; i < l; i++)
            {
                var checkItem = data.overlapTargets[i];
                overlapping = currentItem.left < checkItem.right && currentItem.right > checkItem.left && currentItem.top < checkItem.bottom && currentItem.bottom > checkItem.top;

                if (overlapping)
                {
                    var overlapRect = {
                        top: Math.max(currentItem.top, checkItem.top),
                        left: Math.max(currentItem.left, checkItem.left),
                        width: Math.min(currentItem.right, checkItem.right) - Math.max(currentItem.left, checkItem.left),
                        height: Math.min(currentItem.bottom, checkItem.bottom) - Math.max(currentItem.top, checkItem.top)
                    };
                    var result = $.extend({}, data.overlapTargets[i]);
                    result.overlapRect = overlapRect;
                    overlapItems.push(result);
                    if (data.options.overlap.fast)
                    {
                        break;
                    }
                }
            }
            if (overlapItems.length > 0)
            {
                data.options.overlap.overlap.call(this, overlapItems, event.target);
            }
            else
            {
                data.options.overlap.overlap.call(this, new Array(), event.target);
            }
        }
    });

})(jQuery);    