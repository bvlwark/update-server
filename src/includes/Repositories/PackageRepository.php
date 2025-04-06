<?php

namespace BVLWARK\UpdateServer\Repositories;

use BVLWARK\UpdateServer\Installer;
use BVLWARK\UpdateServer\Models\PackageModel;

defined( 'ABSPATH' ) || exit;

class PackageRepository extends AbstractRepository {
	/**
	 * PackageRepository constructor.
	 */
	public function __construct() {
		global $wpdb;

		$this->table   = $wpdb->prefix . Installer::PACKAGE_TABLE_NAME;
		$this->model   = PackageModel::class;
		$this->mapping = array(
			'name'        => ColumnTypeEnum::VARCHAR,
			'slug'        => ColumnTypeEnum::VARCHAR,
			'type'        => ColumnTypeEnum::VARCHAR,
			'description' => ColumnTypeEnum::TEXT,
			'is_public'   => ColumnTypeEnum::TINYINT,
		);
	}
}
