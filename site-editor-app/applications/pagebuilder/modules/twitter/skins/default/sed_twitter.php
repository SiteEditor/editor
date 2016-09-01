<div <?php echo $sed_attrs; ?> class="<?php echo $class; ?> s-tb-sm module twitter-module twitter-module-default " <?php echo $has_cover;?>>
<?php
if($twitter_id && $consumer_key && $consumer_secret && $access_token && $access_token_secret && $count) {
$transName = 'list_tweets_'.$module_html_id;
$cacheTime = 10;
if(false === ($twitterData = get_transient($transName))) {

	$token = get_option('cfTwitterToken_'.$module_html_id);

	// get a new token anyways
	delete_option('cfTwitterToken_'.$module_html_id);

	// getting new auth bearer only if we don't have one
	if(!$token) {
		// preparing credentials
		$credentials = $consumer_key . ':' . $consumer_secret;
		$toSend = base64_encode($credentials);

		// http post arguments
		$args = array(
			'method' => 'POST',
			'httpversion' => '1.1',
			'blocking' => true,
			'headers' => array(
				'Authorization' => 'Basic ' . $toSend,
				'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
			),
			'body' => array( 'grant_type' => 'client_credentials' )
		);

		add_filter('https_ssl_verify', '__return_false');
		$response = wp_remote_post('https://api.twitter.com/oauth2/token', $args);

		$keys = json_decode(wp_remote_retrieve_body($response));

		if($keys) {
			// saving token to wp_options table
			update_option('cfTwitterToken_'.$module_html_id, $keys->access_token);
			$token = $keys->access_token;
		}
	}
	// we have bearer token wether we obtained it from API or from options
	$args = array(
		'httpversion' => '1.1',
		'blocking' => true,
		'headers' => array(
			'Authorization' => "Bearer $token"
		)
	);

	add_filter('https_ssl_verify', '__return_false');
	$api_url = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name='.$twitter_id.'&count='.$count;
	$response = wp_remote_get($api_url, $args);

	set_transient($transName, wp_remote_retrieve_body($response), 60 * $cacheTime);
}
@$twitter = json_decode(get_transient($transName), true);
if($twitter && is_array($twitter)) {
?>
<div class="twitter-box">
	<div class="twitter-holder">
		<div class="b">
			<div class="tweets-container" id="tweets_<?php echo $module_html_id; ?>">
				<ul class="jtwt">
					<?php foreach($twitter as $tweet): ?>
					<li class="jtwt_tweet">
						<p class="jtwt_tweet_text">
						<?php
						$latestTweet = $tweet['text'];
						$latestTweet = preg_replace('/http:\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', '&nbsp;<a href="http://$1" target="_blank">http://$1</a>&nbsp;', $latestTweet);
						$latestTweet = preg_replace('/@([a-z0-9_]+)/i', '&nbsp;<a href="http://twitter.com/$1" target="_blank">@$1</a>&nbsp;', $latestTweet);
						echo $latestTweet;
						?>
						</p>
						<?php
						$twitterTime = strtotime($tweet['created_at']);
						$timeAgo = $this->ago($twitterTime);
						?>
						<a href="http://twitter.com/<?php echo $tweet['user']['screen_name']; ?>/statuses/<?php echo $tweet['id_str']; ?>" class="jtwt_date"><?php echo $timeAgo; ?></a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	<span class="arrow"></span>
</div>
<?php
    }elseif( site_editor_app_on() || ( isset( $_REQUEST['sed_page_ajax'] ) && $_REQUEST['sed_page_ajax'] == "sed_load_modules" ) )
        echo "<p class='empty-tweet'>" . __( "Not Exist Any tweet" , "site-editor" ) . "</p>";
}elseif( site_editor_app_on() || ( isset( $_REQUEST['sed_page_ajax'] ) && $_REQUEST['sed_page_ajax'] == "sed_load_modules" )  )
    echo "<p class='invalid-info-twitter'>" . __( "Please Insert Valid twitter info" , "site-editor" ) . "</p>";

?>
</div>
