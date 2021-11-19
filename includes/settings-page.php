<?php
/**
 * Setting Options page for rt-scripts-optimizer plugin.
 *
 * This page will allow users to exclude scripts from being included in script optimizer.
 *
 * @package RT_Script_Optimizer
 */

/**
 * Custom option's and settings
 */
function rt_settings_init() {

	// Register a new setting options.
	register_setting( 'rt-scripts-optimizer-settings', 'rt_scripts_optimizer_exclude_paths' );
	register_setting( 'rt-scripts-optimizer-settings', 'rt_scripts_optimizer_exclude_handles' );

	// Register a new section.
	add_settings_section(
		'rt_scripts_optimizer_settings_section',                            // ID.
		__( 'Script\'s Optimizer Settings', 'RT_Script_Optimizer' ),          // Title.
		'rt_scripts_optimizer_settings_callback',                           // Callback Function.
		'rt-scripts-optimizer-settings'                                     // Page.
	);

	// Register a new field to fetch paths of scripts to exclude.
	add_settings_field(
		'rt_scripts_optimizer_path_field',                              // As of WP 4.6 this value is used only internally.
		__( 'Script path\'s', 'RT_Script_Optimizer' ),                    // Title.
		'rt_scripts_optimizer_paths_field_callback',                    // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section',                        // Section.
	);

	// Register a new field to fetch handles of scripts to exclude.
	add_settings_field(
		'rt_scripts_optimizer_handle_field',                            // As of WP 4.6 this value is used only internally.
		__( 'Script handle\'s', 'RT_Script_Optimizer' ),                  // Title.
		'rt_scripts_optimizer_handles_field_callback',                  // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section',                        // Section.
	);
}

/**
 * Register settings to the admin_init action hook.
 */
add_action( 'admin_init', 'rt_settings_init' );


/**
 * Section Description callback.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_settings_callback( $args ) {
	?>
		<p>
			<?php esc_html_e( 'Add Scripts you want to exclude from the optimizer by providing it\'s handle or path.', 'RT_Script_Optimizer' ); ?>
		</p>
	<?php
}

/**
 * Field callback to accept handles to exclude.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_handles_field_callback( $args ) {

	// option value.
	$handles = get_option( 'rt_scripts_optimizer_exclude_handles' );
	?>

	<input type="text"
		id="rt_optimizer_handles"
		name="rt_scripts_optimizer_exclude_handles"
		value="<?php echo esc_attr( $handles ); ?>"
		style="width:80%;"
	>

	<br>

	<p class = 'description' >
		<?php esc_html_e( 'Adding script handles to this field will exclude them from optimizer.', 'RT_Script_Optimizer' ); ?>
	</p>
	<?php
}

/**
 * Field callback to accept paths of scripts to exclude.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_paths_field_callback( $args ) {

	// option value.
	$paths = get_option( 'rt_scripts_optimizer_exclude_paths' );
	?>

	<input type="text"
		id="rt_optimizer_paths"
		name="rt_scripts_optimizer_exclude_paths"
		value="<?php echo esc_attr( $paths ); ?>"
		style="width:80%;"
	>

	<br>

	<p class = 'description' >
		<?php esc_html_e( 'Adding script path to this field will exclude them from optimizer.', 'RT_Script_Optimizer' ); ?>
	</p>
	<?php
}

// Add action to add options page.
add_action( 'admin_menu', 'rt_scripts_optimizer_options_submenu' );

/**
 * Option page submenu callback.
 */
function rt_scripts_optimizer_options_submenu() {

	add_options_page(
		__( 'rt-scripts Optimizer', 'RT_Script_Optimizer' ),
		__( 'Script\'s Optimizer', 'RT_Script_Optimizer' ),
		'manage_options',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_settings_template'
	);

}


/**
 * Top level menu callback function
 */
function rt_scripts_optimizer_settings_template() {

	// check if user can edit the setting.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	?>
	<div>
		<h1>
			<?php echo esc_html( get_admin_page_title() ); ?>
		</h1>
		<br><br>
		<form action="options.php" method="post">
			<?php

			// output settings fields for the registered setting "RT_Script_Optimizer".
			settings_fields( 'rt-scripts-optimizer-settings' );

			// setting sections and their fields.
			do_settings_sections( 'rt-scripts-optimizer-settings' );

			// output save settings button.
			submit_button( __( 'Save Settings', 'RT_Script_Optimizer' ) );

			?>
		</form>
	</div>
	<?php
}
