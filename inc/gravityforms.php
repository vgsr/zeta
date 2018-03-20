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
 * @uses GFFormsModel::get_input_type()
 *
 * @param array $form Form data
 * @param array $args Field arguments
 * @return bool Form has field
 */
function zeta_gf_form_has_field( $form, $args = array() ) {

	// Define local variable
	$retval = false;

	// Form has fields, args are provided
	if ( ! empty( $form['fields'] ) && ! empty( $args ) ) {

		// Walk form fields
		foreach ( $form['fields'] as $field ) {
			$matches = array();

			// Walk args
			foreach ( $args as $key => $value ) {
				switch ( $key ) {

					// Handle field type
					case 'type' :
						$matches[] = ( GFFormsModel::get_input_type( $field ) == $value );
						break;
					default :

						// Serving `null` will check for detail absence
						if ( null === $value ) {
							$matches[] = ! isset( $field->{$key} );
						} else {
							$matches[] = isset( $field->{$key} ) ? ( $field->{$key} == $value ) : false;
						}
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
 * @uses GFFormsModel::get_input_type()
 * @uses wp_add_inline_style()
 *
 * @param array $form Form data for which to load the scripts
 * @param bool $ajax Whether the form uses AJAX
 */
function zeta_gf_enqueue_scripts( $form, $ajax = false ) {
	wp_enqueue_style( 'gforms_formsmain_css', get_template_directory_uri() . '/assets/css/gravityforms.css' );

	// Form has date field with datepicker UI
	if ( zeta_gf_form_has_field( $form, array( 'type' => 'date', 'dateType' => 'datepicker' ) ) ) {

		// Enqueue datepicker style
		wp_enqueue_style( 'gforms_datepicker_css', GFCommon::get_base_url() . '/css/datepicker.min.css', null, GFCommon::$version );
	}

	/**
	 * Form has multiple pages
	 *
	 * The CSS counter `form-field`, which handles the field numbering, ignores the
	 * fields within hidden elements. Because all non-current viewed form's pages
	 * are hidden with 'display:none;', this leads each page to count only his
	 * own fields, starting with 1. To prevent this, the CSS counter must know
	 * how many fields reside within the anterior hidden pages.
	 *
	 * NB: the issue also occurs when conditionally showing/hiding fields. I do
	 * not know yet how to deal with that. Perhaps CSS counter is not that helpful?
	 */
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
