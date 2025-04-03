<?php

namespace BVLWARK\UpdateServer\Repositories;

defined( 'ABSPATH' ) || exit;

abstract class ColumnTypeEnum {
	/**
	 * @var string
	 */
	const string INT = 'INT';

	/**
	 * @var string
	 */
	const string TINYINT = 'TINYINT';

	/**
	 * @var string
	 */
	const string BIGINT = 'BIGINT';

	/**
	 * @var string
	 */
	const string CHAR = 'CHAR';

	/**
	 * @var string
	 */
	const string VARCHAR = 'VARCHAR';

	/**
	 * @var string
	 */
	const string LONGTEXT = 'LONGTEXT';

	/**
	 * @var string
	 */
	const string TEXT = 'TEXT';

	/**
	 * @var string
	 */
	const string DATETIME = 'DATETIME';
}
