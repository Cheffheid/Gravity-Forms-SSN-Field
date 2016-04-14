<?php

/**
 *
 * @since             1.0.0
 * @package           GF_Field_SSN
 *
 * @wordpress-plugin
 * Plugin Name:       Gravity Forms SSN Field
 * Plugin URI:        https://jeffreydewit.com/projects/gravity-forms-ssn-field
 * Description:       Adds a field for the last four of an SSN. Requires Gravity Forms.
 * Version:           1.0.0
 * Author:            Jeffrey de Wit
 * Author URI:        https://jeffreydewit.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gf-field-ssn
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/Cheffheid/Gravity-Forms-SSN-Field
 */

function gf_field_ssn_init() {
	if ( class_exists( 'GFForms' ) ) {

		class GF_Field_SSN extends GF_Field {

			public $type = 'ssn';

			public function get_form_editor_field_title() {
				return esc_attr__('Last four of SSN', 'gf-field-ssn' );
			}

			function get_form_editor_field_settings() {
				return array(
					'conditional_logic_field_setting',
					'prepopulate_field_setting',
					'error_message_setting',
					'label_setting',
					'label_placement_setting',
					'admin_label_setting',
					'rules_setting',
					'visibility_setting',
					'duplicate_setting',
					'description_setting',
					'css_class_setting',
				);
			}

			public function is_conditional_logic_supported() {
				return true;
			}

			public function get_field_input( $form, $value = '', $entry = null ) {
				$form_id         = absint( $form['id'] );
				$is_entry_detail = $this->is_entry_detail();
				$is_form_editor  = $this->is_form_editor();

				$html_input_type = 'text';

				$logic_event = ! $is_form_editor && ! $is_entry_detail ? $this->get_conditional_logic_event( 'keyup' ) : '';
				$id          = (int) $this->id;
				$field_id    = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

				$value        = esc_attr( $value );
				$class_suffix = $is_entry_detail ? '_admin' : '';
				$class        = $class_suffix;

				$tabindex              = $this->get_tabindex();
				$disabled_text         = $is_form_editor ? 'disabled="disabled"' : '';
				$placeholder_attribute = $this->get_field_placeholder_attribute();

				$input = "XXX-XX-<input name='input_{$id}' id='{$field_id}' type='{$html_input_type}' value='{$value}' class='{$class}' maxlength='4' pattern='[0-9]{4}' size='4' {$tabindex} {$logic_event} {$disabled_text}/>";

				return sprintf( "<div class='ginput_container ginput_container_text'>%s</div>", $input );
			}

			public function validate( $value, $form ) {

				$raw_value = $_POST[ 'input_' . $this->id ];

				if ( ! rgblank( $raw_value ) && ! is_numeric( $raw_value ) ) {
					$this->failed_validation = true;

					if ( empty( $this->errorMessage ) ) {
						$this->validation_message = esc_html__( 'Please enter a valid number', 'gf-field-ssn' );
					} else {
						$this->validation_message = $this->errorMessage;
					}
				} else {
					if ( $raw_value > 9999 ) {
						$this->failed_validation = true;
						$this->validation_message = esc_html__( 'Only need four numbers', 'gf-field-ssn' );
					}
				}
			}
		}

		GF_Fields::register( new GF_Field_SSN() );
	}
}
add_action( 'plugins_loaded', 'gf_field_ssn_init' );