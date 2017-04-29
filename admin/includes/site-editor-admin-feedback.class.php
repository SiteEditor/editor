<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Site Editor Admin Feedback Class
 *
 * Send Customer Feedback to SiteEditor.ORG for enhance SiteEditor Quality
 *
 * Thanks from elementor by https://elementor.com/
 *
 * @package SiteEditor
 * @subpackage admin
 * @since 1.1.0
 */
class SiteEditorAdminFeedback{

    /**
     * API URL
     *
     * @since 1.1.0
     * @access private
     * @var string
     * @static
     */
    private static $_api_url = 'http://www.siteeditor.org/?api_type=user_tracker';

    /**
     * API Feedback URL
     *
     * @since 1.1.0
     * @access private
     * @var string
     * @static
     */
    private static $_api_feedback_url = 'http://www.siteeditor.org/?api_type=user_feedback';


    /**
     * Site Editor Admin Manager Instance of SiteEditorAdminRender
     *
     * @since 1.1.0
     * @access private
     * @var object
     */
    private $manager;

    /**
     * SiteEditorAdminFeedback constructor.
     *
     * @since 1.1.0
     * @param $admin_manager object instance of SiteEditorAdminRender
     */
	public function __construct( $admin_manager ) {

        $this->manager = $admin_manager;

        /**
         * After Activate Plugin Show one dialog
         */
        add_action( 'admin_init', array( $this , 'allow_user_tracking' ) , 10  );

        /**
         * User tracker Dialog template print
         */
        add_action( 'admin_footer', array( $this , 'allow_user_tracking_template' )  );

        /**
         * After Save Site Editor settings
         */
        add_action( 'sed_after_admin_settings_save', array( $this , '_send_user_data' ) , 1000  );

        /**
         * In Cron Jobs: each once on week
         */
        add_action( 'sed_tracker_send_event' , array( $this , '_send_user_data' ) );

        /**
         * Show Admin Notic for User Tracker
         */
        add_action( 'admin_notices', array( $this , 'admin_notices' ) );

        /**
         * After Click Admin on notic
         */
        add_action( 'admin_init', array( $this , 'handle_tracker_actions' ) );

        /**
         * For Print deactivate feedback dialog template
         */
        add_action( 'admin_enqueue_scripts', array( $this , 'enqueue_scripts' ) );

        /**
         * Send skip or admin feedback when deactivate plugin with ajax
         */
        add_action( 'wp_ajax_sed_deactivate_feedback', array( $this, 'ajax_deactivate_feedback' ) );

        /**
         * Check for save or don't save allowe user tracking option for first time
         */
        add_action( 'sed_save_plugin_options_after', array( $this, 'is_checked_save_user_tracking' ) , 1000 ,1 );

    }

    /**
     * Check for save or don't save allow user tracking option for first time
     *
     * @since 1.1.0
     * @access public
     *
     */
    public function is_checked_save_user_tracking( $option ){

        $sed_init = sed_get_plugin_options( 'sed_init' );

        if( isset( $option['key'] ) && $option['key'] === "sed_allow_user_tracking" && $sed_init ){

            sed_save_plugin_options( '1' , 'sed_allow_user_tracking_changed');

        }

    }

    /**
     * allow user tracking form template
     *
     * @since 1.1.0
     * @access public
     *
     */
    public function allow_user_tracking_template( ){

        $sed_allow_user_tracking_changed = sed_get_plugin_options( 'sed_allow_user_tracking_changed' );

        if( $sed_allow_user_tracking_changed ){

            return ;

        }

        $screen = get_current_screen();

        if( in_array( $screen->id , array( 'siteeditor_page_site_editor_module' , 'toplevel_page_site_editor_index' ) ) ){

            include dirname( dirname( __FILE__ ) ) . "/templates/default/user_tracking.php";

        }

    }

    public function admin_notices() {

        // Show tracker notice after 24 hours from installed time.
        if ( self::_get_installed_time() > strtotime( '-24 hours' ) )
            return;

        if ( $this->_is_allow_track() )
            return;

        if ( ! current_user_can( 'manage_options' ) )
            return;

        //TODO: Skip for development env
        $optin_url = wp_nonce_url( add_query_arg( 'sed_user_tracker', 'opt_into' ), 'opt_into' );

        $optout_url = wp_nonce_url( add_query_arg( 'sed_user_tracker', 'opt_out' ), 'opt_out' );

        $tracker_description_text = __( 'Love using Site Editor? Become a super contributor by opting in to our anonymous plugin data collection and to our updates. We guarantee no sensitive data is collected.', 'site-editor' );

        $tracker_description_text = apply_filters( 'sed_tracker_admin_description_text', $tracker_description_text );

        include dirname( dirname( __FILE__ ) ) . "/templates/default/user_tracking_notic.php";
        
    }

