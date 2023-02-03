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

	// Register new setting options.
	$script_settings = [
		'rt_scripts_optimizer_exclude_paths'                    => [
			'id'       => 'rt_scripts_optimizer_path_field',
			'title'    => __( 'Load js normally by adding script path here', 'RT_Script_Optimizer' ),
			'callback' => 'rt_scripts_optimizer_paths_field_callback',
			'page'     => 'rt-scripts-optimizer-settings',
			'section'  => 'rt_scripts_optimizer_settings_section'
		],
		'rt_scripts_optimizer_exclude_handles'                  => [
			'id'       => 'rt_scripts_optimizer_handle_field',
			'title'    => __( 'Load js normally by adding script handles', 'RT_Script_Optimizer' ),
			'callback' => 'rt_scripts_optimizer_handles_field_callback',
			'page'     => 'rt-scripts-optimizer-settings',
			'section'  => 'rt_scripts_optimizer_settings_section'
		],
		'rt_scripts_optimizer_style_dequeue_non_logged_handles' => [
			'id'       => 'rt_scripts_optimizer_style_dequeue_non_logged_handles',
			'title'    => __( 'CSS handles of the stylesheets which should not be loaded if user not logged in', 'RT_Script_Optimizer' ),
			'callback' => 'rt_scripts_optimizer_style_dequeue_non_logged_handles_callback',
			'page'     => 'rt-scripts-optimizer-settings',
			'section'  => 'rt_scripts_optimizer_settings_section'
		],
		'rt_scripts_optimizer_style_async_handles'              => [
			'id'       => 'rt_scripts_optimizer_style_async_handles',
			'title'    => __( 'CSS handles of the stylesheets which should be asynchronously loaded', 'RT_Script_Optimizer' ),
			'callback' => 'rt_scripts_optimizer_style_async_handles_callback',
			'page'     => 'rt-scripts-optimizer-settings',
			'section'  => 'rt_scripts_optimizer_settings_section'
		],
		'rt_scripts_optimizer_style_async_handles_onevent'      => [
			'id'       => 'rt_scripts_optimizer_style_async_handles_onevent',
			'title'    => __( 'CSS handles of the stylesheets which should be asynchronously loaded on any window event', 'RT_Script_Optimizer' ),
			'callback' => 'rt_scripts_optimizer_style_async_handles_onevent_callback',
			'page'     => 'rt-scripts-optimizer-settings',
			'section'  => 'rt_scripts_optimizer_settings_section'
		],
		'rt_scripts_optimizer_load_amp_boilerplate_style'       => [
			'id'       => 'rt_scripts_optimizer_load_amp_boilerplate_style',
			'title'    => __( 'Load AMP boilerplate CSS', 'RT_Script_Optimizer' ),
			'callback' => 'rt_scripts_optimizer_load_amp_boilerplate_style_callback',
			'page'     => 'rt-scripts-optimizer-settings',
			'section'  => 'rt_scripts_optimizer_settings_section'
		],
		'rt_scripts_optimizer_skip_css_concatination_all'       => [
			'id'       => 'rt_scripts_optimizer_skip_css_concatination_all',
			'title'    => __( 'Skip all CSS concatination', 'RT_Script_Optimizer' ),
			'callback' => 'rt_scripts_optimizer_skip_css_concatination_all_callback',
			'page'     => 'rt-scripts-optimizer-settings',
			'section'  => 'rt_scripts_optimizer_settings_section'
		],
		'rt_scripts_optimizer_skip_css_concatination_handles'   => [
			'id'       => 'rt_scripts_optimizer_skip_css_concatination_handles',
			'title'    => __( 'Skip CSS concatination for these handles', 'RT_Script_Optimizer' ),
			'callback' => 'rt_scripts_optimizer_skip_css_concatination_handles_callback',
			'page'     => 'rt-scripts-optimizer-settings',
			'section'  => 'rt_scripts_optimizer_settings_section'
		]
	];

	/**
	 * Add fields from settings array to following core functions.
	 * 
	 * add_settings_field( $id, $title, $callback, $page, $section )
	 * - https://developer.wordpress.org/reference/functions/add_settings_field/
	 * 
	 * register_setting( $option_group, $option_name )
	 * - https://developer.wordpress.org/reference/functions/register_setting/
	 * 
	 */
	foreach ( $script_settings as $option_name => $settings ) {

		add_settings_field(
			$settings[ 'id' ],
			$settings[ 'title' ],
			$settings[ 'callback' ],
			$settings[ 'page' ],
			$settings[ 'page' ]
		);

		register_setting( 'rt-scripts-optimizer-settings', $option_name );
	}

	// Register a new section.
	add_settings_section(
		'rt_scripts_optimizer_settings_section',                            // ID.
		__( 'RT Scripts Optimizer Settings', 'RT_Script_Optimizer' ),        // Title.
		'rt_scripts_optimizer_settings_callback',                           // Callback Function.
		'rt-scripts-optimizer-settings'                                     // Page.
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
			<?php esc_html_e( 'Add scripts to exclude from the RT Scripts Optimizer by providing it\'s handle or path.', 'RT_Script_Optimizer' ); ?>
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

	<p class='description'>
		<?php esc_html_e( 'Adding script handles to this field will exclude them from optimizer and load them normally.', 'RT_Script_Optimizer' ); ?>
	</p>
	<?php
}

