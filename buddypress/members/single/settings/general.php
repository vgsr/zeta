<?php

/**
 * BuddyPress - Members Single Settings
 *
 * Changes to the default template:
 * - Reordered labels and inputs
 * - Wrapped settings in div.settings-field
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

<?php /** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */ ?>
<?php do_action( 'bp_before_member_settings_template' ); ?>

<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form" id="settings-form">

	<?php if ( ! is_super_admin() ) : ?>

		<div class="settings-field">
			<label for="pwd"><?php _e( 'Current Password', 'zeta' ); ?></label>
			<input type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" <?php bp_form_field_attributes( 'password' ); ?>/>
			<p class="description">
				<?php _e( 'Required to update email or change current password.', 'zeta' ); ?>
				<a href="<?php echo wp_lostpassword_url(); ?>" title="<?php esc_attr_e( 'Password Lost and Found', 'buddypress' ); ?>"><?php _e( 'Lost your password?', 'buddypress' ); ?></a>
			</p>
		</div>

	<?php endif; ?>

	<div class="settings-field">
		<label for="email"><?php _e( 'Account Email', 'buddypress' ); ?></label>
		<input type="email" name="email" id="email" value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" <?php bp_form_field_attributes( 'email' ); ?>/>
	</div>

	<div class="settings-field">
		<label for="pass1"><?php _e( 'Change Password', 'zeta' ); ?></label>
		<input type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small password-entry" <?php bp_form_field_attributes( 'password' ); ?>/>
		<div id="pass-strength-result"></div>
		<p class="description"><?php _e( 'Leave blank for no change.', 'zeta' ); ?></p>
	</div>

	<div class="settings-field">
		<label for="pass2"><?php _e( 'Repeat New Password', 'buddypress' ); ?></label>
		<input type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small password-entry-confirm" <?php bp_form_field_attributes( 'password' ); ?>/>
	</div>

	<?php

	/**
	 * Fires before the display of the submit button for user general settings saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_core_general_settings_before_submit' ); ?>

	<div class="submit">
		<input type="submit" name="submit" value="<?php esc_attr_e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
	</div>

	<?php

	/**
	 * Fires after the display of the submit button for user general settings saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_core_general_settings_after_submit' ); ?>

	<?php wp_nonce_field( 'bp_settings_general' ); ?>

</form>

<?php

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_after_member_settings_template' ); ?>
