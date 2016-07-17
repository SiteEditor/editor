<div {{sed_attrs}} {{{item_settings}}} class="{{class}} s-tb-sm module module-grid-gallery sed-grid-gallery-default grid-default sed-columns-{{count_columns}}" >
    {{{content}}}
    <div class="controls">
      <span class="control fa fa-arrow-circle-left" data-direction="previous"></span>
      <span class="control fa fa-arrow-circle-right" data-direction="next"></span>
      <span class="grid fa fa-th"></span>
      <!--<span class="fs-toggle fa fa-arrows-alt icon-fullscreen"></span>-->
    </div>
</div>
<style type="text/css">
[sed_model_id="{{sed_model_id}}"] .items--small li{
  padding: {{padding}}px;
}
[sed_model_id="{{sed_model_id}}"] .items--small{
  margin: -{{padding}}px;
}
</style> 

