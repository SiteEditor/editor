<footer  {{sed_attrs}} sed_role="site-footer" class="footer-area module module-footer footer-default <# if(footer_style == "footer-dark-style"){ #> black-style-widget <# } #> {{footer_style}} {{className}}">
  <div class="sed-pb-component footer-inner"  data-parent-id="{{sed_model_id}}"  drop-placeholder="Drop Each Module Into The Footer">
    {{{content}}}
  </div>
</footer>