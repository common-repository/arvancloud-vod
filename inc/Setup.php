<?php
namespace WP_Arvan\Engine;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.0.1
 * @package   ArvanCloud_VOD
 * @author    Khorshid, ArvanCloud <info@khorshidlab.com>
 * @license   GPL-3.0+
 * @link      https://www.arvancloud.ir/en/products/video-platform
 */
use WP_Arvan\Admin\Enqueue;
use WP_Arvan\Engine\API\VOD\GeneralSettings;
use WP_Arvan\Engine\API\VOD_Key;
use WP_Arvan\Engine\API\VOD\Channels;
use WP_Arvan\Engine\VOD\Assets;
use WP_Arvan\Engine\VOD\Assets\Media_Action;
use WP_Arvan\Engine\VOD\Assets\Add_Video;
use WP_Arvan\Engine\VOD\Assets\Tinymce_plugin;
use WP_Arvan\Engine\VOD\Widgets\Video_Links;

class Setup {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 *
	 * @since    0.0.1
	 */
	public function __construct() {

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->register_shortcodes();

		(new Enqueue)->initialize();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function load_dependencies() {


		$this->loader = new Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the load_plugin_textdomain function in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function set_locale() {

		$VOD_Key = new VOD_Key;
		$Channels = new Channels;
		$Assets = new Assets;
		$Media_Action = new Media_Action;
		$Add_Video = new Add_Video;
		$Tinymce_plugin = new Tinymce_plugin;
		$Video_Links = new Video_Links;

		$this->loader->add_action( 'plugins_loaded', $this, 'load_plugin_textdomain' );
		$this->loader->add_action( 'admin_init', $VOD_Key, 'set_acvod_api_key',10 );
		$this->loader->add_action( 'admin_init', $Channels, 'set_default_channel',11 );
		$this->loader->add_action( 'admin_init', $Media_Action, 'process_media_actions' );
		$this->loader->add_action( 'admin_init', $Add_Video, 'get_not_uploaded_videos' );
		$this->loader->add_action( 'admin_notices', $Media_Action, 'maybe_display_media_action_message' );

		if ( self::is_plugin_has_selected_channel() ) {
			$this->loader->add_filter( 'bulk_actions-upload', $Media_Action, 'bulk_actions_upload' );
			$this->loader->add_filter( 'bulk_actions-toplevel_page_arvancloud-vod-videos', $Media_Action, 'bulk_actions_upload' );
			$this->loader->add_filter( 'media_row_actions', $Media_Action, 'add_media_row_actions', 10, 3 );
			$this->loader->add_action( 'wp_ajax_aco_get_attachment_provider_details', $Media_Action, 'ajax_get_attachment_provider_details' );
		}

		$this->loader->add_action( 'pre_get_posts', $Assets, 'filter_media_library_with_videos', 1, 1);
		$this->loader->add_filter( 'plupload_init', $Add_Video, 'change_allowed_mime_types' );
		$this->loader->add_action( 'admin_init', $Tinymce_plugin, 'custom_mce_buttons' );
		$this->loader->add_action( 'after_wp_tiny_mce', $Tinymce_plugin, 'tinymce_extra_vars' );
		$this->loader->add_action( 'add_meta_boxes_attachment', $Video_Links, 'add_meta_boxes' );
		$this->loader->add_filter( 'wp_get_attachment_url', $Assets, 'media_library_url_rewrite', 10, 2 );
		$this->loader->add_filter( 'wp_update_attachment_metadata', $Assets, 'wp_update_attachment_metadata', 10, 2 );
		$this->loader->add_filter( 'the_title', $Assets, 'media_library_title_rewrite', 10, 2);


		$general_settings = GeneralSettings::get_instance();
		$this->loader->add_action( 'admin_init', $general_settings, 'set_prevent_saving_video_on_local_status' );


		$Video_Hooks = new Assets\Video_Hooks();
		$this->loader->add_filter( 'wp_prepare_attachment_for_js', $Video_Hooks, 'rewrite_video_thumbnail', 10, 2);

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_admin_hooks() {

		$this->loader->add_action( 'admin_menu', $this, 'setup_admin_menu' );
		//$this->loader->add_action( 'admin_menu', $this, 'setup_admin_menu' );


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.0.1
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.0.1
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'arvancloud-vod',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	public function setup_admin_menu() {

		if ( defined('ACVOD_PLUGIN_STATUS') && ACVOD_PLUGIN_STATUS ) {

			add_menu_page(
				__( 'ArvanCloud Videos', 'arvancloud-vod' ),
				__( 'Arvan VoD', 'arvancloud-vod'),
				'manage_options',
				'arvancloud-vod' . '-videos',
				[$this, 'video_library_page'],
				ACVOD_PLUGIN_ROOT_URL . 'assets/images/arvancloud-logo.svg',
				10
			);

			add_submenu_page(
				'arvancloud-vod' . '-videos',
				__( 'Video Library', 'arvancloud-vod' ),
				__( 'Library', 'arvancloud-vod' ),
				'manage_options',
				'arvancloud-vod' . '-videos',
				[$this, 'video_library_page'],
			);

			add_submenu_page(
				'arvancloud-vod' . '-videos',
				__( 'Upload New Video', 'arvancloud-vod' ),
				__( 'Add', 'arvancloud-vod' ),
				'manage_options',
				'arvancloud-vod' . '-videos-add',
				[$this, 'add_video_page'],
			);

			add_submenu_page(
				'arvancloud-vod' . '-videos',
				__( 'Setting', 'arvancloud-vod' ),
				__( 'Setting', 'arvancloud-vod' ),
				'manage_options',
				'arvancloud-vod',
				[$this, 'settings_page'],
			);

			add_submenu_page(
				'arvancloud-vod' . '-videos',
				__( 'About ArvanCloud', 'arvancloud-vod' ),
				__( 'About', 'arvancloud-vod' ),
				'manage_options',
				'arvancloud-vod' . '-about',
				[$this, 'about_us_page'],
			);

		} else {
			add_menu_page(
				__( 'ArvanCloud Videos', 'arvancloud-vod' ),
				__( 'Arvan VoD', 'arvancloud-vod'),
				'manage_options',
				'arvancloud-vod',
				[$this, 'settings_page'],
				ACVOD_PLUGIN_ROOT_URL . 'assets/images/arvancloud-logo.svg',
				10
			);

			add_submenu_page(
				'arvancloud-vod',
				__( 'Setting', 'arvancloud-vod' ),
				__( 'Setting', 'arvancloud-vod' ),
				'manage_options',
				'arvancloud-vod',
				[$this, 'settings_page'],
			);

			add_submenu_page(
				'arvancloud-vod',
				__( 'About ArvanCloud', 'arvancloud-vod' ),
				__( 'About', 'arvancloud-vod' ),
				'manage_options',
				'arvancloud-vod' . '-about',
				[$this, 'about_us_page'],
			);
		}

		// if is edit attachment page
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
			global $pagenow;
			$post_ID = sanitize_text_field( $_GET['post'] );
			if( in_array( $pagenow, array( 'post.php') ) && get_post_type( $post_ID ) === 'attachment') {
				(new Add_Video)->maybe_update_video();
			}
		}

	}

	public function settings_page() {
		$vod_status = get_option('arvan-cloud-vod-status');
		$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : false;
		if (  empty($vod_status) || ($vod_status != 'connected' && $vod_status != 'connected-channel') || $action == 'config-api' ) {
			require_once( ACVOD_PLUGIN_ROOT . 'admin/views/pages/api-key-settings.php' );
		} else {
			require_once( ACVOD_PLUGIN_ROOT . 'admin/views/pages/general-setting.php' );
		}
	}

	public function about_us_page() {
		require_once( ACVOD_PLUGIN_ROOT . 'admin/views/pages/about-us.php' );
	}

	public function video_library_page() {
		require_once( ACVOD_PLUGIN_ROOT . 'admin/views/pages/video-library.php' );
	}

	public function add_video_page() {
		require_once( ACVOD_PLUGIN_ROOT . 'admin/views/pages/add-video.php' );
	}


	public static function is_plugin_has_selected_channel() {
		$vod_status = get_option('arvan-cloud-vod-status');
		
		if(isset($_POST[ 'selected_channel' ]) and $vod_status == 'connected'){
			Channels::set_default_channel();
			$vod_status = get_option('arvan-cloud-vod-status');
		}

		if (  empty($vod_status) || ($vod_status != 'connected-channel') || empty(get_option('arvan-cloud-vod-selected_channel_id')) ) {
			return false;
		} else {
			return true;
		}
	}

	public function register_shortcodes() {
		add_shortcode('r1c-vod', [$this, 'r1c_vod_shortcode']);
	}

	public function r1c_vod_shortcode( $atts ) {

		$atts = shortcode_atts( array(
			'video_post_id' => null,
		), $atts, 'r1c-vod' );


		if (empty($atts['video_post_id'])) {
			return;
		}

		$video_data = get_post_meta($atts['video_post_id'], 'acv_video_data', true);

		if (! empty($video_data) ) {
			$response = '<div class="wp-block-r1c-vod-select">
				<div class="r1_iframe_embed">
					<iframe src="'. $video_data['player_url'] .'" style="border:0 #ffffff none;" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowFullScreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
				</div>
			</div>';
		} else {
			$url = wp_get_attachment_url($atts['video_post_id']);
			$file_format = pathinfo($url, PATHINFO_EXTENSION);
			$response = do_shortcode('[video width="640" height="360" '. $file_format .'="'. $url .'"][/video]');
		}

		return $response;
	}

}
