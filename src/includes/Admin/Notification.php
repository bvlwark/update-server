<?php

namespace BVLWARK\UpdateServer\Admin;

defined( 'ABSPATH' ) || exit;

class Notification {
	const string NOTICE_ERROR   = 'notice-error';
	const string NOTICE_SUCCESS = 'notice-success';
	const string NOTICE_WARNING = 'notice-warning';
	const string NOTICE_INFO    = 'notice-info';

	/**
	 * @var array
	 */
	protected array $types = array(
		'error'   => self::NOTICE_ERROR,
		'success' => self::NOTICE_SUCCESS,
		'warning' => self::NOTICE_WARNING,
		'info'    => self::NOTICE_INFO,
	);

	/**
	 * Notice constructor.
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'init' ) );
	}

	/**
	 * Retrieves the notice message from the transients, displays it and finally deletes the transient itself.
	 */
	public function init(): void {
		foreach ( $this->types as $type => $class ) {
			$messages = get_transient( "bvlwark_update_server_notice_$type" );

			if ( $messages && is_array( $messages ) ) {
				foreach ( $messages as $message ) {
					printf(
						'<div class="notice %s is-dismissible"><p><b>BVLWARK Licenses</b>: %s</p></div>',
						esc_html( $class ),
						esc_html( $message )
					);
				}

				delete_transient( "bvlwark_update_server_notice_$type" );
			}
		}
	}

	/**
	 * Adds a dashboard notice to be displayed on the next page reload.
	 *
	 * @param string $level    Notice level.
	 * @param string $message  Notice content.
	 * @param int    $duration Duration of the notice (in seconds).
	 */
	public static function add( string $level, string $message, int $duration = 60 ): void {
		$messages = get_transient( "bvlwark_update_server_notice_$level" );

		if ( $messages && is_array( $messages ) ) {
			$messages[] = $message;
		} else {
			$messages = array( $message );
		}

		set_transient( "bvlwark_update_server_notice_$level", $messages, $duration );
	}

	/**
	 * Display an error message.
	 *
	 * @param string $message The error message.
	 */
	public static function error( string $message ): void {
		self::add( 'error', $message );
	}

	/**
	 * Display a success message.
	 *
	 * @param string $message The success message to be displayed.
	 */
	public static function success( string $message ): void {
		self::add( 'success', $message );
	}

	/**
	 * Display a warning message.
	 *
	 * @param string $message The warning message to be displayed.
	 */
	public static function warning( string $message ): void {
		self::add( 'warning', $message );
	}

	/**
	 * Display an info message.
	 *
	 * @param string $message The info message to be displayed.
	 */
	public static function info( string $message ): void {
		self::add( 'info', $message );
	}
}
