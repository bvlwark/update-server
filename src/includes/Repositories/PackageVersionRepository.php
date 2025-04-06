<?php

namespace BVLWARK\UpdateServer\Repositories;

use BVLWARK\UpdateServer\Installer;
use BVLWARK\UpdateServer\Models\PackageVersionModel;

defined( 'ABSPATH' ) || exit;

class PackageVersionRepository extends AbstractRepository {
	/**
	 * PackageVersionRepository constructor.
	 */
	public function __construct() {
		global $wpdb;

		$this->table   = $wpdb->prefix . Installer::PACKAGE_TABLE_NAME;
		$this->model   = PackageVersionModel::class;
		$this->mapping = array(
			'package_id' => ColumnTypeEnum::BIGINT,
			'version'    => ColumnTypeEnum::VARCHAR,
			'requires'   => ColumnTypeEnum::VARCHAR,
			'tested'     => ColumnTypeEnum::VARCHAR,
			'changelog'  => ColumnTypeEnum::LONGTEXT,
			'protected'  => ColumnTypeEnum::TINYINT,
		);
	}
}
