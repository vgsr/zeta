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
add_filter( 'pre_option_rg_gforms_disable_css', '__return_true' );

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

/** GF Pages ***************************************************************/

/**
 * Print entry meta for form pages
 *
 * @since 1.0.0
 */
function zeta_gfp_entry_meta() {

	// Form
	if ( function_exists( 'gf_pages_is_form' ) && gf_pages_is_form() ) {

		// Form is inactive
		if ( gf_pages_is_form_inactive() ) {
			echo '<span class="form-status">' . esc_html_x( 'Inactive', 'Form status', 'zeta' ) . '</span>';

		// Form is not open yet
		} elseif ( ! gf_pages_is_form_open() ) {
			printf( '<span class="form-status" title="%s">%s</span>',
				gf_pages_get_form_open_date( get_option( 'date_format' ) ),
				/* translators: time difference */
				sprintf( esc_html__( 'Opening in %s', 'zeta' ), human_time_diff( time(), gf_pages_get_form_open_date( 'U' ) ) )
			);

		// Form is closed
		} elseif ( gf_pages_is_form_closed() ) {
			echo '<span class="form-status">' . esc_html_x( 'Closed', 'Form status', 'zeta' ) . '</span>';

		// Form is temporal
		} elseif ( gf_pages_get_form_close_date() ) {

			// Open date
			echo '<span class="form-date">' . gf_pages_get_form_open_date( get_option( 'date_format' ) ) . '</span>';

			// Closing timespan
			printf( '<span class="form-status" title="%s">%s</span>',
				gf_pages_get_form_close_date( get_option( 'date_format' ) ),
				/* translators: time difference */
				sprintf( esc_html__( 'Closing in %s', 'zeta' ), human_time_diff( time(), gf_pages_get_form_close_date( 'U' ) ) )
			);
		}

		// User can edit forms
		if ( GFCommon::current_user_can_any( 'gforms_edit_forms' ) ) {

			// View count
			$count = gf_pages_get_form_view_count();
			echo '<span class="view-count">' . sprintf( _n( '%d View', '%d Views', $count, 'zeta' ), $count ) . '</span>';

			// Entry count
			$count = gf_pages_get_form_entry_count();
			$link = $count > 0 ? '<a href="' . gf_pages_get_view_form_entries_url() . '">%s</a>' : '%s';
			echo '<span class="entry-count">' . sprintf( $link, sprintf( _n( '%d Entry', '%d Entries', $count, 'zeta' ), $count ) ) . '</span>';

			// Edit link
			gf_pages_the_form_edit_link( array(
				'link_before' => '<span class="edit-link">',
				'link_after'  => '</span>'
			) );
		}
	}
}
add_action( 'zeta_entry_meta', 'zeta_gfp_entry_meta' );
