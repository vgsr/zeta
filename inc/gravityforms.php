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
						$matches[] = ( GFFormsModel::get_input_type( $field ) == $value );
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

	// Form has date field with datepicker UI
	if ( zeta_gf_form_has_field( $form, array( 'type' => 'date', 'dateType' => 'datepicker' ) ) ) {

		// Enqueue datepicker style
		wp_enqueue_style( 'gforms_datepicker_css', GFCommon::get_base_url() . '/css/datepicker.min.css', null, GFCommon::$version );
	}

	// Form has multiple pages
	if ( zeta_gf_form_has_field( $form, array( 'type' => 'page' ) ) ) {
		$counter = 0;
		$css = '';

		// Walk form fields
		foreach ( $form['fields'] as $field ) {

			// Increment counter for regular fields
			if ( ! in_array( GFFormsModel::get_input_type( $field ), array( 'page', 'section', 'html' ) ) ) {
				$counter++;

			// Add counter increment for page field. Don't reset `$counter`
			} elseif ( 'page' == GFFormsModel::get_input_type( $field ) && $counter ) {
				$css .= "#gform_page_{$form['id']}_{$field->pageNumber} { counter-increment: form-field {$counter}; }\n";
			}
		}

		// Add style
		if ( ! empty( $css ) ) {
			wp_add_inline_style( 'gforms_formsmain_css', $css );
		}
	}
}
add_action( 'gform_enqueue_scripts', 'zeta_gf_enqueue_scripts', 10, 2 );
