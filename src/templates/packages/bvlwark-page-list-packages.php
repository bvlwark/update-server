<?php

use BVLWARK\UpdateServer\Tables\PackagesTable;

defined( 'ABSPATH' ) || exit;

/**
 * Available variables.
 *
 * @var PackagesTable $packages        The BVLWARK package list object.
 * @var string        $add_package_url URL for the "Add package" page.
 */

?>

<div class="wrap bvlwark">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Packages', 'bvlwark-update-server' ); ?></h1>

	<a class="page-title-action" href="<?php echo esc_url( $add_package_url ); ?>">
		<span><?php esc_html_e( 'Add new', 'bvlwark-update-server' ); ?></span>
	</a>

	<hr class="wp-header-end">

	<form method="post" id="bvlwark-packages-table">
		<?php
		$packages->prepare_items();
		$packages->views();
		$packages->search_box( __( 'Search packages', 'bvlwark-update-server' ), 'package' );
		$packages->display();
		?>
	</form>
</div>