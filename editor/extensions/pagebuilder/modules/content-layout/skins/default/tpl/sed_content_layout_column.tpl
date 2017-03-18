<td class="sed-column-pb {{className}}" {{sed_attrs}} sed-role="column-pb">

  <# if( sed_main_content == "no" ){ #>
    <div class="sed-column-contents-pb sed-pb-component" data-parent-id="{{sed_model_id}}" drop-placeholder="{{placeholder}}">
        {{{content}}}
    </div>
  <# }else{ #>
    <div class="sed-column-contents-pb sed-main-content-column">
      {{{content}}}
    </div>
  <# } #>
  <style type="text/css">
  <!--
  [sed_model_id="{{sed_model_id}}"]{
    width : {{width}};
  }
  -->
  </style>
</td>



