<?php

/**
 * The sidebar containing the main widget area.
 *
 * @package Zeta
 */

if ( ! is_active_sidebar( 'zeta-sidebar-1' ) ) {
	return;
}

?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'zeta-sidebar-1' ); ?>
</div><!-- #secondary -->
