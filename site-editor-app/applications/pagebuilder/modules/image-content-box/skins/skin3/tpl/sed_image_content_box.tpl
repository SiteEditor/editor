<div {{sed_attrs}} class="s-tb-sm module module-image-content-box image-content-box-skin3 {{class}}">
    <div class="item">
        <div class="inner {{arrow}} {{#ifCond show_button "===" false }} hide-button {{/ifCond}} {{#ifCond item_bodered ">" 0 }} item-bodered {{/ifCond}}">
          {{{content}}}
        </div><!-- .inner -->
    </div><!-- .item -->
</div>

<style type="text/css">
[sed_model_id="{{sed_model_id}}"] .inner{
    border-width: {{item_bodered}}px;
}
[sed_model_id="{{sed_model_id}}"] .img-item{
    padding: 0 {{item_img}}px {{item_img}}px;
}
</style>