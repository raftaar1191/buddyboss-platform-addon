<?php
/**
 * BuddyBoss Compatibility Integration Class.
 *
 * @since BuddyBoss 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the bp compatibility class.
 *
 * @since BuddyBoss 1.0.0
 */
class BuddyBoss_Platform_Addon_BuddyBoss_Integration_Current_Fields{

    public function __construct() {

        /**
         * Register fields for settings hooks
         * bp_admin_setting_general_register_fields
         * bp_admin_setting_xprofile_register_fields
         * bp_admin_setting_groups_register_fields
         * bp_admin_setting_forums_register_fields
         * bp_admin_setting_activity_register_fields
         * bp_admin_setting_media_register_fields
         * bp_admin_setting_friends_register_fields
         * bp_admin_setting_invites_register_fields
         * bp_admin_setting_search_register_fields
         */
        add_action( 'bp_admin_setting_general_register_fields', array( $this, 'admin_setting_general_register_fields' ) );
    }

    public function admin_setting_general_register_fields( $setting ) {
        // Main General Settings Section
	    $setting->add_section( 'BuddyBoss_Platform_Addon_addon', __( 'Addon Settings', 'buddyboss-platform-addon' ) );

	    $args          = array();
	    $setting->add_field( 'buddyboss-platform-addon-enable-my-addon', __( 'My Field', 'buddyboss-platform-addon' ), array( $this, 'admin_general_setting_callback' ), 'intval', $args );
    }

    public function admin_general_setting_callback() {
		?>
        <input id="buddyboss-platform-addon-enable-my-addon" name="buddyboss-platform-addon-enable-my-addon" type="checkbox"
               value="1" <?php checked( $this->is_addon_field_enabled() ); ?> />
        <label for="buddyboss-platform-addon-enable-my-addon"><?php _e( 'Enable my option', 'buddyboss-platform-addon' ); ?></label>
		<?php
	}

    public function is_addon_field_enabled( $default = 1 ) {
		return get_option( 'buddyboss-platform-addon-enable-my-addon', $default );
	}
}