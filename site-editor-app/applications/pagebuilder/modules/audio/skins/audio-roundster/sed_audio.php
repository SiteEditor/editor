<div <?php echo $sed_attrs; ?> class="<?php echo $class ?> s-tb-sm module audio-module audio-module-roundster" <?php echo $has_cover;?>>
	<div id="jp_container_<?php echo $module_html_id ?>" class="sed_jp_container jp-audio playlist-bottom">
		<div class="sed_playlist_item_info" <?php echo $item_settings ?>></div>
		<div class="jp-type-single">
			<div id="jp_jplayer_<?php echo $module_html_id; ?>" class="sed_jp_jplayer jp-jplayer sed-jplayer  <?php if($show_poster === false){  ?> jp-no-poster <?php } ?> "></div>
			<div class="jp-gui">
				<div class="jp-toggle-playlist helper opened">
					<a href="javascript:void(0);" class="jp-toggle-playlist-icon">
						<div class="icon">
							<i class="fa fa-remove closed"></i>
						</div>
					</a>
				</div><!-- .jp-toggle-playlist -->
				<div class="jp-audio-play hidden">
					<a href="javascript:void(0);" class="jp-audio-play-icon" tabindex="1">
						<div class="icon"><i class="fa fa-play"></i></div>
					</a>
				</div>
                <?php if($show_title && $show_poster){  ?>
    				<div class="jp-details jp-title-container">
    					<ul>
    						<li><span class="jp-title"></span></li>
    					</ul>
    				</div>
                <?php } ?>
				<div class="jp-interface-wrapper">
					<div class="jp-interface">
						<div class="jp-controls-holder">
							<ul class="jp-controls">
								<ul class="pull-left">
									<li><a href="javascript:void(0);" class="jp-stop fa fa-stop" style="display: none;" tabindex="1"></a></li>
									<li class="main-element"><a href="javascript:void(0);" class="jp-play fa fa-play" tabindex="1"></a></li>
									<li class="main-element"><a href="javascript:void(0);" class="jp-pause fa fa-pause" tabindex="1"></a></li>
								  <!--	<li><a href="javascript:void(0);" class="jp-previous fa fa-step-backward" tabindex="1"></a></li>
									<li><a href="javascript:void(0);" class="jp-next fa fa-step-forward" tabindex="1"></a></li>  -->
								</ul>

								<ul class="pull-right">
									<li class="volume-controls">
										<a href="javascript:void(0);" class="jp-mute fa fa-volume-up" tabindex="1" title="mute"></a>
										<a href="javascript:void(0);" class="jp-unmute fa fa-volume-off" tabindex="1" title="unmute"></a>
										<div class="fader">
											<div class="wrapper">
												<div class="jp-volume-bar">
													<div class="jp-volume-bar-value"></div>
												</div><!-- .jp-volume-bar -->
											</div>
										</div>
									</li>
								  <!--	<li class="jp-toggle-playlist opened" title="open playlist">
										<a href="javascript:void(0);" class="jp-toggle-playlist-icon">
											<div class="icon">
												<i class="fa fa-list opened"></i>
											</div>
										</a>
									</li> --><!-- .jp-toggle-playlist -->
									<li>
										<a href="javascript:void(0);" class="jp-repeat" tabindex="1" title="repeat">
											<i class="fa fa-retweet"></i>
										</a>
										<a href="javascript:void(0);" class="jp-repeat-off" tabindex="1" title="repeat off">
											<i class="fa fa-retweet"></i>
								 		</a>
									</li>
								 <!--	<li>
										<a href="javascript:void(0);" class="jp-full-screen" tabindex="1" title="full screen">
											<i class="fa fa-expand"></i>
										</a>
									</li>
									<li>
										<a href="javascript:void(0);" class="jp-restore-screen" tabindex="1" title="restore screen">
											<i class="fa fa-compress"></i>
										</a>
									</li>  -->
								</ul><!-- .pull-right -->

								<div class="center">
									<li class="progress-holder">
										<div class="jp-current-time"></div>
										<div class="jp-progress">
											<div class="jp-seek-bar">
												<div class="jp-play-bar"></div>
											</div>
										</div><!-- .jp-progress -->
										<div class="jp-duration"></div>
									</li>
								</div>
								<!-- <li><a href="javascript:void(0);" class="jp-volume-max fa fa-volume-up" tabindex="1" title="max volume"></a></li> -->
							</ul><!-- .jp-controls -->
						</div>
					</div><!-- .jp-interface -->
				</div><!-- .jp-interface-wrapper -->
			</div><!-- .jp-gui -->

			<div class="jp-no-solution">
				<span><?php _e('Update Required' , 'site-editor');?></span>
				<?php _e('To play the media you will need to either update your browser to a recent version or update your' , 'site-editor');?> <a href="http://get.adobe.com/flashplayer/" target="_blank"><?php _e('Flash plugin' , 'site-editor');?>Flash plugin</a>.
			</div>
		</div>
	</div>
</div>