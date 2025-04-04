<?php defined( 'ABSPATH' ) || exit; ?>

<div class="wrap bvlwark">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Add an item', 'bvlwark-update-server' ); ?>
	</h1>
	<hr class="wp-header-end">

	<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="bvlwark_update_server_add_item">
		<?php wp_nonce_field( 'bvlwark_update_server_add_item' ); ?>

		<table class="form-table">
			<tbody>

			</tbody>
		</table>

		<?php submit_button( __( 'Add', 'bvlwark-update-server' ) ); ?>
	</form>
</div>