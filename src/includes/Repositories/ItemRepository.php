<?php

namespace BVLWARK\UpdateServer\Repositories;

use BVLWARK\UpdateServer\Installer;
use BVLWARK\UpdateServer\Models\ItemModel;

defined( 'ABSPATH' ) || exit;

class ItemRepository extends AbstractRepository {
	/**
	 * ItemRepository constructor.
	 */
	public function __construct() {
		global $wpdb;

		$this->table   = $wpdb->prefix . Installer::ITEM_TABLE_NAME;
		$this->model   = ItemModel::class;
		$this->mapping = array(
			'name'        => ColumnTypeEnum::VARCHAR,
			'slug'        => ColumnTypeEnum::VARCHAR,
			'type'        => ColumnTypeEnum::VARCHAR,
			'description' => ColumnTypeEnum::TEXT,
			'protected'   => ColumnTypeEnum::TINYINT,
		);
	}
}