/**
 * Field callback to accept handles of stylesheets to be dequeued when user not logged in.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_style_dequeue_non_logged_handles_callback( $args ) {

	// option value.
	$paths = get_option( 'rt_scripts_optimizer_style_dequeue_non_logged_handles' );
	?>

	<input type="text"
		id="rt_optimizer_style_dequeue_non_logged_handles"
		name="rt_scripts_optimizer_style_dequeue_non_logged_handles"
		value="<?php echo esc_attr( $paths ); ?>"
		style="width:80%;"
	>

	<br>

	<p class='description'>
		<?php esc_html_e( 'Adding stylesheets\' handles here will make them be dequeued when user not logged in.', 'RT_Script_Optimizer' ); ?>
	</p>
	<?php
}

/**
 * Field callback to accept handles of stylesheets to be loaded asynchronously.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_style_async_handles_callback( $args ) {

	// option value.
	$paths = get_option( 'rt_scripts_optimizer_style_async_handles' );
	?>

	<input type="text"
		id="rt_optimizer_style_async_handles"
		name="rt_scripts_optimizer_style_async_handles"
		value="<?php echo esc_attr( $paths ); ?>"
		style="width:80%;"
	>

	<br>

	<p class='description'>
		<?php esc_html_e( 'Adding stylesheets\' handle here will make them load asynchronously automatically.', 'RT_Script_Optimizer' ); ?>
	</p>
	<?php
}

/**
 * Field callback to accept handles of stylesheets to be loaded asynchronously on windows event.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_style_async_handles_onevent_callback( $args ) {

	// option value.
	$paths = get_option( 'rt_scripts_optimizer_style_async_handles_onevent' );
	?>

	<input type="text"
		id="rt_optimizer_style_async_on_event_handles"
		name="rt_scripts_optimizer_style_async_handles_onevent"
		value="<?php echo esc_attr( $paths ); ?>"
		style="width:80%;"
	>

	<br>

	<p class='description'>
		<?php esc_html_e( 'Adding stylesheets\' handle here will make them load asynchronously on windows event.', 'RT_Script_Optimizer' ); ?>
	</p>
	<?php
}

/**
 * Field callback to take input of whether to include amp-boilerplate css or not.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_load_amp_boilerplate_style_callback( $args ) {

	// option value.
	$load_amp_css = get_option( 'rt_scripts_optimizer_load_amp_boilerplate_style' );
	?>

	<input type="checkbox" id="rt_optimizer_load_amp_css" name="rt_scripts_optimizer_load_amp_boilerplate_style" value="1" <?php checked( $load_amp_css, '1', true ); ?>>

	<br>

	<p class='description'>
		<?php esc_html_e( 'Check this if you want to load AMP boilerplate CSS to avoid CLS issue.', 'RT_Script_Optimizer' ); ?>
	</p>
	<?php
}

/**
 * Field callback to take input of whether to skip all CSS concatination or not.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_skip_css_concatination_all_callback( $args ) {

	// option value.
	$skip_css_concatination = get_option( 'rt_scripts_optimizer_skip_css_concatination_all' );
	?>

	<input type="checkbox" id="rt_optimizer_skip_css_concatination_all" name="rt_scripts_optimizer_skip_css_concatination_all" value="1" <?php checked( $skip_css_concatination, '1', true ); ?>>

	<br>

	<p class='description'>
		<?php esc_html_e( 'Check this if you want to disable CSS concatination completely. If this is checked then the below field have no effect.', 'RT_Script_Optimizer' ); ?>
	</p>
	<?php
}

/**
 * Field callback to take input of stylesheet handles which are not to be concated.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_skip_css_concatination_handles_callback( $args ) {

	// option value.
	$handles = get_option( 'rt_scripts_optimizer_skip_css_concatination_handles' );
	?>

	<input type="text"
		id="rt_optimizer_skip_css_concatination_handles"
		name="rt_scripts_optimizer_skip_css_concatination_handles"
		value="<?php echo esc_attr( $handles ); ?>"
		style="width:80%;"
	>

	<br>

	<p class='description'>
		<?php esc_html_e( 'Disable CSS concatination of the supplied handles. If the skip all concatination checkbox is checked then these values will have no effect.', 'RT_Script_Optimizer' ); ?>
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

	<p class='description'>
		<?php esc_html_e( 'Adding script path to this field will exclude them from optimizer and load them normally.', 'RT_Script_Optimizer' ); ?>
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
		__( 'RT Scripts Optimizer', 'RT_Script_Optimizer' ),
		__( 'RT Scripts Optimizer', 'RT_Script_Optimizer' ),
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
