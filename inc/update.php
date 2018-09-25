<?php

/**
 * Zeta Updater
 *
 * @package Zeta
 * @subpackage Updater
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Define additional theme file headers
 *
 * @since 1.0.0
 *
 * @param array $headers Theme file headers
 * @return array Theme file headers
 */
function zeta_extra_theme_headers( $headers ) {

	// Theme database version
	$headers[] = 'DB Version';

	return $headers;
}
add_filter( 'extra_theme_headers', 'zeta_extra_theme_headers' );

/** Versions ******************************************************************/

/**
 * Output the theme version
 *
 * @since 1.0.0
 */
function zeta_version() {
	echo zeta_get_version();
}

	/**
	 * Return the theme version
	 *
	 * @since 1.0.0
	 *
	 * @return string The theme version
	 */
	function zeta_get_version() {
		return wp_get_theme()->get( 'Version' );
	}

/**
 * Output the theme database version
 *
 * @since 1.0.0
 */
function zeta_db_version() {
	echo zeta_get_db_version();
}

	/**
	 * Return the theme database version
	 *
	 * @since 1.0.0
	 *
	 * @return string The theme database version
	 */
	function zeta_get_db_version() {
		return wp_get_theme()->get( 'DB Version' );
	}

/**
 * Output the theme database version directly from the database
 *
 * @since 1.0.0
 */
function zeta_db_version_raw() {
	echo zeta_get_db_version_raw();
}

	/**
	 * Return the theme database version directly from the database
	 *
	 * @since 1.0.0
	 *
	 * @return string The current theme database version
	 */
	function zeta_get_db_version_raw() {
		return get_theme_mod( 'zeta_db_version', '' );
	}

/** Updaters ******************************************************************/

/**
 * If there is no raw DB version, this is the first installation
 *
 * @since 1.0.0
 *
 * @return bool True if update, False if not
 */
function zeta_is_install() {
	return ! zeta_get_db_version_raw();
}

/**
 * Compare the theme version to the DB version to determine if updating
 *
 * @since 1.0.0
 *
 * @return bool True if update, False if not
 */
function zeta_is_update() {
	$raw    = (int) zeta_get_db_version_raw();
	$cur    = (int) zeta_get_db_version();
	$retval = (bool) ( $raw < $cur );
	return $retval;
}

/**
 * Update the DB to the latest version
 *
 * @since 1.0.0
 */
function zeta_version_bump() {
	set_theme_mod( 'zeta_db_version', zeta_get_db_version() );
}

/**
 * Setup the theme updater
 *
 * @since 1.0.0
 */
function zeta_setup_updater() {

	// Bail if no update needed
	if ( ! zeta_is_update() )
		return;

	// Call the automated updater
	zeta_version_updater();
}

/**
 * Theme's version updater looks at what the current database version is, and
 * runs whatever other code is needed.
 *
 * This is most-often used when the data schema changes, but should also be used
 * to correct issues with theme meta-data silently on software update.
 *
 * @since 1.0.0
 *
 * @todo Log update event
 */
function zeta_version_updater() {

	// Get the raw database version
	$raw_db_version = (int) zeta_get_db_version_raw();

	/** 0.9.x Branch ********************************************************/

	// 0.9.4
	if ( $raw_db_version < 20180925 ) {
		zeta_update_version_094();
	}

	/** All done! ***********************************************************/

	// Bump the version
	zeta_version_bump();
}

/**
 * Update routines for version 0.9.4
 *
 * @since 0.9.4
 */
function zeta_update_version_094() {

	// Rename 'background_image' theme mod
	$mod = get_theme_mod( 'background_image', array() );
	set_theme_mod( 'default_background', $mod );
	remove_theme_mod( 'background_image' );

	// Rename 'background_image_single' theme mod
	$mod = get_theme_mod( 'background_image_single', 0 );
	set_theme_mod( 'default_background_single', $mod );
	remove_theme_mod( 'background_image_single' );
}
