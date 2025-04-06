<?php
// phpcs:disable Generic.PHP.Syntax.PHPSyntax

namespace BVLWARK\UpdateServer;

defined( 'ABSPATH' ) || exit;

class HtmlRenderer {
	/**
	 * @param array  $args Template arguments.
	 * @param string $type Field type.
	 *
	 * @return array
	 */
	private static function parse_field_args( array $args, string $type = 'input' ): array {
		$extended_args = array();
		$base_args     = array(
			'id'              => '',
			'label'           => '',
			'name'            => '',
			'value'           => '',
			'readonly'        => false,
			'disabled'        => false,
			'required'        => false,
			'description'     => '',
			'wrapper_classes' => array(),
		);

		switch ( $type ) {
			case 'input':
				$extended_args = array(
					'type'        => 'text',
					'checked'     => false,
					'min'         => null,
					'max'         => null,
					'step'        => null,
					'placeholder' => null,
				);
				break;
			case 'select':
				$extended_args = array(
					'options' => array(),
				);
				break;
			case 'textarea':
				$extended_args = array(
					'cols' => 50,
					'rows' => 10,
				);
				break;
			default:
				break;
		}

		$default_args = array_merge( $base_args, $extended_args );
		$parsed       = wp_parse_args( $args, $default_args );

		return apply_filters( 'bvlwark_parse_field_args', $parsed, $type );
	}

	/**
	 * Field data sanitization.
	 *
	 * @param array $args Input arguments.
	 *
	 * @return array
	 */
	private static function sanitize_field_args( array $args ): array {
		$sanitized = array();

		foreach ( $args as $key => $value ) {
			switch ( $key ) {
				case 'id':
				case 'label':
				case 'type':
				case 'value':
				case 'name':
				case 'description':
				case 'placeholder':
					$sanitized[ $key ] = bvlwark_clean_string( $value );
					break;
				case 'cols':
				case 'rows':
				case 'min':
				case 'max':
				case 'step':
					$sanitized[ $key ] = (int) $value;
					break;
				case 'wrapper_classes':
					$sanitized[ $key ] = array_map( 'bvlwark_clean_string', $value );
					break;
				case 'checked':
				case 'readonly':
				case 'disabled':
				case 'required':
					$sanitized[ $key ] = (bool) $value;
					break;
				case 'options':
					if ( empty( $value ) ) {
						$sanitized[ $key ] = $value;
					} else {
						foreach ( $value as $i => $option ) {
							$sanitized[ $key ][ $i ]['value'] = bvlwark_clean_string( $option['value'] );
							$sanitized[ $key ][ $i ]['label'] = bvlwark_clean_string( $option['label'] );
						}
					}
					break;
				default:
					$sanitized[ $key ] = $value;
					break;
			}
		}

		return apply_filters( 'bvlwark_sanitize_field_args', $sanitized, $args );
	}

	/**
	 * Renders a list of arbitrary options.
	 *
	 * @param array           $options  The options to render. Must be an associative
	 *                                  array with the keys 'value', and 'label'.
	 * @param null|int|string $selected The selected value.
	 *
	 * @return string
	 */
	public static function render_options( array $options, null|int|string $selected = null ): string {
		$html = '';

		foreach ( $options as $option ) {
			$html .= sprintf(
				'<option value="%s" %s>%s</option>',
				esc_html( $option['value'] ),
				selected( $option['value'], $selected, false ),
				esc_html( $option['label'] ),
			);
		}

		return $html;
	}

	/**
	 * Renders an input field.
	 *
	 * @param array $args Input field arguments.
	 *
	 * @return string
	 */
	public static function render_input( array $args ): string {
		$data        = self::sanitize_field_args( self::parse_field_args( $args ) );
		$id          = $data['id'];
		$name        = ! empty( $data['name'] ) ? $data['name'] : $data['id'];
		$type        = $data['type'];
		$value       = $data['value'];
		$checked     = $data['checked'] === true ? 'checked="checked"' : '';
		$readonly    = $data['readonly'] ? 'readonly' : '';
		$disabled    = $data['disabled'] ? 'disabled="disabled"' : '';
		$required    = $data['required'] ? 'required="required"' : '';
		$placeholder = $data['placeholder']
			? 'placeholder="' . esc_attr( $data['placeholder'] ) . '"'
			: '';

		return sprintf(
			'<input id="%s" name="%s" class="regular-text" type="%s" value="%s" %s %s %s %s %s/>',
			esc_html( $id ),
			esc_html( $name ),
			esc_html( $type ),
			esc_html( $value ),
			$checked,
			$readonly,
			$disabled,
			$required,
			$placeholder,
		);
	}

