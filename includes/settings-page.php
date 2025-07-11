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
function rt_settings_init()
{

	// Register new setting options.
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_exclude_paths');
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_exclude_handles');
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_style_dequeue_non_logged_handles');
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_style_async_handles');
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_style_async_handles_onevent');
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_load_amp_boilerplate_style');
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_skip_css_concatination_all');
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_skip_css_concatination_handles');
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_comment_out_style_handles');
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_disable_js_optimizations');
	register_setting('rt-scripts-optimizer-settings', 'rt_scripts_optimizer_disable_css_optimizations');

	// Register a new section.
	add_settings_section(
		'rt_scripts_optimizer_settings_section',                            // ID.
		__('RT Scripts Optimizer Settings', 'RT_Script_Optimizer'),        // Title.
		'rt_scripts_optimizer_settings_callback',                           // Callback Function.
		'rt-scripts-optimizer-settings'                                     // Page.
	);

	// Register a new field to fetch paths of scripts to exclude.
	add_settings_field(
		'rt_scripts_optimizer_path_field',                              // As of WP 4.6 this value is used only internally.
		__('Load js normally by adding script path here', 'RT_Script_Optimizer'),                    // Title.
		'rt_scripts_optimizer_paths_field_callback',                    // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section'                         // Section.
	);

	// Register a new field to fetch handles of scripts to exclude.
	add_settings_field(
		'rt_scripts_optimizer_handle_field',                            // As of WP 4.6 this value is used only internally.
		__('Load js normally by adding script handles', 'RT_Script_Optimizer'),                  // Title.
		'rt_scripts_optimizer_handles_field_callback',                  // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section'                         // Section.
	);

	// Register a new field to fetch handles of styles to be dequeued for non-logged in users.
	add_settings_field(
		'rt_scripts_optimizer_style_dequeue_non_logged_handles',                            // As of WP 4.6 this value is used only internally.
		__('CSS handles of the stylesheets which should not be loaded if user not logged in', 'RT_Script_Optimizer'),                  // Title.
		'rt_scripts_optimizer_style_dequeue_non_logged_handles_callback',                  // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section'                         // Section.
	);

	// Register a new field to fetch handles of styles to be loaded async.
	add_settings_field(
		'rt_scripts_optimizer_style_async_handles',                            // As of WP 4.6 this value is used only internally.
		__('CSS handles of the stylesheets which should be asynchronously loaded', 'RT_Script_Optimizer'),                  // Title.
		'rt_scripts_optimizer_style_async_handles_callback',                  // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section'                         // Section.
	);

	// Register a new field to fetch handles of styles to be loaded on any window event.
	add_settings_field(
		'rt_scripts_optimizer_style_async_handles_onevent',                            // As of WP 4.6 this value is used only internally.
		__('CSS handles of the stylesheets which should be asynchronously loaded on any window event', 'RT_Script_Optimizer'),                  // Title.
		'rt_scripts_optimizer_style_async_handles_onevent_callback',                  // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section'                         // Section.
	);

	// Register a new field to fetch option whether to load amp-boilerplate style.
	add_settings_field(
		'rt_scripts_optimizer_load_amp_boilerplate_style',                            // As of WP 4.6 this value is used only internally.
		__('Load AMP boilerplate CSS', 'RT_Script_Optimizer'),                  // Title.
		'rt_scripts_optimizer_load_amp_boilerplate_style_callback',                  // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section'                         // Section.
	);

	// Register a new field to fetch option whether to skip all CSS concatination.
	add_settings_field(
		'rt_scripts_optimizer_skip_css_concatination_all',                            // As of WP 4.6 this value is used only internally.
		__('Skip all CSS concatination', 'RT_Script_Optimizer'),                  // Title.
		'rt_scripts_optimizer_skip_css_concatination_all_callback',                  // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section'                         // Section.
	);

	// Register a new field to fetch handles of stylesheets which are not to be concated.
	add_settings_field(
		'rt_scripts_optimizer_skip_css_concatination_handles',                            // As of WP 4.6 this value is used only internally.
		__('Skip CSS concatination for these handles', 'RT_Script_Optimizer'),                  // Title.
		'rt_scripts_optimizer_skip_css_concatination_handles_callback',                  // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section'                         // Section.
	);

	// Register a new field to fetch handles of stylesheets which are to be commented out.
	add_settings_field(
		'rt_scripts_optimizer_comment_out_style_handles',                            // As of WP 4.6 this value is used only internally.
		__('Comment out CSS by handle', 'RT_Script_Optimizer'),                  // Title.
		'rt_scripts_optimizer_comment_out_style_handles_callback',                  // Callback Function.
		'rt-scripts-optimizer-settings',                                // Page.
		'rt_scripts_optimizer_settings_section'                         // Section.
	);

	// Register a new field to disable JS optimizations.
	add_settings_field(
		'rt_scripts_optimizer_disable_js_optimizations',
		__('Disable JS Optimizations', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_disable_js_optimizations_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_settings_section'
	);

	// Register a new field to disable CSS optimizations.
	add_settings_field(
		'rt_scripts_optimizer_disable_css_optimizations',
		__('Disable CSS Optimizations', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_disable_css_optimizations_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_settings_section'
	);
}

/**
 * Register settings to the admin_init action hook.
 */
add_action('admin_init', 'rt_settings_init');


/**
 * Section Description callback.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_settings_callback($args)
{
?>
	<p>
		<?php esc_html_e('Add scripts to exclude from the RT Scripts Optimizer by providing it\'s handle or path.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to accept handles to exclude.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_handles_field_callback($args)
{

	// option value.
	$handles = get_option('rt_scripts_optimizer_exclude_handles');
?>

	<input type="text"
		id="rt_optimizer_handles"
		name="rt_scripts_optimizer_exclude_handles"
		value="<?php echo esc_attr($handles); ?>"
		style="width:80%;">

	<br>

	<p class='description'>
		<?php esc_html_e('Adding script handles to this field will exclude them from optimizer and load them normally.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to accept handles of stylesheets to be dequeued when user not logged in.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_style_dequeue_non_logged_handles_callback($args)
{

	// option value.
	$paths = get_option('rt_scripts_optimizer_style_dequeue_non_logged_handles');
?>

	<input type="text"
		id="rt_optimizer_style_dequeue_non_logged_handles"
		name="rt_scripts_optimizer_style_dequeue_non_logged_handles"
		value="<?php echo esc_attr($paths); ?>"
		style="width:80%;">

	<br>

	<p class='description'>
		<?php esc_html_e('Adding stylesheets\' handles here will make them be dequeued when user not logged in.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to accept handles of stylesheets to be loaded asynchronously.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_style_async_handles_callback($args)
{

	// option value.
	$paths = get_option('rt_scripts_optimizer_style_async_handles');
?>

	<input type="text"
		id="rt_optimizer_style_async_handles"
		name="rt_scripts_optimizer_style_async_handles"
		value="<?php echo esc_attr($paths); ?>"
		style="width:80%;">

	<br>

	<p class='description'>
		<?php esc_html_e('Adding stylesheets\' handle here will make them load asynchronously automatically.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to accept handles of stylesheets to be loaded asynchronously on windows event.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_style_async_handles_onevent_callback($args)
{

	// option value.
	$paths = get_option('rt_scripts_optimizer_style_async_handles_onevent');
?>

	<input type="text"
		id="rt_optimizer_style_async_on_event_handles"
		name="rt_scripts_optimizer_style_async_handles_onevent"
		value="<?php echo esc_attr($paths); ?>"
		style="width:80%;">

	<br>

	<p class='description'>
		<?php esc_html_e('Adding stylesheets\' handle here will make them load asynchronously on windows event.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to take input of whether to include amp-boilerplate css or not.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_load_amp_boilerplate_style_callback($args)
{

	// option value.
	$load_amp_css = get_option('rt_scripts_optimizer_load_amp_boilerplate_style');
?>

	<input type="checkbox" id="rt_optimizer_load_amp_css" name="rt_scripts_optimizer_load_amp_boilerplate_style" value="1" <?php checked($load_amp_css, '1', true); ?>>

	<br>

	<p class='description'>
		<?php esc_html_e('Check this if you want to load AMP boilerplate CSS to avoid CLS issue.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to take input of whether to skip all CSS concatination or not.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_skip_css_concatination_all_callback($args)
{

	// option value.
	$skip_css_concatination = get_option('rt_scripts_optimizer_skip_css_concatination_all');
?>

	<input type="checkbox" id="rt_optimizer_skip_css_concatination_all" name="rt_scripts_optimizer_skip_css_concatination_all" value="1" <?php checked($skip_css_concatination, '1', true); ?>>

	<br>

	<p class='description'>
		<?php esc_html_e('Check this if you want to disable CSS concatination completely. If this is checked then the below field have no effect.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to take input of stylesheet handles which are not to be concated.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_skip_css_concatination_handles_callback($args)
{

	// option value.
	$handles = get_option('rt_scripts_optimizer_skip_css_concatination_handles');
?>

	<input type="text"
		id="rt_optimizer_skip_css_concatination_handles"
		name="rt_scripts_optimizer_skip_css_concatination_handles"
		value="<?php echo esc_attr($handles); ?>"
		style="width:80%;">

	<br>

	<p class='description'>
		<?php esc_html_e('Disable CSS concatination of the supplied handles. If the skip all concatination checkbox is checked then these values will have no effect.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to accept handles of stylesheets to be commented out.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_comment_out_style_handles_callback($args)
{

	// option value.
	$handles = get_option('rt_scripts_optimizer_comment_out_style_handles');
?>

	<input type="text"
		id="rt_optimizer_comment_out_style_handles"
		name="rt_scripts_optimizer_comment_out_style_handles"
		value="<?php echo esc_attr($handles); ?>"
		style="width:80%;">

	<br>

	<p class='description'>
		<?php esc_html_e('Adding stylesheets\' handles here will comment them out in the HTML, preventing them from loading.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to disable JS optimizations.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_disable_js_optimizations_callback($args)
{

	// option value.
	$disable_js_optimizations = get_option('rt_scripts_optimizer_disable_js_optimizations');
?>

	<input type="checkbox" id="rt_optimizer_disable_js_optimizations" name="rt_scripts_optimizer_disable_js_optimizations" value="1" <?php checked($disable_js_optimizations, '1', true); ?> >

	<br>

	<p class='description'>
		<?php esc_html_e('Check this if you want to disable all JS optimizations.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to disable CSS optimizations.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_disable_css_optimizations_callback($args)
{

	// option value.
	$disable_css_optimizations = get_option('rt_scripts_optimizer_disable_css_optimizations');
?>

	<input type="checkbox" id="rt_optimizer_disable_css_optimizations" name="rt_scripts_optimizer_disable_css_optimizations" value="1" <?php checked($disable_css_optimizations, '1', true); ?> >

	<br>

	<p class='description'>
		<?php esc_html_e('Check this if you want to disable all CSS optimizations.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to disable JS optimizations.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_disable_js_optimizations_callback($args)
{

	// option value.
	$disable_js_optimizations = get_option('rt_scripts_optimizer_disable_js_optimizations');
?>

	<input type="checkbox" id="rt_optimizer_disable_js_optimizations" name="rt_scripts_optimizer_disable_js_optimizations" value="1" <?php checked($disable_js_optimizations, '1', true); ?>>

	<br>

	<p class='description'>
		<?php esc_html_e('Check this if you want to disable all JS optimizations.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to disable CSS optimizations.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_disable_css_optimizations_callback($args)
{

	// option value.
	$disable_css_optimizations = get_option('rt_scripts_optimizer_disable_css_optimizations');
?>

	<input type="checkbox" id="rt_optimizer_disable_css_optimizations" name="rt_scripts_optimizer_disable_css_optimizations" value="1" <?php checked($disable_css_optimizations, '1', true); ?>>

	<br>

	<p class='description'>
		<?php esc_html_e('Check this if you want to disable all CSS optimizations.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * Field callback to accept paths of scripts to exclude.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_paths_field_callback($args)
{

	// option value.
	$paths = get_option('rt_scripts_optimizer_exclude_paths');
?>

	<input type="text"
		id="rt_optimizer_paths"
		name="rt_scripts_optimizer_exclude_paths"
		value="<?php echo esc_attr($paths); ?>"
		style="width:80%;">

	<br>

	<p class='description'>
		<?php esc_html_e('Adding script path to this field will exclude them from optimizer and load them normally.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

// Add action to add options page.
add_action('admin_menu', 'rt_scripts_optimizer_options_submenu');

/**
 * Option page submenu callback.
 */
function rt_scripts_optimizer_options_submenu()
{

	add_options_page(
		__('RT Scripts Optimizer', 'RT_Script_Optimizer'),
		__('RT Scripts Optimizer', 'RT_Script_Optimizer'),
		'manage_options',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_settings_template'
	);
}


/**
 * Top level menu callback function
 */
function rt_scripts_optimizer_settings_template()
{

	// check if user can edit the setting.
	if (! current_user_can('manage_options')) {
		return;
	}

?>
	<div>
		<h1>
			<?php echo esc_html(get_admin_page_title()); ?>
		</h1>
		<br><br>
		<form action="options.php" method="post">
			<?php

			// output settings fields for the registered setting "RT_Script_Optimizer".
			settings_fields('rt-scripts-optimizer-settings');

			// setting sections and their fields.
			do_settings_sections('rt-scripts-optimizer-settings');

			// output save settings button.
			submit_button(__('Save Settings', 'RT_Script_Optimizer'));

			?>
		</form>
	</div>
<?php
}
