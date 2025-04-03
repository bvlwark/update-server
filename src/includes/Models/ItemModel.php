<?php

namespace BVLWARK\UpdateServer\Models;

use stdClass;

defined( 'ABSPATH' ) || exit;

class ItemModel extends AbstractModel {
	/**
	 * @var string
	 */
	protected string $type;

	/**
	 * @var string
	 */
	protected string $slug;

	/**
	 * @var string
	 */
	protected string $name;

	/**
	 * @var string
	 */
	protected string $description;

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

		$this->type        = (string) $item->type;
		$this->slug        = (string) $item->slug;
		$this->name        = (string) $item->name;
		$this->description = (string) $item->description;
		$this->protected   = (bool) $item->description;
	}

	/**
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * @param string $type New type.
	 */
	public function set_type( string $type ): void {
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * @param string $slug New slug.
	 */
	public function set_slug( string $slug ): void {
		$this->slug = $slug;
	}

	/**
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * @param string $name New name.
	 */
	public function set_name( string $name ): void {
		$this->name = $name;
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
	 * @return bool
	 */
	public function is_protected(): bool {
		return $this->protected;
	}

	/**
	 * @param bool $protected_value Protection identifier.
	 */
	public function set_protected( bool $protected_value ): void {
		$this->protected = $protected_value;
	}
}