    public function handle_tracker_actions() {

        if ( ! isset( $_GET['sed_user_tracker'] ) )
            return;

        if ( 'opt_into' === $_GET['sed_user_tracker'] ) {

            check_admin_referer( 'opt_into' );

            sed_save_plugin_options( 'yes' , 'sed_allow_user_tracking' );

            $this->_send_user_data( true );

        }

        if ( 'opt_out' === $_GET['sed_user_tracker'] ) {

            check_admin_referer( 'opt_out' );

            sed_save_plugin_options( 'skip' , 'sed_allow_user_tracking' );

        }

        wp_redirect( remove_query_arg( 'sed_user_tracker' ) );

        exit;

    }

    public function allow_user_tracking(){

        if( isset( $_POST['sed_user_tracking_allow_from_admin'] ) ){

            $allow_tracking = $_POST['sed_user_tracking_allow_from_admin'];

            if( in_array( $allow_tracking , array( "yes" , "skip" ) ) ){

                sed_save_plugin_options( $allow_tracking , 'sed_allow_user_tracking' );

                $this->_send_user_data( true );

            }

        }

    }

    public function _send_user_data( $override = false ){

        // Don't trigger this on AJAX Requests
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        if( ! $this->_is_allow_track() ){
            return ;
        }

        $last_send = self::_get_last_send_time();

        if ( ! apply_filters( 'sed_tracker_send_override', $override ) ) {
            // Send a maximum of once per week by default.
            if ( $last_send && $last_send > apply_filters( 'sed_tracker_last_send_interval', strtotime( '-1 week' ) ) ) {
                return;
            }
        } else {
            // Make sure there is at least a 1 hour delay between override sends, we dont want duplicate calls due to double clicking links.
            if ( $last_send && $last_send > strtotime( '-1 hours' ) ) {
                return;
            }
        }

        // Update time first before sending to ensure it is set
        sed_save_plugin_options( time() , 'sed_tracker_last_send' );

        // Send here..
        $params = array(
            'system'            => $this->_get_system_reports_data(),
            'site_lang'         => get_bloginfo( 'language' ),
            'email'             => get_option( 'admin_email' ),
            'is_first_time'     => empty( $last_send ),
        );

        $params = apply_filters( 'sed_send_tracking_data_params', $params );

        add_filter( 'https_ssl_verify', '__return_false' );

        $response = wp_safe_remote_post(
            self::$_api_url,
            array(
                'timeout'       => 25,
                'blocking'      => false,
                //'sslverify'   => false,
                'body'          => array(
                    'data'          => wp_json_encode( $params ),
                ),
            )
        );

    }

    private function _is_allow_track() {

        return 'yes' === sed_get_plugin_options( 'sed_allow_user_tracking' );

    }

    /**
     * Get the last time tracking data was sent.
     * @return int|bool
     */
    private static function _get_last_send_time() {

        return apply_filters( 'sed_tracker_last_send_time', sed_get_plugin_options( 'sed_tracker_last_send', '' ) );

    }

    private static function _get_installed_time() {

        $installed_time = sed_get_plugin_options( 'sed_installed_time' );

        if ( ! $installed_time ) {

            $installed_time = time();

            sed_save_plugin_options( $installed_time , 'sed_installed_time' );
        }

        return $installed_time;
    }
    
