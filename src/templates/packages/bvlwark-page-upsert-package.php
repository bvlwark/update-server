<?php

use BVLWARK\UpdateServer\Models\PackageModel;
use BVLWARK\UpdateServer\PackageTypeEnum;

defined( 'ABSPATH' ) || exit;

/**
 * Available variables
 *
 * @var int|null          $package_id The BVLWARK package ID.
 * @var PackageModel|null $package    The BVLWARK package model object.
 */

?>

<div class="wrap bvlwark">
	<h1 class="wp-heading-inline">
		<?php if ( $package_id ) : ?>
			<?php esc_html_e( 'Edit a package', 'bvlwark-update-server' ); ?>
		<?php else : ?>
			<?php esc_html_e( 'Add a package', 'bvlwark-update-server' ); ?>
		<?php endif; ?>
	</h1>
	<hr class="wp-header-end">

	<form method="post" enctype="multipart/form-data" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="bvlwark_add_package">
		<?php if ( $package_id ) : ?>
			<input type="hidden" name="package_id" value="<?php echo esc_attr( $package_id ); ?>" />
		<?php endif; ?>

		<?php wp_nonce_field( 'bvlwark_add_package' ); ?>

		<table class="form-table">
			<tbody>

			<?php
			bvlwark_render_admin_input(
				array(
					'id'          => 'name',
					'label'       => __( 'Name', 'bvlwark-update-server' ),
					'required'    => true,
					'description' => __( 'The name of the theme/plugin', 'bvlwark-update-server' ),
					'placeholder' => 'The Amazing Plugin',
					'value'       => $package ? $package->get_name() : '',
				)
			);

			bvlwark_render_admin_input(
				array(
					'id'          => 'slug',
					'label'       => __( 'Slug', 'bvlwark-update-server' ),
					'required'    => true,
					'description' => __( 'The slug, or technical name of the theme/plugin', 'bvlwark-update-server' ),
					'placeholder' => 'the-amazing-plugin',
					'value'       => $package ? $package->get_slug() : '',
				)
			);

			bvlwark_render_admin_select(
				array(
					'id'       => 'type',
					'label'    => __( 'Type', 'bvlwark-update-server' ),
					'required' => true,
					'options'  => PackageTypeEnum::get_options(),
					'value'    => $package ? $package->get_type() : PackageTypeEnum::PLUGIN,
				)
			);

			bvlwark_render_admin_rich_text(
				array(
					'id'    => 'description',
					'label' => __( 'Description', 'bvlwark-update-server' ),
					'value' => $package ? $package->get_description() : '',
				)
			);

			bvlwark_render_admin_input(
				array(
					'id'          => 'is_public',
					'label'       => __( 'Public?', 'bvlwark-update-server' ),
					'type'        => 'checkbox',
					'description' => __( 'Is the theme or plugin available for public download?', 'bvlwark-update-server' ),
					'value'       => $package ? $package->is_public() : false,
				)
			);
			?>

			</tbody>
		</table>

		<?php submit_button( __( 'Submit', 'bvlwark-update-server' ) ); ?>
	</form>
</div>