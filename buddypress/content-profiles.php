<?php

/**
 * The template for displaying BuddyPress profiles in the post content
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

<div id="buddypress">
	<div id="members-dir-list" class="members dir-list">

		<p>
			<?php bp_members_pagination_count(); ?>
		</p>

		<?php bp_get_template_part( 'content-members-loop' ); ?>
	</div><!-- #members-dir-list -->
</div>