    private function _get_system_reports_data( ) {

        global $wpdb;

        if ($wpdb->use_mysqli) {
            $ver = mysqli_get_server_info($wpdb->dbh);
        } else {
            $ver = mysql_get_server_info();
        }

        $mysql_version = "N/A";

        if ( !empty($wpdb->is_mysql) && !stristr($ver, 'MariaDB') ){

            $mysql_version = $wpdb->db_version();

        }

        $active_theme = wp_get_theme();

        $theme_version = $active_theme->Version;

        $system_info = array(

            /**
             * WordPress Information
             */
            'wp_home'               => get_bloginfo( 'home' ) ,
            'wp_siteurl'            => get_bloginfo( 'siteurl' ) ,
            'wp_version'            => get_bloginfo( 'version' ) ,
            'wp_site_title'         => get_bloginfo( 'name' ) ,
            'wp_site_tagline'       => get_bloginfo( 'description' ) ,
            'wp_charset'            => get_bloginfo( 'charset' ) ,
            'wp_multisite'          => is_multisite() ,
            'wp_memory'             => $this->get_system_memory() ,
            'wp_debug'              => defined('WP_DEBUG') && WP_DEBUG ,
            'wp_cron'               => defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ,
            'wp_language'           => get_locale() ,

            /**
             * Server Information
             */
            'Server Info'           => esc_html($_SERVER['SERVER_SOFTWARE']) ,
            'phpversion'            => function_exists('phpversion') ? phpversion() : 'N/A',
            'post_max_size'         => size_format($this->memory_size_convert(ini_get('post_max_size'))) ,
            'max_execution_time'    => ini_get('max_execution_time') ,
            'max_input_vars'        => ini_get('max_input_vars') ,
            'curl_version'          => function_exists('curl_version') ? curl_version() : 'N/A' ,
            'suhosin'               => extension_loaded('suhosin') ? "yes" : "no" ,
            'mysql_version'         => $mysql_version ,
            'max_upload_size'       => size_format(wp_max_upload_size()) ,

            /**
             * WordPress Theme
             */
            'theme_name'            => $active_theme->Name ,
            'theme_version'         => $theme_version ,
            'theme_author'          => $active_theme->{'Author URI'} ,
            'is_child_theme'        => is_child_theme() ,

            /**
             * WordPress Active Plugins
             */
            'active_plugins'        => array(),

        );

        /**
         * WordPress Child Theme
         */
        if( is_child_theme() ) {

            $parent_theme = wp_get_theme($active_theme->Template);

            $system_info['parent_theme_name']       = $parent_theme->Name;
            $system_info['parent_theme_version']    = $parent_theme->Version;
            $system_info['parent_theme_author']     = $parent_theme->{'Author URI'};

        }

        /**
         * WordPress Active Plugins
         */
        $active_plugins = (array) get_option('active_plugins', array());

        if (is_multisite()) {
            $network_activated_plugins = array_keys(get_site_option('active_sitewide_plugins', array()));
            $active_plugins = array_merge($active_plugins, $network_activated_plugins);
        }

        foreach ($active_plugins as $plugin) {

            $plugin_data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);

            if ( ! empty($plugin_data['Name']) ) {

                $plugin_info = array();

                $plugin_info['plugin_name']             = esc_html( $plugin_data['Name'] );
                $plugin_info['plugin_URI']              = !empty( $plugin_data['PluginURI'] ) ? esc_url($plugin_data['PluginURI']) : "N/A";
                $plugin_info['plugin_version']          = !empty( $plugin_data['Version'] ) ? esc_html($plugin_data['Version']) : "N/A";

                $system_info['active_plugins'][] = $plugin_info;

            }

        }

        return $system_info;

    }

    private function get_system_memory(){

        $memory = $this->memory_size_convert( WP_MEMORY_LIMIT );

        if ( function_exists('memory_get_usage') ) {
            $system_memory = $this->memory_size_convert(@ini_get('memory_limit'));
            $memory = max($memory, $system_memory);
        }

        return size_format($memory);

    }

    private function memory_size_convert($size) {
        $l = substr($size, -1);
        $ret = substr($size, 0, -1);
        switch (strtoupper($l)) {
            case 'P':
                $ret *= 1024;
            case 'T':
                $ret *= 1024;
            case 'G':
                $ret *= 1024;
            case 'M':
                $ret *= 1024;
            case 'K':
                $ret *= 1024;
        }
        return $ret;
    }

    /**
     * Enqueue admin scripts.
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueue_scripts() {

        if( in_array( get_current_screen()->id, array( 'plugins', 'plugins-network' ) ) ){

            add_action( 'admin_footer', array( $this, 'print_deactivate_feedback_dialog' ) );

        }

    }

    public function print_deactivate_feedback_dialog() {

        $deactivate_reasons = array(

            'no_longer_needed' => array(
                'title' => __( 'I no longer need the plugin', 'site-editor' ),
                'input_placeholder' => '',
            ),

            'found_a_better_plugin' => array(
                'title' => __( 'I found a better plugin', 'site-editor' ),
                'input_placeholder' => __( 'Please share which plugin', 'site-editor' ),
            ),

            'couldnt_get_the_plugin_to_work' => array(
                'title' => __( 'I couldn\'t get the plugin to work', 'site-editor' ),
                'input_placeholder' => '',
            ),

            'temporary_deactivation' => array(
                'title' => __( 'It\'s a temporary deactivation', 'site-editor' ),
                'input_placeholder' => '',
            ),

            'other' => array(
                'title' => __( 'Other', 'site-editor' ),
                'input_placeholder' => __( 'Please share the reason', 'site-editor' ),
            ),

        );

        include dirname( dirname( __FILE__ ) ) . "/templates/default/deactivate_feedback.php";
        
    }

    public function ajax_deactivate_feedback() {

        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], '_sed_deactivate_feedback_nonce' ) ) {
            wp_send_json_error();
        }

        $reason_text = '';

        $reason_key = '';

        if ( ! empty( $_POST['reason_key'] ) )
            $reason_key = $_POST['reason_key'];

        if ( ! empty( $_POST[ "reason_{$reason_key}" ] ) )
            $reason_text = $_POST[ "reason_{$reason_key}" ];

        self::send_feedback( $reason_key, $reason_text );

        wp_send_json_success();
        
    }

    public static function send_feedback( $feedback_key, $feedback_text ) {

        return wp_remote_post( self::$_api_feedback_url, array(
            'timeout' => 30,
            'body' => array(
                'api_version'   => SED_VERSION,
                'site_lang'     => get_bloginfo( 'language' ),
                'feedback_key'  => $feedback_key,
                'feedback'      => $feedback_text,
            ),
        ) );

    }

}