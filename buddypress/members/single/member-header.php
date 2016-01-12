<?php

/**
 * BuddyPress - Users Header
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

<?php do_action( 'bp_before_member_header' ); ?>

<div id="item-header-avatar">
	<a class="item-avatar" href="<?php bp_is_my_profile() ? bp_members_component_link( 'profile', 'change-avatar' ) : bp_displayed_user_link(); ?>">
		<?php bp_displayed_user_avatar( 'type=full' ); ?>
	</a>

	<?php if ( bp_is_my_profile() ) : ?>
	<a class="change-avatar" href="<?php bp_members_component_link( 'profile', 'change-avatar' ); ?>"><span class="bp-screen-reader-text"><?php _e( 'Change your profile photo', 'zeta' ); ?></span></a>
	<?php endif; ?>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<?php do_action( 'bp_before_member_header_meta' ); ?>

	<div id="item-meta">

		<?php if ( bp_is_active( 'activity' ) ) : ?>
		<div id="latest-update"><?php
			bp_activity_latest_update( bp_displayed_user_id() );
		?></div>
		<?php endif;

		/**
		 * If you'd like to show specific profile fields here use:
		 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		 */
		do_action( 'bp_profile_header_meta' );

		?>
	</div><!-- #item-meta -->

	<div id="item-actions"><?php
		do_action( 'bp_member_header_actions' );
	?></div><!-- #item-actions -->

	<button class="item-actions-toggle" aria-controls="actions" aria-expanded="false"><?php _e( 'Actions', 'zeta' ); ?></button>

</div><!-- #item-header-content -->

<?php do_action( 'bp_after_member_header' ); ?>

<?php do_action( 'template_notices' ); ?>