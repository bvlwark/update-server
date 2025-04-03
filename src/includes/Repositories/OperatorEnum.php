<?php

namespace BVLWARK\UpdateServer\Repositories;

use ReflectionClass;

defined( 'ABSPATH' ) || exit;

abstract class OperatorEnum {
	const string AND      = 'AND';
	const string OR       = 'OR';
	const string IN       = 'IN';
	const string NOT_IN   = 'NOT IN';
	const string NOT_LIKE = 'NOT LIKE';
	const string LIKE     = 'LIKE';
	const string BETWEEN  = 'BETWEEN';

	/**
	 * Returns class constants.
	 *
	 * @return array
	 */
	public static function get_keys(): array {
		$reflection_class = new ReflectionClass( __CLASS__ );

		return array_keys( $reflection_class->getConstants() );
	}

	/**
	 * Returns class constants.
	 *
	 * @return array
	 */
	public static function get_values(): array {
		$reflection_class = new ReflectionClass( __CLASS__ );

		return array_values( $reflection_class->getConstants() );
	}
}
