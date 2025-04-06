<?php

use BVLWARK\UpdateServer\Tables\ItemsTable;

defined( 'ABSPATH' ) || exit;

/**
 * Available variables.
 *
 * @var ItemsTable $items        The BVLWARK Item list object.
 * @var string     $add_item_url URL for the "Add item" page.
 */

?>

<div class="wrap bvlwark">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Items', 'bvlwark-update-server' ); ?></h1>

	<a class="page-title-action" href="<?php echo esc_url( $add_item_url ); ?>">
		<span><?php esc_html_e( 'Add new', 'bvlwark-update-server' ); ?></span>
	</a>

	<hr class="wp-header-end">

	<form method="post" id="bvlwark-license-table">
		<?php
		$items->prepare_items();
		$items->views();
		$items->search_box( __( 'Search items', 'bvlwark-update-server' ), 'item' );
		$items->display();
		?>
	</form>
</div>