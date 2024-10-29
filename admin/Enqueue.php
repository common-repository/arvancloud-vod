<?php

/**
 * ArvanCloud_VOD
 *
 * @package   ArvanCloud_VOD
 * @author    Khorshid, ArvanCloud <{{author_email}}>
 * @copyright {{author_copyright}}
 * @license   GPL-3.0+
 * @link      https://www.arvancloud.ir/en/products/video-platform
 */

namespace WP_Arvan\Admin;

use WP_Arvan\Engine\API\VOD_Key;
use WP_Arvan\Engine\VOD\Assets;

/**
 * This class contain the Enqueue stuff for the backend
 */
class Enqueue {

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize() {

		add_action('admin_enqueue_scripts',[$this,'override_delete_media_alert']);

		if ( defined('ACVOD_PLUGIN_STATUS') && ACVOD_PLUGIN_STATUS ) {

			\add_action('init', function () {
				register_block_type(
					'r1c/vod-select', array(
						// Enqueue blocks.style.build.css on both frontend & backend.
						'style'         => 'arvancloud-vod' . '-block-style-css',
						// Enqueue blocks.build.js in the editor only.
						'editor_script' => 'arvancloud-vod' . '-block-js',
						// Enqueue blocks.editor.build.css in the editor only.
						'editor_style'  => 'arvancloud-vod' . '-block-editor-css',
						'attributes'      => array(
							'videoId'            => array(
								'type' => 'string',
							),
						),
					)
				);
			});

		}

		\add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		\add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		\add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		add_filter("media_view_strings", function($strings){
			$strings['warnDelete'] = __("You are about to permanently delete this item from your site.\nAlso, you are deleting this video from your local storage, this video is still available on Arvan Video Platform Storage.\nThis action cannot be undone.\n 'Cancel' to stop, 'OK' to delete.",'arvancloud-vod');
			return $strings;
		});

	}


	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function enqueue_admin_styles() {

		\wp_enqueue_style( 'arvancloud-vod' . '-admin-styles', \plugins_url( 'assets/css/admin.css', ACVOD_PLUGIN_ABSOLUTE ), array( 'dashicons' ), ACVOD_VERSION );


		$style = ".attachment-preview.type-video img.thumbnail { width:50px; }";

		wp_add_inline_style('arvancloud-vod-admin-styles', $style);

		// enqueue if is rtl
		if ( \is_rtl() ) {
			\wp_enqueue_style( 'arvancloud-vod' . '-admin-rtl-styles', \plugins_url( 'assets/css/admin-rtl.css', ACVOD_PLUGIN_ABSOLUTE ), array( 'dashicons' ), ACVOD_VERSION );
		}

		if ( get_locale() == 'fa_IR' && explode('_', \get_current_screen()->id)[0] == '%d9%88%db%8c%d8%af%d8%a6%d9%88%d9%87%d8%a7' ) {
			add_action('admin_head', function() {
				echo '<style>.drag-drop #drag-drop-area { border: 4px dashed #00baba; height: 200px;} </style>';
			});
		}


	}

