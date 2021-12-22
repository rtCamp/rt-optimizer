<?php
/**
 * Plugin Name: RT Scripts Optimizer
 * Description: Loading scripts via worker thread for boosting up the site speed. Keeps the main thread idle for users to interact as quickly as possible.
 * Author: rtCamp, pradeep910
 * Plugin URI:  https://rtcamp.com
 * Author URI:  https://rtcamp.com
 * Version: 0.1
 * Text Domain: rt-script-optimizer
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package RT_Script_Optimizer
 */

define( 'RT_SCRIPTS_OPTIMIZER_DIR_PATH', plugin_dir_path( __FILE__ ) );

// Include settings options page.
require_once RT_SCRIPTS_OPTIMIZER_DIR_PATH . 'includes/settings-page.php';

// Skip if it is WP Backend.
if ( is_admin() ) {
	return;
}

// Skip if it is customizer preview.
if ( isset( $_REQUEST['customize_changeset_uuid'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return;
}

// Variable to store the scripts to be excluded.
$skip_js = array(
	'lodash',
	'wp-dom-ready',
	'wp-hooks',
	'wp-i18n',
);
/**
 * Head scripts
 */
function rt_head_scripts() {

	// If AMP page request, return nothing.
	if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
		return null;
	}
	?>
	<script type="text/worker" id="rtpwa">onmessage=function(e){var o=new Request(e.data,{mode:"no-cors",redirect:"follow"});fetch(o)};</script>
	<script type="text/javascript">var x = new Worker("data:text/javascript;base64," + btoa(document.getElementById("rtpwa").textContent));</script>
	<!-- Load the amp-boiler plate to show content after 0.5 seconds. Helps with CLS issue. Use selector (.site-content) of the content area after your <header> tag, so header displays always. -->
	<style amp-boilerplate>
		@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
		.site-content{-webkit-animation:-amp-start 1s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 1s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 1s steps(1,end) 0s 1 normal both;animation:-amp-start 1s steps(1,end) 0s 1 normal both}
		@media (min-width: 768px) {.site-content{-webkit-animation:-amp-start 0.5s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 0.5s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 0.5s steps(1,end) 0s 1 normal both;animation:-amp-start 0.5s steps(1,end) 0s 1 normal both} }
	</style>
	<noscript><style amp-boilerplate>.site-content{-webkit-animation: none;-moz-animation: none;-ms-animation: none;animation: none}</style></noscript>
	<?php
}
add_action( 'wp_head', 'rt_head_scripts', 0 );

/**
 * Footer scripts
 */
function rt_footer_scripts() {

	// Skip if it is backend or AMP page.
	if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
		return null;
	}
	?>
		<script type="text/javascript">const t = ["mouseover", "keydown", "touchmove", "touchstart", "scroll"]; t.forEach(function (t) { window.addEventListener(t, e, { passive: true }) }); function e() { n(); t.forEach(function (t) { window.removeEventListener(t, e, { passive: true }) }) } function c(t, e, n) { if (typeof n === "undefined") { n = 0 } t[n](function () { n++; if (n === t.length) { e() } else { c(t, e, n) } }) } function u() { var t = document.createEvent("Event"); t.initEvent("DOMContentLoaded", true, true); window.dispatchEvent(t); document.dispatchEvent(t); var e = document.createEvent("Event"); e.initEvent("readystatechange", true, true); window.dispatchEvent(e); document.dispatchEvent(e); var n = document.createEvent("Event"); n.initEvent("load", true, true); window.dispatchEvent(n); document.dispatchEvent(n); var o = document.createEvent("Event"); o.initEvent("show", true, true); window.dispatchEvent(o); document.dispatchEvent(o); var c = window.document.createEvent("UIEvents"); c.initUIEvent("resize", true, true, window, 0); window.dispatchEvent(c); document.dispatchEvent(c); } function rti(t, e) { var n = document.createElement("script"); n.type = "text/javascript"; if (t.src) { n.onload = e; n.onerror = e; n.src = t.src; n.id = t.id } else { n.textContent = t.innerText; n.id = t.id } t.parentNode.removeChild(t); document.body.appendChild(n); if (!t.src) { e() } } function n() { var t = document.querySelectorAll("script"); var n = []; var o;[].forEach.call(t, function (e) { o = e.getAttribute("type"); if (o == "text/rtscript") { n.push(function (t) { rti(e, t) }) } }); c(n, u) }</script>
	<?php
}
add_action( 'wp_footer', 'rt_footer_scripts' );

/**
 * Setting up scripts id and type attribute to identify which scripts to offload to worker thread and reduce main thread execution on load.
 * This function changes script attribute for delaying script execution. The scripts having type="text/rtscript" will be passed to Worker thread and
 * then returned back to DOM once user does any interactions with the site by the means of tap, scroll, keypress, or click.
 *
 * @param string $tag    The `<script>` tag for the enqueued script.
 * @param string $handle The script's registered handle.
 * @param string $src    The script's source URL.
 *
 * @return string
 */
function rt_scripts_handler( $tag, $handle, $src ) {

	global $skip_js;

	if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
		return $tag;
	}

	/**
	 * Checks if the plugin has to be disabled.
	 *
	 * Return true if it has to be disabled.
	 *
	 * @return bool.
	 */
	$disable_rt_optimzer = apply_filters( 'disable_rt_scripts_optimizer', false );

	if ( $disable_rt_optimzer ) {
		return $tag;
	}

	$handles_option_array = explode( ',', get_option( 'rt_scripts_optimizer_exclude_handles' ) );
	$paths_option_array   = explode( ',', get_option( 'rt_scripts_optimizer_exclude_paths' ) );

	// Get handle using the paths provided in the settings.
	foreach ( $paths_option_array as $key => $script_path ) {
		$script_path = trim( $script_path );
		if ( empty( $script_path ) ) {
			continue;
		}

		if ( strpos( $src, $script_path ) && ! in_array( $handle, $skip_js, true ) ) {
			array_push( $skip_js, $handle );
			break;
		}
	}

	$skip_js = array_unique( array_merge( $skip_js, $handles_option_array ) );

	$array_regenerator_runtime_script = array_search( 'regenerator-runtime', $skip_js, true );

	// If page is single post or page and the script is not in the skip_js array then skip regenerator-runtime script.
	if ( is_single() && ! $array_regenerator_runtime_script ) {
		array_push( $skip_js, 'regenerator-runtime' );
	} elseif ( $array_regenerator_runtime_script ) {
		unset( $skip_js[ $array_regenerator_runtime_script ] );
	}

	if ( in_array( $handle, $skip_js, true ) ) {
		return $tag;
	}

	// Change the script attributes and id before returning to remove it from main thread.
	$tag = sprintf(
		'<script type="text/rtscript" src="%s" id="%s"></script>',  // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
		esc_url( $src ),
		$handle . '-js'
	);

	return $tag;

}
add_filter( 'script_loader_tag', 'rt_scripts_handler', 10, 3 );


/**
 * Remove concating all js if site is using nginx-http plugin for files concatination or site is hosted on WordPress VIP.
 */
add_filter( 'js_do_concat', '__return_false' );

/**
 * Disable the emojis.
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'disable_emojis' );

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array  $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 *
 * @return array Difference betwen the two arrays.
 */
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {

		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

		$urls = array_diff( $urls, array( $emoji_svg_url ) );
	}
	return $urls;
}
