<div id="{{sed_model_id}}" style="height:{{parallax_height}}px" {{sed_attrs}} class="{{class}} module parallax-slider parallax-slider-default "{{{item_settings}}}>
    <div class=" pxs_navigation">
    	<span style="right:{{parallax_nav_space}}%" class="pxs_next"></span>
    	<span style="left:{{parallax_nav_space}}%" class="pxs_prev"></span>
    </div>
    <div class="pxs_bg">
        <div class="pxs_bg1"></div>
        <div class="pxs_bg2"></div>
        <div class="pxs_bg3"></div>
    </div>
    <div class="pxs_loading">Loading images...</div>
    {{{content}}}
</div>

<style type="text/css">
[sed_model_id="{{sed_model_id}}"] .image-container{
    width:{{parallax_item_width}}%;
    top:{{parallax_item_top}}px;
    bottom:{{parallax_item_bottom}}px;
    left:{{parallax_item_left}}%;
}
</style>