<?php

namespace WP_Arvan\Engine\VOD\Assets;

class Video_Hooks
{
	public function rewrite_video_thumbnail($response){
		$id = $response['id'];
		if ( current_user_can( 'manage_options' ) && get_post_type($id) == 'attachment' && !empty(get_post_meta( $id, 'acv_video_data', true )) ) {

			$response['thumb']['src'] = ACVOD_PLUGIN_ROOT_URL . 'assets/images/arvancloud-logo.svg';
			$response['image']['src'] = ACVOD_PLUGIN_ROOT_URL . 'assets/images/arvancloud-logo.svg';

			return $response;
		}


		return $response;

	}
}
