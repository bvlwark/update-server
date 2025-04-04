<?php

namespace BVLWARK\UpdateServer\Models;

use stdClass;

defined( 'ABSPATH' ) || exit;

class ApiKeyModel extends AbstractModel {
	/**
	 * @var int
	 */
	protected int $id;

	/**
	 * @var int
	 */
	protected int $user_id;

	/**
	 * @var null|string
	 */
	protected ?string $description = null;

	/**
	 * @var null|string
	 */
	protected ?string $permissions = null;

	/**
	 * @var string
	 */
	protected string $consumer_key;

	/**
	 * @var string
	 */
	protected string $consumer_secret;

	/**
	 * @var null|string
	 */
	protected ?string $nonces;

	/**
	 * @var string
	 */
	protected string $truncated_key;

	/**
	 * @var null|string
	 */
	protected ?string $last_access;

	/**
	 * @var string
	 */
	protected string $created_at;

	/**
	 * @var int
	 */
	protected int $created_by;

	/**
	 * @var null|string
	 */
	protected ?string $updated_at;

	/**
	 * @var null|int
	 */
	protected ?int $updated_by;

	/**
	 * ApiKey constructor.
	 *
	 * @param null|stdClass $api_key API key data.
	 */
	public function __construct( ?stdClass $api_key = null ) {
		parent::__construct( $api_key );

		if ( ! $api_key ) {
			return;
		}

		$this->user_id         = (int) $api_key->user_id;
		$this->description     = $api_key->description;
		$this->permissions     = $api_key->permissions;
		$this->consumer_key    = $api_key->consumer_key;
		$this->consumer_secret = $api_key->consumer_secret;
		$this->nonces          = $api_key->nonces;
		$this->truncated_key   = $api_key->truncated_key;
		$this->last_access     = $api_key->last_access;
	}

	/**
	 * @return int
	 */
	public function get_user_id(): int {
		return $this->user_id;
	}

	/**
	 * @param int $user_id WordPress User ID.
	 *
	 * @return void
	 */
	public function set_user_id( int $user_id ): void {
		$this->user_id = $user_id;
	}

	/**
	 * @return string
	 */
	public function get_description(): ?string {
		return $this->description;
	}

	/**
	 * @param string $description Description.
	 *
	 * @return void
	 */
	public function set_description( string $description ): void {
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function get_permissions(): ?string {
		return $this->permissions;
	}

	/**
	 * @param string $permissions Permissions.
	 *
	 * @return void
	 */
	public function set_permissions( string $permissions ): void {
		$this->permissions = $permissions;
	}

	/**
	 * @return string
	 */
	public function get_consumer_key(): string {
		return $this->consumer_key;
	}

	/**
	 * @param string $consumer_key Consumer key.
	 *
	 * @return void
	 */
	public function set_consumer_key( string $consumer_key ): void {
		$this->consumer_key = $consumer_key;
	}

	/**
	 * @return string
	 */
	public function get_consumer_secret(): string {
		return $this->consumer_secret;
	}

	/**
	 * @param string $consumer_secret Consumer secret.
	 *
	 * @return void
	 */
	public function set_consumer_secret( string $consumer_secret ): void {
		$this->consumer_secret = $consumer_secret;
	}

	/**
	 * @return null|string
	 */
	public function get_nonces(): ?string {
		return $this->nonces;
	}

	/**
	 * @param null|string $nonces Nonces.
	 *
	 * @return void
	 */
	public function set_nonces( ?string $nonces ): void {
		$this->nonces = $nonces;
	}

	/**
	 * @return string
	 */
	public function get_truncated_key(): string {
		return $this->truncated_key;
	}

	/**
	 * @param string $truncated_key Truncated key.
	 *
	 * @return void
	 */
	public function set_truncated_key( string $truncated_key ): void {
		$this->truncated_key = $truncated_key;
	}

	/**
	 * @return null|string
	 */
	public function get_last_access(): ?string {
		return $this->last_access;
	}

	/**
	 * @param null|string $last_access Last access.
	 *
	 * @return void
	 */
	public function set_last_access( ?string $last_access ): void {
		$this->last_access = $last_access;
	}
}
