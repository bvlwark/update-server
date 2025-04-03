<?php

namespace BVLWARK\UpdateServer\Repositories;

use BVLWARK\UpdateServer\Installer;
use BVLWARK\UpdateServer\Models\ItemVersionModel;

defined( 'ABSPATH' ) || exit;

class ItemVersionRepository extends AbstractRepository {
	/**
	 * ItemVersionRepository constructor.
	 */
	public function __construct() {
		global $wpdb;

		$this->table   = $wpdb->prefix . Installer::ITEM_TABLE_NAME;
		$this->model   = ItemVersionModel::class;
		$this->mapping = array(
			'item_id'     => ColumnTypeEnum::BIGINT,
			'version'     => ColumnTypeEnum::VARCHAR,
			'requires'    => ColumnTypeEnum::VARCHAR,
			'tested'      => ColumnTypeEnum::VARCHAR,
			'description' => ColumnTypeEnum::TEXT,
			'changelog'   => ColumnTypeEnum::TEXT,
			'protected'   => ColumnTypeEnum::TINYINT,
		);
	}
}
