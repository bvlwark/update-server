<?php

namespace BVLWARK\UpdateServer\Models;

use stdClass;

defined( 'ABSPATH' ) || exit;

class PackageVersionModel extends AbstractModel {
	/**
	 * @var int
	 */
	protected int $package_id;

	/**
	 * @var string
	 */
	protected string $version;

	/**
	 * @var string
	 */
	protected string $requires;

	/**
	 * @var string
	 */
	protected string $tested;

	/**
	 * @var string
	 */
	protected string $changelog;


	/**
	 * PackageVersionModel constructor.
	 *
	 * @param null|stdClass $package_version Package version data.
	 */
	public function __construct( ?stdClass $package_version = null ) {
		parent::__construct( $package_version );

		if ( ! $package_version ) {
			return;
		}

		$this->package_id = (int) $package_version->package_id;
		$this->version    = (string) $package_version->version;
		$this->requires   = (string) $package_version->requires;
		$this->tested     = (string) $package_version->tested;
		$this->changelog  = (string) $package_version->changelog;
	}

	/**
	 * @return int
	 */
	public function get_package_id(): int {
		return $this->package_id;
	}

	/**
	 * @param int $package_id New package ID.
	 */
	public function set_package_id( int $package_id ): void {
		$this->package_id = $package_id;
	}

	/**
	 * @return string
	 */
	public function get_version(): string {
		return $this->version;
	}

	/**
	 * @param string $version New version.
	 */
	public function set_version( string $version ): void {
		$this->version = $version;
	}

	/**
	 * @return string
	 */
	public function get_requires(): string {
		return $this->requires;
	}

	/**
	 * @param string $requires New requires.
	 */
	public function set_requires( string $requires ): void {
		$this->requires = $requires;
	}

	/**
	 * @return string
	 */
	public function get_tested(): string {
		return $this->tested;
	}

	/**
	 * @param string $tested New tested.
	 */
	public function set_tested( string $tested ): void {
		$this->tested = $tested;
	}

	/**
	 * @return string
	 */
	public function get_changelog(): string {
		return $this->changelog;
	}

	/**
	 * @param string $changelog New changelog.
	 */
	public function set_changelog( string $changelog ): void {
		$this->changelog = $changelog;
	}
}
