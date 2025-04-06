<?php

namespace BVLWARK\UpdateServer;

use ReflectionClass;

defined( 'ABSPATH' ) || exit;

abstract class PackageTypeEnum {
	/**
	 * Enumerator value used for plugin types.
	 *
	 * @var string
	 */
	const string PLUGIN = 'plugin';

	/**
	 * Enumerator value used for theme types.
	 *
	 * @var string
	 */
	const string THEME = 'theme';

	/**
	 * Available enumerator values.
	 *
	 * @var array
	 */
	public static array $status = array(
		self::PLUGIN,
		self::THEME,
	);

	/**
	 * Available text representations of the enumerator
	 *
	 * @var array
	 */
	public static array $enum_array = array(
		'plugin',
		'theme',
	);

	/**
	 * Key/value pairs of text representations and actual enumerator values.
	 *
	 * @var array
	 */
	public static array $values = array(
		'plugin' => self::PLUGIN,
		'theme'  => self::THEME,
	);

	/**
	 * Returns an array of all possible enumerators.
	 *
	 * @return array
	 */
	public static function get_options(): array {
		return array(
			array(
				'value' => self::PLUGIN,
				'label' => __( 'Plugin', 'bvlwark-update-server' ),
			),
			array(
				'value' => self::THEME,
				'label' => __( 'Theme', 'bvlwark-update-server' ),
			),
		);
	}

	/**
	 * Returns the class constants as an array.
	 *
	 * @return array
	 */
	public static function get_constants(): array {
		$reflection_class = new ReflectionClass( __CLASS__ );

		return $reflection_class->getConstants();
	}
}