	/**
	 * Renders an input field.
	 *
	 * @param array $args    Input field data. Keys: id, label, name, type,
	 *                       value, readonly, and disabled.
	 *
	 * @return string
	 */
	public static function render_admin_input( array $args ): string {
		$data            = self::sanitize_field_args( self::parse_field_args( $args ) );
		$id              = $data['id'];
		$label           = $data['label'];
		$name            = ! empty( $data['name'] ) ? $data['name'] : $data['id'];
		$type            = $data['type'];
		$value           = $data['value'];
		$wrapper_classes = $data['wrapper_classes'];
		$description     = '';

		if ( isset( $data['description'] ) ) {
			$description = sprintf(
				'<p class="description">%s</p>',
				bvlwark_clean_string( $data['description'] )
			);
		}

		return sprintf(
			'
				<tr id="%s" class="%s">
					<th>
						<label for="%s">%s</label>
					</th>
					<td>%s %s</td>
				</tr>
			',
			esc_html( "bvlwark-admin-input-$id" ),
			implode( ' ', $wrapper_classes ),
			esc_html( $id ),
			esc_html( $label ),
			self::render_input(
				array(
					'id'          => $id,
					'name'        => $name,
					'type'        => $type,
					'value'       => $value,
					'readonly'    => $data['readonly'],
					'disabled'    => $data['disabled'],
					'required'    => $data['required'],
					'placeholder' => $data['placeholder'],
				)
			),
			$description
		);
	}

	/**
	 * Renders a select field.
	 *
	 * @param array $args    Input field data. Keys: id, label, name, type,
	 *                       value, readonly, and disabled.
	 *
	 * @return string
	 */
	public static function render_admin_select( array $args ): string {
		$args            = self::parse_field_args( $args, 'select' );
		$data            = self::sanitize_field_args( $args );
		$id              = $data['id'];
		$label           = $data['label'];
		$options         = $data['options'];
		$value           = $data['value'];
		$name            = $data['name'] ?: $data['id'];
		$wrapper_classes = $data['wrapper_classes'];
		$description     = '';

		if ( isset( $data['description'] ) ) {
			$description = sprintf(
				'<p class="description">%s</p>',
				bvlwark_clean_string( $data['description'] )
			);
		}

		return sprintf(
			'
				<tr id="%s" class="%s">
					<th>
						<label for="%s">%s</label>
					</th>
					<td>
						<select id="%s" name="%s" class="regular-text" %s %s %s>
							%s
						</select>
						%s
					</td>
				</tr>
			',
			esc_html( "bvlwark-admin-select-$id" ),
			esc_html( implode( ' ', $wrapper_classes ) ),
			esc_html( $id ),
			esc_html( $label ),
			esc_html( $id ),
			esc_html( $name ),
			$data['readonly'] ? 'readonly' : '',
			$data['disabled'] ? 'disabled="disabled"' : '',
			$data['required'] ? 'required="required"' : '',
			self::render_options( $options, $value ),
			$description
		);
	}

	/**
	 * Renders an input field.
	 *
	 * @param array $args    Input field data. Keys: id, label, name, type,
	 *                       value, readonly, and disabled.
	 *
	 * @return string
	 */
	public static function render_admin_textarea( array $args ): string {
		$args            = self::parse_field_args( $args, 'textarea' );
		$data            = self::sanitize_field_args( $args );
		$id              = $data['id'];
		$label           = $data['label'];
		$name            = $data['name'] ?? $data['id'];
		$value           = $data['value'];
		$cols            = $data['cols'];
		$rows            = $data['rows'];
		$wrapper_classes = $data['wrapper_classes'];
		$description     = '';

		if ( isset( $data['description'] ) ) {
			$description = sprintf(
				'<p class="description">%s</p>',
				bvlwark_clean_string( $data['description'] )
			);
		}

		return sprintf(
			'
				<tr id="%s" class="%s">
					<th>
						<label for="%s">%s</label>
					</th>
					<td>
						<textarea name="%s" id="%s" cols="%d" rows="%d" %s %s %s>%s</textarea>
						%s
					</td>
				</tr>
			',
			esc_html( "bvlwark-admin-textarea-$id" ),
			esc_html( implode( ' ', $wrapper_classes ) ),
			esc_html( $id ),
			esc_html( $label ),
			esc_html( $name ),
			esc_html( $id ),
			$cols,
			$rows,
			$data['readonly'] ? 'readonly' : '',
			$data['disabled'] ? 'disabled="disabled"' : '',
			$data['required'] ? 'required="required"' : '',
			esc_html( $value ),
			$description
		);
	}

	/**
	 * Renders a textarea as a wysiwyg field.
	 *
	 * @param array $args Input field data. Keys: id, label, name, type,
	 *                    value, readonly, and disabled.
	 *
	 * @return void
	 */
	public static function render_admin_rich_text( array $args ): void {
		$args            = self::parse_field_args( $args, 'textarea' );
		$data            = self::sanitize_field_args( $args );
		$id              = $data['id'];
		$label           = $data['label'];
		$value           = $data['value'] ?? '';
		$name            = ! empty( $data['name'] ) ? $data['name'] : $data['id'];
		$rows            = $data['rows'] ?? 10;
		$wrapper_classes = $data['wrapper_classes'];

		printf(
			'
			<tr id="%s" class="%s">
				<th>
					<label for="%s">%s</label>
				</th>
				<td>
			',
			esc_html( "bvlwark-admin-wysiwyg-$id" ),
			esc_html( implode( ' ', $wrapper_classes ) ),
			esc_html( $id ),
			esc_html( $label ),
		);

		wp_editor(
			$value,
			$name,
			array(
				'textarea_name' => $name,
				'media_buttons' => false,
				'textarea_rows' => $rows,
				'teeny'         => true,
				'quicktags'     => true,
			)
		);

		echo '</td></tr>';
	}
}
