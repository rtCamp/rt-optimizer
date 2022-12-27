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

define( 'RT_SCRIPTS_OPTIMIZER_DIR_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'RT_SCRIPTS_OPTIMIZER_DIR_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

// Include settings options page.
require_once RT_SCRIPTS_OPTIMIZER_DIR_PATH . '/includes/settings-page.php';

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

	$load_amp_css = get_option( 'rt_scripts_optimizer_load_amp_boilerplate_style' );

	if ( '1' === $load_amp_css ) {
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
 * Loads the specified stylesheets asynchronously.
 *
 * @param string $html The link tag generated by WordPress.
 * @param string $handle The style enqueue handle.
 */
function load_async_styles( $html, $handle ) {

	if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
		return $html;
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
		return $html;
	}

	$async_loading = explode( ',', get_option( 'rt_scripts_optimizer_style_async_handles' ) );
	if ( ! is_admin() && in_array( $handle, $async_loading, true ) ) {
		$async_html  = str_replace( 'rel=\'stylesheet\'', 'rel=\'preload\' as=\'style\'', $html );
		$async_html  = str_replace( 'media=\'all\'', 'media=\'all\' onload="this.onload=null;this.rel=\'stylesheet\'"', $async_html );
		$async_html .= sprintf( '<noscript>%s</noscript>', $html );
		return $async_html;
	}

	$async_js_loading = array(); // The above array can be used here also but that will cause FOUT as this script is included in the footer from where the CSS is loaded.
	if ( ! is_admin() && in_array( $handle, $async_js_loading, true ) ) {
		$async_html  = str_replace( 'rel=\'stylesheet\'', 'rel=\'rt-optimized-stylesheet\'', $html );
		$async_html .= sprintf( '<noscript>%s</noscript>', $html );
		return $async_html;
	}

	$optimized_loading = explode( ',', get_option( 'rt_scripts_optimizer_style_async_handles_onevent' ) );
	if ( ! is_admin() && in_array( $handle, $optimized_loading, true ) ) {
		$async_html  = str_replace( 'rel=\'stylesheet\'', 'rel=\'rt-optimized-onevent-stylesheet\'', $html );
		$async_html .= sprintf( '<noscript>%s</noscript>', $html );
		return $async_html;
	}
	return $html;
}
add_filter( 'style_loader_tag', 'load_async_styles', 10, 2 );

/**
 * Add script to include stylesheets on demand.
 */
function style_enqueue_script() {

	if ( is_admin() ) {
		return null;
	}
	?>
		<script type="text/javascript">
			const s_i_e=["mouseover","keydown","touchmove","touchstart","scroll"];function s_i_e_e(){s_i(),s_i_e.forEach(function(e){window.removeEventListener(e,s_i_e_e,{passive:!0})})}function s_i_rti(e){loadCSS(e.href),e.href||s_i_e_e()}function s_i(){var e=document.querySelectorAll("link");[].forEach.call(e,function(e){"rt-optimized-onevent-stylesheet"==e.getAttribute("rel")&&s_i_rti(e)})}s_i_e.forEach(function(e){window.addEventListener(e,s_i_e_e,{passive:!0})}),function(){var e=document.querySelectorAll("link");[].forEach.call(e,function(e){"rt-optimized-stylesheet"==e.getAttribute("rel")&&loadCSS(e.href)})}();
		</script>

		<script type="text/javascript">
			const iframes = document.getElementsByTagName('iframe');
			const iframesObserver = new IntersectionObserver((entries, self) => {
				entries.forEach((entry) => {
					if(entry.isIntersecting) {
						entry.target.src = entry.target.getAttribute('data-src');
						self.unobserve(entry.target);
					}
				})
			},{
				threshold: 0,
				rootMargin: '200px'
			});
			Array.from(iframes).forEach(function (el) {
				iframesObserver.observe(el);
			});
		</script>
	<?php
}
add_action( 'wp_footer', 'style_enqueue_script' );

/**
 * Dequeues unnecessary styles.
 */
function dequeue_styles() {

	if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
		return;
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
		return;
	}

	// If user not logged in, these styles will be dequeued.
	$non_logged_in = explode( ',', get_option( 'rt_scripts_optimizer_style_dequeue_non_logged_handles' ) );

	if ( ! is_user_logged_in() ) {
		foreach ( $non_logged_in as $dequeue_handle ) {
			wp_dequeue_style( $dequeue_handle );
			wp_deregister_style( $dequeue_handle );
		}
	}
}
add_action( 'wp_print_styles', 'dequeue_styles' );

/**
 * Remove concating all js if site is using nginx-http plugin for files concatination or site is hosted on WordPress VIP.
 */
add_filter( 'js_do_concat', '__return_false' );

/**
 * Skips CSS concatination for handles specified in the option `rt_scripts_optimizer_skip_css_concatination_handles`.
 *
 * @param bool   $do_concat The default boolean value for concatination of the current handle.
 * @param string $handle The current handle.
 */
function skip_css_concatination( $do_concat, $handle ) {

	$skip_concatination_handles = explode( ',', get_option( 'rt_scripts_optimizer_skip_css_concatination_handles' ) );

	foreach ( $skip_concatination_handles as $key => $skip_concatination_handle ) {
		if ( $skip_concatination_handle === $handle ) {
			return false;
		}
	}

	return $do_concat;
}

/**
 * Disable concatination according to the supplied setting.
 */
if ( '1' === get_option( 'rt_scripts_optimizer_skip_css_concatination_all' ) ) {

	add_filter( 'css_do_concat', '__return_false' );

} else {

	add_filter( 'css_do_concat', 'skip_css_concatination', 10, 2 );

}

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

/**
 * Enqueues loadCSS scripts.
 */
function rt_scripts_optimizer_load_scripts() {

	wp_enqueue_script( 'loadCSS', RT_SCRIPTS_OPTIMIZER_DIR_URL . '/assets/js/loadCSS.min.js', array(), filemtime( RT_SCRIPTS_OPTIMIZER_DIR_PATH . '/assets/js/loadCSS.min.js' ), false );

}
add_action( 'wp_enqueue_scripts', 'rt_scripts_optimizer_load_scripts' );

/**
 * Rename src attribute of iframes to block them from automatically loading on page load.
 *
 * This will be loaded by javascript.
 *
 * @param string $content Original content.
 *
 * @return string Modified content.
 */
function rt_scripts_optimizer_iframe_lazy_loading( $content ) {

	$content = preg_replace( '~<iframe[^>]*\K (?=src=)~i', ' data-', $content );

	return $content;

}

add_action( 'the_content', 'rt_scripts_optimizer_iframe_lazy_loading' );
