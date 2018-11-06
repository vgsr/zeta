<?php

/**
 * The template for displaying BuddyPress profiles in the post content
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

<div class="zeta-bp-profiles-list">

	<ul class="bp-item-list">
		<?php while ( bp_members() && zeta_bp_members_profiles_list_limiting() ) : bp_the_member(); ?>

			<li <?php bp_member_class( array( 'member' ) ); ?>>
				<div class="item-avatar">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(); ?></a>
				</div>

				<div class="item">
					<div class="item-title">
						<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
					</div>
				</div>
			</li>

		<?php endwhile; ?>

		<?php if ( zeta_bp_members_profiles_list_is_limited() ) : ?>

			<li class="bp-list-limit <?php echo $GLOBALS['members_template']->total_member_count % 2 ? 'odd' : 'even'; ?>">
				<div class="item">
					<?php printf( get_permalink() ? '<a href="%1$s">%2$s</a>' : '<span>%2$s</span>',
						esc_url( get_permalink() ),
						'&plus;' . zeta_bp_members_profiles_list_limited_count()
					); ?>
				</div>
			</li>

		<?php endif; ?>
	</ul>

</div>