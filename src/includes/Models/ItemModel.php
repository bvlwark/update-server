<?php

namespace BVLWARK\UpdateServer\Models;

use stdClass;

defined( 'ABSPATH' ) || exit;

class ItemModel extends AbstractModel {
	/**
	 * @var string
	 */
	protected string $name;

	/**
	 * @var string
	 */
	protected string $slug;

	/**
	 * @var string
	 */
	protected string $type;

	/**
	 * @var null|string
	 */
	protected ?string $description;

	/**
	 * @var bool
	 */
	protected bool $is_public;


	/**
	 * ItemModel constructor.
	 *
	 * @param null|stdClass $item Item data.
	 */
	public function __construct( ?stdClass $item = null ) {
		parent::__construct( $item );

		if ( ! $item ) {
			return;
		}

		$this->name        = (string) $item->name;
		$this->slug        = (string) $item->slug;
		$this->type        = (string) $item->type;
		$this->description = ! empty( $item->description )
			? (string) $item->description
			: null;
		$this->is_public   = (bool) $item->is_public;
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
	 * @return null|string
	 */
	public function get_description(): ?string {
		return $this->description;
	}

	/**
	 * @param null|string $description New description.
	 */
	public function set_description( ?string $description ): void {
		$this->description = $description;
	}

	/**
	 * @return bool
	 */
	public function is_public(): bool {
		return $this->is_public;
	}

	/**
	 * @param bool $is_public New is_public value.
	 */
	public function set_public( bool $is_public ): void {
		$this->is_public = $is_public;
	}
}
