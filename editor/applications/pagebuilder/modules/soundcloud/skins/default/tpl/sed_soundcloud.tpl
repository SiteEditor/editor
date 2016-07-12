<# color = color.replace("#" , "" ); #>
<div {{sed_attrs}} class="sed-soundcloud {{className}}" width="{{width}}" height="{{height}}" {{has_cover}}>
<iframe width="{{width}}" height=" <# if(visual){ #> {{height}} <# }else{ #> auto <# } #>" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url={{url}}&amp;color={{color}}&amp;auto_play={{auto_play}}&amp;show_comments={{comments}}&amp;visual={{visual}}"></iframe></div>

