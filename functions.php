<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/***************************** Add section in current settings ***************************************/
/**
 * Set up the my plugin integration.
 */
require_once dirname( __FILE__ ) . '/integration/buddyboss-intergrations-current-fields.php';
new BuddyBoss_Platform_Addon_BuddyBoss_Integration_Current_Fields();

/**************************************** MY PLUGIN INTEGRATION ************************************/

/**
 * Set up the my plugin integration.
 */
function buddyboss_platform_addon_register_integration() {
	require_once dirname( __FILE__ ) . '/integration/buddyboss-integration.php';
	buddypress()->integrations['addon'] = new BuddyBoss_Platform_Addon_BuddyBoss_Integration();
}
add_action( 'bp_setup_integrations', 'buddyboss_platform_addon_register_integration' );
