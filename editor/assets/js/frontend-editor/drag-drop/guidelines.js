(function($)
{
    var sedGuidelines = function( ui , data , options , currentItem ){
        var snapTolerance = options.guidelines.snapTolerance ? options.guidelines.snapTolerance : 2;
        var sides = ["top", "right", "bottom", "left" , "hcenter" , "vcenter"];
        var snaps = [];

        ui.size = (ui.size) ? ui.size : data.dragElementSize;
        var cposFunc = function( d , pos){
            //if(typeof data._convertPositionTo == "function")
                return data._convertPositionTo(d , pos);
            //else
                //return _convertPositionTo(d , pos , ui.helper);
        };

        for (var i = 0, l = sides.length; i < l; i++)
        {
            var sideSnaps = $.grep(data.items, function(item) {
                return item.snapSide === sides[i] || item.oppositeSnapSide === sides[i] ;
            });

            var marginSnaps = $.grep(data.margins, function (item) {
                return item.snapSide === sides[i];
            });

            var j, len;
            for (j = 0, len = sideSnaps.length; j < len; j++)
            {
                if (Math.abs(currentItem[sides[i]] - sideSnaps[j].position) <= snapTolerance)
                {             ////api.log(ui.size.height);
                    //found a guideline for a snap
                    switch (sides[i])
                    {
                        case "top":
                            ui.position.top = cposFunc("relative", { top: sideSnaps[j].position, left: 0 }).top;
                            break;
                        case "bottom":
                            ui.position.top = cposFunc("relative", { top: sideSnaps[j].position, left: 0 }).top - ui.size.height;
                            break;
                        case "right":
                            ui.position.left = cposFunc("relative", { left: sideSnaps[j].position, top: 0 }).left - ui.size.width;
                            break;
                        case "left":
                            ui.position.left = cposFunc("relative", { left: sideSnaps[j].position, top: 0 }).left;
                            break;
                        case "hcenter":
                            ui.position.top = cposFunc("relative", { top: sideSnaps[j].position, left: 0 }).top - (ui.size.height/2);
                            break;
                        case "vcenter":
                            ui.position.left = cposFunc("relative", { left: sideSnaps[j].position, top: 0 }).left - (ui.size.width/2);
                            break;
                        default:
                            break;
                    }
                    snaps.push(sideSnaps[j]);
                }
            }

            for (j = 0, len = marginSnaps.length; j < len; j++)
            {
                var oppositeSide = (i + 2) > 3 ? (i - 2) : (i + 2);
                if (Math.abs(currentItem[sides[oppositeSide]] - marginSnaps[j].position) <= snapTolerance)
                {
                    if (sides[i] === "top" || sides[i] === "bottom")
                    {
                        if (Math.min(currentItem.right, marginSnaps[j].offset.left + marginSnaps[j].size.width) - Math.max(currentItem.left, marginSnaps[j].offset.left) <= 0)
                        {
                            continue;
                        }
                    }
                    else
                    {
                        if (Math.min(currentItem.bottom, marginSnaps[j].offset.top + marginSnaps[j].size.height) - Math.max(currentItem.top, marginSnaps[j].offset.top) <= 0)
                        {
                            continue;
                        }
                    }
                    //found margin for a snap
                    switch (sides[i])
                    {
                        case "top":
                            ui.position.top = cposFunc("relative", { top: marginSnaps[j].position - snapTolerance, left: 0 }).top - ui.size.height;
                            break;
                        case "bottom":
                            ui.position.top = cposFunc("relative", { top: marginSnaps[j].position + snapTolerance, left: 0 }).top;
                            break;
                        case "right":
                            ui.position.left = cposFunc("relative", { left: marginSnaps[j].postion + snapTolerance, top: 0 }).left;
                            break;
                        case "left":
                            ui.position.left = cposFunc("relative", { left: marginSnaps[j].position - snapTolerance, top: 0 }).left - ui.size.width;
                            break;
                        case "hcenter":
                            //ui.position.top = cposFunc("relative", { top: marginSnaps[j].position + (snapTolerance/2), left: 0 }).top - (ui.size.height/2);
                            break;
                        case "vcenter":
                            //ui.position.left = cposFunc("relative", { left: marginSnaps[j].position + (snapTolerance/2), top: 0 }).left - (ui.size.width/2);
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        //clear old guides
        $(".draggable-snap-guidelines").remove();

        var draggableOffset = ui.helper.offset();

        $.each(data.items , function(){
            this.item.removeClass("item-snap-active");
        });

        //render guides
        $.each(snaps, function ()
        {
            var snappedToOffset = this.offset;
            var snappedToSize = this.size;
            var snappedToSides = { top: snappedToOffset.top, right: snappedToOffset.left + snappedToSize.width, bottom: snappedToOffset.top + snappedToSize.height, left: snappedToOffset.left , vcenter : snappedToOffset.left + (snappedToSize.width/2), hcenter : snappedToOffset.top + (snappedToSize.height/2) };
            var draggableSides = { top: draggableOffset.top, right: draggableOffset.left + ui.size.width, bottom: draggableOffset.top + ui.size.height, left: draggableOffset.left , vcenter : draggableOffset.left + (ui.size.width/2), hcenter : draggableOffset.top + (ui.size.height/2)  };
            var currentSnapSide;
            ////api.log((ui.size.width/2) )
            //if(this.snapSide == "vcenter" || this.snapSide == "hcenter")
                ////api.log( this.snapSide + " s:" + snappedToSides[this.snapSide] + " d:" + draggableSides[this.snapSide] );

            this.item.removeClass("item-snap-active");

            if (snappedToSides[this.snapSide] === draggableSides[this.snapSide])
            {
                currentSnapSide = this.snapSide;
            }else if(snappedToSides[this.snapSide] === draggableSides[this.oppositeSnapSide]){
                currentSnapSide = this.oppositeSnapSide;
            }else{
                return ;
            }

            this.item.addClass("item-snap-active");

            var snapGuideline = $("<div class='draggable-snap-guidelines'></div>");
            this.element.parent().append(snapGuideline);

            var guidePosition;

            switch ( currentSnapSide )
            {
                case "top":
                    guidePosition = cposFunc("relative", { top: draggableOffset.top, left: Math.min(snappedToOffset.left + snappedToSize.width, draggableOffset.left + ui.size.width) });
                    snapGuideline.css({
                        "top": guidePosition.top,
                        "left": guidePosition.left,
                        "width": Math.abs(Math.max(snappedToOffset.left, draggableOffset.left) - Math.min(snappedToOffset.left + snappedToSize.width, draggableOffset.left + ui.size.width))
                    });
                    snapGuideline.addClass("draggable-snap-guidelines-horizontal");
                    break;
                case "bottom":
                    guidePosition = cposFunc("relative", { top: draggableOffset.top + ui.size.height, left: Math.min(snappedToOffset.left + snappedToSize.width, draggableOffset.left + ui.size.width) });
                    snapGuideline.css({
                        "top": guidePosition.top,
                        "left": guidePosition.left,
                        "width": Math.abs(Math.max(snappedToOffset.left, draggableOffset.left) - Math.min(snappedToOffset.left + snappedToSize.width, draggableOffset.left + ui.size.width))
                    });
                    snapGuideline.addClass("draggable-snap-guidelines-horizontal");
                    break;
                case "left":
                    guidePosition = cposFunc("relative", { top: Math.min(snappedToOffset.top + snappedToSize.height, draggableOffset.top + ui.size.height), left: draggableOffset.left });
                    snapGuideline.css({
                        "top": guidePosition.top,
                        "left": guidePosition.left,
                        "height": Math.abs(Math.max(snappedToOffset.top, draggableOffset.top) - Math.min(snappedToOffset.top + snappedToSize.height, draggableOffset.top + ui.size.height))
                    });
                    snapGuideline.addClass("draggable-snap-guidelines-vertical");
                    break;
                case "right":
                    guidePosition = cposFunc("relative", { top: Math.min(snappedToOffset.top + snappedToSize.height, draggableOffset.top + ui.size.height), left: draggableOffset.left + ui.size.width });
                    snapGuideline.css({
                        "top": guidePosition.top,
                        "left": guidePosition.left,
                        "height": Math.abs(Math.max(snappedToOffset.top, draggableOffset.top) - Math.min(snappedToOffset.top + snappedToSize.height, draggableOffset.top + ui.size.height))
                    });
                    snapGuideline.addClass("draggable-snap-guidelines-vertical");
                    break;
                case "vcenter":
                    guidePosition = cposFunc("relative", { top: Math.min(snappedToOffset.top + snappedToSize.height, draggableOffset.top + ui.size.height), left: draggableOffset.left + (ui.size.width/2) });
                    snapGuideline.css({
                        "top": guidePosition.top,
                        "left": guidePosition.left,
                        "height": Math.abs(Math.max(snappedToOffset.top, draggableOffset.top) - Math.min(snappedToOffset.top + snappedToSize.height, draggableOffset.top + ui.size.height))
                    });
                    snapGuideline.addClass("draggable-snap-guidelines-vertical");
                    break;
                case "hcenter":
                    guidePosition = cposFunc("relative", { top: draggableOffset.top + (ui.size.height/2), left: Math.min(snappedToOffset.left + snappedToSize.width, draggableOffset.left + ui.size.width) });
                    snapGuideline.css({
                        "top": guidePosition.top,
                        "left": guidePosition.left,
                        "width": Math.abs(Math.max(snappedToOffset.left, draggableOffset.left) - Math.min(snappedToOffset.left + snappedToSize.width, draggableOffset.left + ui.size.width))
                    });
                    snapGuideline.addClass("draggable-snap-guidelines-horizontal");
                    break;
                default:
                    break;
            }
        });
    };

    $.ui.plugin.add("draggable", "guidelines", {
        start: function (event, ui)
        {
            //format for snap guidelines is { position: x/y, snapSide: [top, right, bottom, left], element: $(), size: {top, left}, offset: {top, left} }
            //format for snap margins is { position: x/y, snapSide: [top, right, bottom, left], element: $(), size: {top, left}, offset: {top, left} }
                ////api.log( $(this).data() );
            var i = $(this).data("uiDraggable"), o = i.options;
            if (typeof (o.guidelines.items) === "function")
            {
                i.items = o.guidelines.items.apply(this);
            }
            else if ($.isArray(o.guidelines.items))
            {
                i.items = o.guidelines.items;
            }
            if (typeof (o.guidelines.margins) === "function")
            {
                i.margins = o.guidelines.margins.apply(this);
            }
            else if ($.isArray(o.guidelines.margins))
            {
                i.margins = o.guidelines.margins;
            }

            i.dragElementSize = { width: $(this).width(), height: $(this).height() };
        },
        drag: function (event, ui)
        {
            if (event.shiftKey)
            {
                return;
            }
            var data = $(this).data("uiDraggable");
            var options = data.options;
            var currentItem = { top: ui.offset.top, right: data.dragElementSize.width + ui.offset.left, bottom: data.dragElementSize.height + ui.offset.top, left: ui.offset.left, vcenter: (data.dragElementSize.width/2) + ui.offset.left, hcenter: (data.dragElementSize.height/2) + ui.offset.top };

            sedGuidelines(ui , data , options , currentItem);

            if (options.guidelines.snap)
            {
                options.guidelines.snap.call(this, snaps, event.target);
            }
        },
        stop: function (event, ui)
        {
            var i = $(this).data("uiDraggable");
            $.each(i.items , function(){
                this.item.removeClass("item-snap-active");
            });

            $(".draggable-snap-guidelines").remove();

         }
    });

})(jQuery);