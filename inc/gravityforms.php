<?php

/**
 * Zeta Gravity Forms Functions
 * 
 * @package Zeta
 * @subpackage Gravity Forms
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Short-circuit the setting to disable plugin styles
 *
 * @since 1.0.0
 *
 * @return bool True
 */
function zeta_gf_disable_plugin_styles() {
	return true;
}
add_filter( 'pre_option_rg_gforms_disable_css', 'zeta_gf_disable_plugin_styles' );

/**
 * Return whether the form has fields matching `$args`
 *
 * @since 1.0.0
 *
 * @param array $form Form data
 * @param array $args Field arguments
 * @return bool Form has field
 */
function zeta_gf_form_has_field( $form, $args = array() ) {

	// Define local variable
	$retval = false;

	if ( ! empty( $form['fields'] ) && ! empty( $args ) ) {
		foreach ( $form['fields'] as $field ) {
			$matches = array();

			// Walk args
			foreach ( $args as $key => $value ) {
				switch ( $key ) {

					// Field type
					case 'type' :

						// Get field type
						$type = ( $field instanceof GField )
							? $field->get_input_type()
							: ( empty( $field['inputType'] ) ? $field['type'] : $field['inputType'] );

						// Match field type
						$matches[] = ( $type == $value );
						break;
					default :
						$matches[] = isset( $field->{$key} ) ? ( $field->{$key} == $value ) : false;
						break;
				}
			}

			// Break when field matches all
			if ( ! empty( $matches ) && ! in_array( false, $matches ) ) {
				$retval = true;
				break;
			}
		}
	}

	return $retval;
}

/**
 * Enqueue form scripts
 *
 * @since 1.0.0
 *
 * @uses wp_enqueue_style()
 * @uses zeta_gf_form_has_field()
 */
function zeta_gf_enqueue_scripts( $form, $ajax = false ) {
	wp_enqueue_style( 'gforms_formsmain_css', get_template_directory_uri() . '/css/gravityforms.css' );

	// Enqueue datepicker style
	if ( zeta_gf_form_has_field( $form, array( 'type' => 'date', 'dateType' => 'datepicker' ) ) ) {
		wp_enqueue_style( 'gforms_datepicker_css', GFCommon::get_base_url() . '/css/datepicker.min.css', null, GFCommon::$version );
	}

}
add_action( 'gform_enqueue_scripts', 'zeta_gf_enqueue_scripts', 10, 2 );
