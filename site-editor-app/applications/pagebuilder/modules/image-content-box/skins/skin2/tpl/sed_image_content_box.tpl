<div {{sed_attrs}} class="s-tb-sm module module-image-content-box image-content-box-skin2 {{class}}">
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
    padding: {{item_img}}px {{item_img}}px {{item_img}}px 0;
}
@media (max-width: 768px) {
[sed_model_id="{{sed_model_id}}"] .img-item{
    padding: {{item_img}}px {{item_img}}px 0 ;
}
}
</style>