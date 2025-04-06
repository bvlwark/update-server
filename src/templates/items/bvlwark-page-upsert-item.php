<?php

use BVLWARK\UpdateServer\ItemTypeEnum;
use BVLWARK\UpdateServer\Models\ItemModel;

defined( 'ABSPATH' ) || exit;

/**
 * Available variables
 *
 * @var int|null       $item_id
 * @var ItemModel|null $item
 */

?>

<div class="wrap bvlwark">
	<h1 class="wp-heading-inline">
		<?php if ( $item_id ) : ?>
			<?php esc_html_e( 'Edit an item', 'bvlwark-update-server' ); ?>
		<?php else : ?>
			<?php esc_html_e( 'Add an item', 'bvlwark-update-server' ); ?>
		<?php endif; ?>
	</h1>
	<hr class="wp-header-end">

	<form method="post" enctype="multipart/form-data" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="bvlwark_update_server_add_item">
		<?php if ( $item_id ) : ?>
			<input type="hidden" name="item_id" value="<?php echo esc_attr( $item_id ); ?>" />
		<?php endif; ?>

		<?php wp_nonce_field( 'bvlwark_update_server_add_item' ); ?>

		<table class="form-table">
			<tbody>

			<?php
			bvlwark_render_admin_input(
				array(
					'id'          => 'name',
					'label'       => __( 'Name', 'bvlwark-update-server' ),
					'required'    => true,
					'description' => __( 'The name of the theme/plugin', 'bvlwark-update-server' ),
					'placeholder' => 'License Manager for WooCommerce',
					'value'       => $item ? $item->get_name() : '',
				)
			);

			bvlwark_render_admin_input(
				array(
					'id'          => 'slug',
					'label'       => __( 'Slug', 'bvlwark-update-server' ),
					'required'    => true,
					'description' => __( 'The slug, or technical name of the theme/plugin', 'bvlwark-update-server' ),
					'placeholder' => 'license-manager-for-woocommerce',
					'value'       => $item ? $item->get_slug() : '',
				)
			);

			bvlwark_render_admin_select(
				array(
					'id'       => 'type',
					'label'    => __( 'Type', 'bvlwark-update-server' ),
					'required' => true,
					'options'  => ItemTypeEnum::get_options(),
					'value'    => $item ? $item->get_type() : ItemTypeEnum::PLUGIN,
				)
			);

			bvlwark_render_admin_rich_text(
				array(
					'id'    => 'description',
					'label' => __( 'Description', 'bvlwark-update-server' ),
					'value' => $item ? $item->get_description() : '',
				)
			);

			bvlwark_render_admin_input(
				array(
					'id'          => 'is_public',
					'label'       => __( 'Public?', 'bvlwark-update-server' ),
					'type'        => 'checkbox',
					'description' => __( 'Is the theme or plugin available for public download?', 'bvlwark-update-server' ),
					'value'       => $item ? $item->is_public() : false,
				)
			);
			?>

			</tbody>
		</table>

		<?php submit_button( __( 'Submit', 'bvlwark-update-server' ) ); ?>
	</form>
</div>