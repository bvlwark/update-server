<?php

namespace BVLWARK\UpdateServer\Repositories;

use BVLWARK\UpdateServer\Installer;
use BVLWARK\UpdateServer\Models\ApiKeyModel;

defined( 'ABSPATH' ) || exit;

class ApiKeyRepository extends AbstractRepository {
	/**
	 * ApiKeyRepository constructor.
	 */
	public function __construct() {
		global $wpdb;

		$this->table   = $wpdb->prefix . Installer::API_KEY_TABLE_NAME;
		$this->model   = ApiKeyModel::class;
		$this->mapping = array(
			'user_id'         => ColumnTypeEnum::BIGINT,
			'description'     => ColumnTypeEnum::VARCHAR,
			'permissions'     => ColumnTypeEnum::VARCHAR,
			'consumer_key'    => ColumnTypeEnum::CHAR,
			'consumer_secret' => ColumnTypeEnum::CHAR,
			'nonces'          => ColumnTypeEnum::LONGTEXT,
			'truncated_key'   => ColumnTypeEnum::CHAR,
			'last_access'     => ColumnTypeEnum::DATETIME,
		);
	}
}
