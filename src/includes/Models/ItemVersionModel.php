<?php

namespace BVLWARK\UpdateServer\Models;

use stdClass;

defined( 'ABSPATH' ) || exit;

class ItemVersionModel extends AbstractModel {
	/**
	 * @var int
	 */
	protected int $item_id;

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
	protected string $description;

	/**
	 * @var string
	 */
	protected string $changelog;

	/**
	 * @var bool
	 */
	protected bool $protected;


	/**
	 * ActivationModel constructor.
	 *
	 * @param null|stdClass $item API key data.
	 */
	public function __construct( ?stdClass $item = null ) {
		parent::__construct( $item );

		if ( ! $item ) {
			return;
		}

		$this->item_id     = (int) $item->item_id;
		$this->version     = (string) $item->version;
		$this->requires    = (string) $item->requires;
		$this->tested      = (string) $item->tested;
		$this->description = (string) $item->description;
		$this->changelog   = (string) $item->changelog;
		$this->protected   = (bool) $item->description;
	}

	/**
	 * @return int
	 */
	public function get_item_id(): int {
		return $this->item_id;
	}

	/**
	 * @param int $item_id New Item ID.
	 */
	public function set_item_id( int $item_id ): void {
		$this->item_id = $item_id;
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
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * @param string $description New description.
	 */
	public function set_description( string $description ): void {
		$this->description = $description;
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

	/**
	 * @return bool
	 */
	public function is_protected(): bool {
		return $this->protected;
	}

	/**
	 * @param bool $is_protected New protection status.
	 */
	public function set_protected( bool $is_protected ): void {
		$this->protected = $is_protected;
	}
}
