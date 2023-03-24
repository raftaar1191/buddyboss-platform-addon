<?php
/**
 * Compatibility integration admin tab
 *
 * @since BuddyBoss 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup Compatibility integration admin tab class.
 *
 * @since BuddyBoss 1.0.0
 */
class BuddyBoss_Platform_Addon_BuddyBoss_Admin_Integration_Tab extends BP_Admin_Integration_tab {

	public function initialize() {
		$this->tab_order       = 60;
	}

	public function is_active() {
		return true;
	}

	public function is_addon_field_enabled( $default = 1 ) {
		return get_option( 'buddyboss-platform-addon-field', $default );
	}

	/**
	 * Setting fields callback
	 */
	public function settings_callback_field() {
		?>
        <input name="buddyboss-platform-addon-field"
               id="buddyboss-platform-addon-field"
               type="checkbox"
               value="1"
			<?php checked( $this->is_addon_field_enabled() ); ?>
        />
        <label for="buddyboss-platform-addon-field">
			<?php _e( 'Enable this option', 'buddyboss-platform-addon' ); ?>
        </label>
		<?php
	}

	/**
	 * All the setting of the fields
	 */
	public function get_fields_settings() {

		$fields['buddyboss_platform_addon_settings_section'] = array(
			'buddyboss-platform-addon-field' => array(
				'title'             => __( 'Addon Field', 'buddyboss-platform-addon' ),
				'callback'          => array( $this, 'settings_callback_field' ),
				'sanitize_callback' => '',
				'args'              => array(),
			),
		);

		return $fields;
	}

	/**
	 * Add new setting sections
	 */
	public function get_settings_sections() {
		return array(
			'buddyboss_platform_addon_settings_section' => array(
				'page'  => 'bp-buddyboss-platform-addon',
				'title' => __( 'Addon Settings', 'buddyboss-platform-addon' ),
			),
		);
	}

	/**
	 * Register setting fields
	 */
	public function register_fields() {

		$sections = $this->get_settings_sections();

		foreach ( (array) $sections as $section_id => $section ) {

			$fields_settings = $this->get_fields_settings();
			$fields = isset( $fields_settings[ $section_id ] ) ? $fields_settings[ $section_id ] : false;

			if ( empty( $fields ) ) {
				continue;
			}

			$section_title    = ! empty( $section['title'] ) ? $section['title'] : '';
			$section_callback = ! empty( $section['callback'] ) ? $section['callback'] : false;

			// Add the section
			$this->add_section( $section_id, $section_title, $section_callback );

			// Loop through fields for this section
			foreach ( (array) $fields as $field_id => $field ) {

				$field['args'] = isset( $field['args'] ) ? $field['args'] : array();

				if ( ! empty( $field['callback'] ) && ! empty( $field['title'] ) ) {
					$sanitize_callback = isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : [];
					$this->add_field( $field_id, $field['title'], $field['callback'], $sanitize_callback, $field['args'] );
				}
			}
		}
	}
}