	public function enqueue_styles() {
		\wp_enqueue_style( 'arvancloud-vod' . '-front-styles', \plugins_url( 'assets/css/front.css', ACVOD_PLUGIN_ABSOLUTE ), array(), ACVOD_VERSION );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since
	 * @return void
	 */
	public function enqueue_admin_scripts() {

		$screen = \get_current_screen();

		\wp_enqueue_script(
			'arvancloud-vod' . '-admin-scripts',
			\plugins_url( 'assets/js/admin.js', ACVOD_PLUGIN_ABSOLUTE ),
			array( 'jquery', 'media-grid', 'media' ),
			ACVOD_VERSION,
			true
		);

		if( isset($_GET['page']) &&  strstr($_GET['page'], 'arvancloud-vod-videos') != false ){

			\wp_enqueue_script(
				'arvancloud-vod' . '-upload-vod-redirect',
				\plugins_url( 'assets/js/vod_upload.js', ACVOD_PLUGIN_ABSOLUTE ),
				array( 'jquery', 'media-grid', 'media', 'plupload' ),
				ACVOD_VERSION,
				true
			);
		}
		wp_localize_script(
			'arvancloud-vod' . '-admin-scripts',
			'AR_VOD',
			array(
				'videoGallery' => \admin_url( 'admin.php?page=arvancloud-vod-videos' ),
				'strings'	  => array(
					'video_upload_error' => __( 'you are not allowed to upload this file type.', 'arvancloud-vod' ),
					'copy_to_vod' => __( 'Copy to ArvanVOD', 'arvancloud-vod' ),
				),
				'nonces'  => array(
					'get_attachment_provider_details' => wp_create_nonce( 'get-attachment-vod-details' ),
				),
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
			)
		);



		if ( $screen->id === 'videos_page_arvancloud-vod-videos-add' ||
			get_locale() == 'fa_IR' && explode('_', $screen->id)[0] == '%d9%88%db%8c%d8%af%d8%a6%d9%88%d9%87%d8%a7' ) {
			\wp_enqueue_script(
				'arvancloud-vod' . '-upload-scripts',
				\plugins_url( 'assets/js/vod_upload.js', ACVOD_PLUGIN_ABSOLUTE ),
				array( 'jquery', 'media-grid', 'media', 'plupload' ),
				ACVOD_VERSION,
				true
			);
		}


		if ( defined('ACVOD_PLUGIN_STATUS') && ACVOD_PLUGIN_STATUS ) {

			\add_action('init', function () {
				register_block_type(
					'r1c/vod-select', array(
						// Enqueue blocks.style.build.css on both frontend & backend.
						'style'         => 'arvancloud-vod' . '-block-style-css',
						// Enqueue blocks.build.js in the editor only.
						'editor_script' => 'arvancloud-vod' . '-block-js',
						// Enqueue blocks.editor.build.css in the editor only.
						'editor_style'  => 'arvancloud-vod' . '-block-editor-css',
						'attributes'      => array(
							'videoId'            => array(
								'type' => 'string',
							),
						),
					)
				);
			});

			\wp_register_script(
				'arvancloud-vod' . '-block-js', // Handle.
				plugins_url( '/assets/js/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
				array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
				null,
				true
			);

			wp_register_style(
				'arvancloud-vod' . '-block-editor-css', // Handle.
				plugins_url( 'assets/css/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
				array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
				null // filemtime( plugin_dir_path( __DIR__ ) . 'assets/css/blocks.editor.build.css' ) // Version: File modification time.
			);

			\wp_register_style(
				'arvancloud-vod' . '-block-style-css', // Handle.
				plugins_url( 'assets/css/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
				is_admin() ? array( 'wp-editor' ) : null, // Dependency to include the CSS after it.
				null
			);

			wp_localize_script(
				'arvancloud-vod' . '-block-js',
				'r1cGlobal', // Array containing dynamic data for a JS Global.
				[
					'pluginDirPath' => plugin_dir_path( __DIR__ ),
					'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
					'arvanVideos'	=> (new Assets)->get_all_videos(),
					// Add more data here that you want to access from `r1cGlobal` object.
				]
			);

		}

		if(VOD_Key::validate_api_key()){
			wp_localize_script(
				'arvancloud-vod' . '-admin-scripts',
				'vod_is_api_valid', // Array containing dynamic data for a JS Global.
				[
					'value'=>true
				]
			);
		}

	}

	public function override_delete_media_alert(){
		wp_register_script( 'vod-override-delete-alert', '' );
		wp_enqueue_script( 'vod-override-delete-alert' ,null,null,null,true);

		$script = '<script>window.showNotice.warn = function() { if ( confirm( "'.__("You are about to permanently delete this item from your site.\\nAlso, you are deleting this video from your local storage, this video is still available on Arvan Video Platform Storage.\\nThis action cannot be undone.\\n 'Cancel' to stop, 'OK' to delete.",'arvancloud-vod').'" ) ) { return true;	} return false;	}</script>';

		wp_add_inline_script('vod-override-delete-alert' , $script );
	}

}
