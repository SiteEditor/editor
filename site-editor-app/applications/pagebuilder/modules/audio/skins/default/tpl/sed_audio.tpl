<div {{sed_attrs}} class="{{class}} s-tb-sm module audio-module audio-module-dj" {{has_cover}}>
	<div id="jp_container_{{sed_model_id}}" class="sed_jp_container jp-audio">
		<div class="sed_playlist_item_info" {{{item_settings}}}></div>
		<div class="jp-type-single">
			<div id="jp_jplayer_{{sed_model_id}}" class="sed_jp_jplayer jp-jplayer"></div>

			<div class="jp-gui jp-gui-wrapper">
				<div class="interface ui-1">
					{{#if show_poster}}<div class="jp-playing-poster"><div class="img"></div></div>{{/if}}
					<ul>
			  <!--			<li><a href="#" class="jp-previous">
							<i class="fa fa-step-backward"></i>
						</a></li>    -->
						<li>
							<a href="#" class="jp-play"><i class="jp-icon fa fa-play"></i></a>
							<a href="#" class="jp-pause jp-icon-highlight"><i class="fa fa-pause"></i></a>
						</li>
			  <!--			<li><a href="#" class="jp-next">
							<i class="fa fa-step-forward"></i>
						</a></li>                    -->
                        <li class="btn-repeat off">
                          <a href="javascript:void(0);" class="jp-repeat" tabindex="1" title="repeat">
                            <i class="jp-icon fa fa-retweet"></i>
                          </a>
                          <a href="javascript:void(0);" class="jp-repeat-off jp-icon-highlight" tabindex="1" title="repeat off">
                            <i class="fa fa-retweet"></i>
                          </a>
                        </li>
					</ul>
				</div><!-- .interface ui-1 -->

				<div class="interface ui-2">
					<div class="progress-holder">
						<div class="jp-progress">
							<div class="jp-seek-bar">
								<div class="jp-play-bar">
									<div class="time-holder">
										<div class="time">
											<div class="jp-current-time"></div>
											<div class="jp-duration"></div>
										</div>
									</div>
								</div>
							</div>
						</div><!-- .jp-progress -->
					</div><!-- .progress-holder -->
					<div class="volume-holder">
						<a href="javascript:void(0);" class="jp-mute fa fa-volume-up jp-icon" tabindex="1" title="mute"></a>
						<a href="javascript:void(0);" class="jp-unmute fa fa-volume-off jp-icon-highlight" tabindex="1" title="unmute"></a>
						<div class="fader">
							<div class="wrapper">
								<div class="jp-volume-bar">
									<div class="jp-volume-bar-value"></div>
								</div><!-- .jp-volume-bar -->
							</div>
						</div>
					</div><!-- .volume-holder -->
				</div><!-- .interface ui-2 -->

               {{#if show_title}}
				<div class="interface ui-3">
					<div class="playing-title jp-title-container">
					   <!--	<span class="jp-playing-num"></span>
						<span>-</span> -->
						<span class="jp-playing-title"></span>
					</div>
				</div><!-- .interface ui-3 -->
              {{/if}}
			</div><!-- .jp-gui -->

			<div class="jp-no-solution">
				<span>{{I18n.audio_title_no_support}}</span>
				{{I18n.audio_desc_no_support}}<a href="http://get.adobe.com/flashplayer/" target="_blank">{{I18n.audio_link_no_support}}</a>.
			</div>
		</div>
	</div>
</div>