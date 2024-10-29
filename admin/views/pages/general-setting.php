<?php
use WP_Arvan\Engine\API\VOD\Channels;

$channels = (new Channels)->get_channels();

?>


<div class="wrap">

	<h1><?php echo esc_html_e( 'Channels', 'arvancloud-vod' ) ?></h1>
	<div class="arvan-vod-wrapper">
		<div class="arvan-vod-card">
			<div style="display: flex;align-items: center;justify-content: space-between;">
				<h3><?php echo esc_html_e( 'Select default channel', 'arvancloud-vod' ) ?></h3>
				<a class="button" href="<?php echo add_query_arg(
					array(
						'page' => 'arvancloud-vod',
						'action' => 'config-api'
					),
					esc_url(admin_url( 'admin.php' ))
				); ?>"><?php _e( 'Change API Key', 'arvancloud-vod' ) ?></a>
			</div>
			<form class="arvancloud-vod-config-form selected_channel" method="post" action="<?php echo esc_url(admin_url( '/admin.php?page=arvancloud-vod' )); ?>">
				<div class="arvancloud-vod-config-form-row">
					<?php
						if ( ! empty( $channels ) ) {
							?>
							<label for="selected_channel"><?php echo esc_html_e( 'Select default channel (ArvanCloud Video Channel for Uploading Videos)', 'arvancloud-vod' ) ?></label>
							<select name="selected_channel" id="selected_channel">
								<?php
								foreach ( $channels as $channel ) {
									?>
									<option value="<?php echo esc_attr( $channel['id'] ) ?>" <?php echo esc_attr( $channel['id'] ) == get_option( 'arvan-cloud-vod-selected_channel_id' ) ? 'selected' : '' ?>>
										<?php echo esc_html( $channel['title'] );
										echo strlen($channel['description']) > 0 ? '(' . esc_html( $channel['description'] ) . ')' : ''; ?>
									</option>
									<?php
								}
								?>
							</select>

							<?php
						}
						?>
				</div>
				<?php
				$is_prevent_saving_video_on_local_checked = get_option('vod_prevent_saving_video_on_local', 'no');
				?>
				<div class="arvancloud-vod-config-form-row prevent-saving-video-on-local">
					<label for="prevent_saving_video_on_local"><?php _e('Prevent saving video files on local', 'arvancloud-vod') ?></label>
					<input type="hidden" name="prevent_saving_video_on_local" value="no">
					<input type="checkbox" name="prevent_saving_video_on_local" id="prevent_saving_video_on_local" value="yes" <?php echo ( 'yes' == $is_prevent_saving_video_on_local_checked)?'checked=checked':''; ?>>
					<p><?php _e('The files should be deleted from the local host after uploading them to the Arvan video service.', 'arvancloud-vod'); ?></p>
				</div>
				<button type="submit" class="button button-primary" name="config_arvancloud_vod_selected_channel" value="1"><?php echo esc_html_e( "Save", 'arvancloud-vod' ) ?></button>
			</form>
		</div>
	</div>


    <br>
    <br>
    <br>
    <?php require_once( ACVOD_PLUGIN_ROOT . 'admin/views/components/footer.php' ); ?>
</div>
