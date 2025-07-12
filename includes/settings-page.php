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

	// Register a new section for JS optimizations.
	add_settings_section(
		'rt_scripts_optimizer_js_settings_section',
		__('JavaScript Optimizations', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_js_settings_callback',
		'rt-scripts-optimizer-settings'
	);

	// Register a new field to disable JS optimizations.
	add_settings_field(
		'rt_scripts_optimizer_disable_js_optimizations',
		__('Disable JS Optimizations', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_disable_js_optimizations_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_js_settings_section'
	);

	// Register a new field to fetch paths of scripts to exclude.
	add_settings_field(
		'rt_scripts_optimizer_path_field',
		__('Load js normally by adding script path here', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_paths_field_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_js_settings_section'
	);

	// Register a new field to fetch handles of scripts to exclude.
	add_settings_field(
		'rt_scripts_optimizer_handle_field',
		__('Load js normally by adding script handles', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_handles_field_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_js_settings_section'
	);

	// Register a new section for CSS optimizations.
	add_settings_section(
		'rt_scripts_optimizer_css_settings_section',
		__('CSS Optimizations', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_css_settings_callback',
		'rt-scripts-optimizer-settings'
	);

	// Register a new field to disable CSS optimizations.
	add_settings_field(
		'rt_scripts_optimizer_disable_css_optimizations',
		__('Disable CSS Optimizations', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_disable_css_optimizations_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_css_settings_section'
	);

	// Register a new field to fetch handles of styles to be dequeued for non-logged in users.
	add_settings_field(
		'rt_scripts_optimizer_style_dequeue_non_logged_handles',
		__('CSS handles of the stylesheets which should not be loaded if user not logged in', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_style_dequeue_non_logged_handles_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_css_settings_section'
	);

	// Register a new field to fetch handles of styles to be loaded async.
	add_settings_field(
		'rt_scripts_optimizer_style_async_handles',
		__('CSS handles of the stylesheets which should be asynchronously loaded', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_style_async_handles_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_css_settings_section'
	);

	// Register a new field to fetch handles of styles to be loaded on any window event.
	add_settings_field(
		'rt_scripts_optimizer_style_async_handles_onevent',
		__('CSS handles of the stylesheets which should be asynchronously loaded on any window event', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_style_async_handles_onevent_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_css_settings_section'
	);

	// Register a new field to fetch option whether to load amp-boilerplate style.
	add_settings_field(
		'rt_scripts_optimizer_load_amp_boilerplate_style',
		__('Load AMP boilerplate CSS', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_load_amp_boilerplate_style_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_css_settings_section'
	);

	// Register a new field to fetch option whether to skip all CSS concatination.
	add_settings_field(
		'rt_scripts_optimizer_skip_css_concatination_all',
		__('Skip all CSS concatenation', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_skip_css_concatination_all_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_css_settings_section'
	);

	// Register a new field to fetch handles of stylesheets which are not to be concated.
	add_settings_field(
		'rt_scripts_optimizer_skip_css_concatination_handles',
		__('Skip CSS concatenation for these handles', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_skip_css_concatination_handles_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_css_settings_section'
	);

	// Register a new field to fetch handles of stylesheets which are to be commented out.
	add_settings_field(
		'rt_scripts_optimizer_comment_out_style_handles',
		__('Comment out CSS by handle', 'RT_Script_Optimizer'),
		'rt_scripts_optimizer_comment_out_style_handles_callback',
		'rt-scripts-optimizer-settings',
		'rt_scripts_optimizer_css_settings_section'
	);
}

/**
 * Register settings to the admin_init action hook.
 */
add_action('admin_init', 'rt_settings_init');

/**
 * JS Section Description callback.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_js_settings_callback($args)
{
?>
	<p>
		<?php esc_html_e('Control JavaScript optimization settings. You can disable all JS optimizations using the master switch below.', 'RT_Script_Optimizer'); ?>
	</p>
<?php
}

/**
 * CSS Section Description callback.
 *
 * @param array $args arguments passed.
 */
function rt_scripts_optimizer_css_settings_callback($args)
{
?>
	<hr />
	<p>
		<?php esc_html_e('Control CSS optimization settings. You can disable all CSS optimizations using the master switch below.', 'RT_Script_Optimizer'); ?>
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
		<?php esc_html_e('Check this if you want to disable CSS concatenation completely. If this is checked then the below field have no effect.', 'RT_Script_Optimizer'); ?>
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
		<?php esc_html_e('Disable CSS concatenation of the supplied handles. If the skip all concatenation checkbox is checked then these values will have no effect.', 'RT_Script_Optimizer'); ?>
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
	$disable_js_optimizations = get_option('rt_scripts_optimizer_disable_js_optimizations', '1');
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
	$disable_css_optimizations = get_option('rt_scripts_optimizer_disable_css_optimizations', '1');
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
	<div class="wrap">
		<h1>
			<?php echo esc_html(get_admin_page_title()); ?>
		</h1>

		<style>
			.rt-optimizer-section-wrapper {
				background: #fff;
				border: 1px solid #c3c4c6;
				border-radius: 4px;
				box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
				margin-top: 20px;
				padding: 0;
			}

			.rt-optimizer-section-wrapper h2 {
				font-size: 1.1em;
				margin: 0;
				padding: 1em 1.5em;
				border-bottom: 1px solid #c3c4c6;
			}

			.rt-optimizer-section-wrapper .form-table {
				margin: 0;
				padding: 1em 1.5em;
			}

			.rt-optimizer-section-wrapper .form-table tr:not(:first-of-type) {
				border-left: 4px solid #7e8993;
			}

			.rt-optimizer-section-wrapper .form-table tr:not(:first-of-type) th,
			.rt-optimizer-section-wrapper .form-table tr:not(:first-of-type) td {
				padding-left: 25px;
			}
		</style>

		<form action="options.php" method="post">
			<?php

			// output settings fields for the registered setting "RT_Script_Optimizer".
			settings_fields('rt-scripts-optimizer-settings');

			// Manually render sections to add wrapper div and avoid FOUC.
			global $wp_settings_sections, $wp_settings_fields;
			$page = 'rt-scripts-optimizer-settings';

			if (!isset($wp_settings_sections[$page])) {
				return;
			}

			foreach ((array) $wp_settings_sections[$page] as $section) {
				echo '<div class="rt-optimizer-section-wrapper">';

				if ($section['title']) {
					echo '<h2>' . esc_html($section['title']) . '</h2>' . "\n";
				}

				if ($section['callback']) {
					call_user_func($section['callback'], $section);
				}

				if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']])) {
					echo '</div>';
					continue;
				}

				echo '<table class="form-table" role="presentation">';
				do_settings_fields($page, $section['id']);
				echo '</table>';

				echo '</div>';
			}

			// output save settings button.
			submit_button(__('Save Settings', 'RT_Script_Optimizer'));

			?>
		</form>
	</div>
	<script>
		jQuery(document).ready(function($) {
			// JS Optimizations
			const jsToggle = $('#rt_optimizer_disable_js_optimizations');
			const jsSubOptions = jsToggle.closest('tr').nextAll();

			function toggleJsOptions() {
				if (jsToggle.is(':checked')) {
					jsSubOptions.css('opacity', '0.5');
					jsSubOptions.find('input, textarea, select').prop('disabled', true);
				} else {
					jsSubOptions.css('opacity', '1');
					jsSubOptions.find('input, textarea, select').prop('disabled', false);
				}
			}

			jsToggle.on('change', toggleJsOptions);
			toggleJsOptions();

			// CSS Optimizations
			const cssToggle = $('#rt_optimizer_disable_css_optimizations');
			const cssSubOptions = cssToggle.closest('tr').nextAll();

			function toggleCssOptions() {
				if (cssToggle.is(':checked')) {
					cssSubOptions.css('opacity', '0.5');
					cssSubOptions.find('input, textarea, select').prop('disabled', true);
				} else {
					cssSubOptions.css('opacity', '1');
					cssSubOptions.find('input, textarea, select').prop('disabled', false);
				}
			}
			cssToggle.on('change', toggleCssOptions);
			toggleCssOptions();
		});
	</script>
<?php
}
