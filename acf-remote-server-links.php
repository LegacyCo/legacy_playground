<?php
/**
 * Plugin Name:       ACF Remote Server Links
 * Plugin URI:        https://github.com/LegacyCo/legacy_playground
 * Description:       Links WordPress posts/pages to manual documents stored on a remote server via ACF relationship fields. Supports REST API, HTTP directory listings, and FTP/SFTP sources.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            LegacyCo
 * License:           GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

define( 'ARSL_VERSION',    '1.0.0' );
define( 'ARSL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ARSL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ARSL_PLUGIN_FILE', __FILE__ );

// ---------------------------------------------------------------------------
// Deferred init — ACF must be loaded before we register field groups.
// ---------------------------------------------------------------------------
add_action( 'plugins_loaded', 'arsl_boot', 20 );

function arsl_boot(): void {
	if ( ! class_exists( 'ACF' ) ) {
		add_action( 'admin_notices', 'arsl_notice_acf_missing' );
		return;
	}

	require_once ARSL_PLUGIN_DIR . 'includes/class-cpt-manual.php';
	require_once ARSL_PLUGIN_DIR . 'includes/class-acf-fields.php';
	require_once ARSL_PLUGIN_DIR . 'includes/class-remote-sync.php';
	require_once ARSL_PLUGIN_DIR . 'includes/class-admin-settings.php';

	new ARSL_CPT_Manual();
	new ARSL_ACF_Fields();
	new ARSL_Remote_Sync();
	new ARSL_Admin_Settings();
}

// ---------------------------------------------------------------------------
// Activation — schedule cron and trigger an initial sync.
// ---------------------------------------------------------------------------
register_activation_hook( __FILE__, 'arsl_on_activate' );

function arsl_on_activate(): void {
	// Schedule the recurring sync event.
	if ( ! wp_next_scheduled( 'arsl_sync_manuals' ) ) {
		wp_schedule_event( time(), 'hourly', 'arsl_sync_manuals' );
	}

	// Queue a one-time first sync to run on the next admin page load.
	update_option( 'arsl_run_first_sync', 1 );
}

// ---------------------------------------------------------------------------
// Deactivation — remove cron event so it does not linger.
// ---------------------------------------------------------------------------
register_deactivation_hook( __FILE__, 'arsl_on_deactivate' );

function arsl_on_deactivate(): void {
	wp_clear_scheduled_hook( 'arsl_sync_manuals' );
}

// ---------------------------------------------------------------------------
// Admin notice shown when ACF is not active.
// ---------------------------------------------------------------------------
function arsl_notice_acf_missing(): void {
	?>
	<div class="notice notice-error">
		<p>
			<strong>ACF Remote Server Links</strong> requires
			<a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">
				Advanced Custom Fields
			</a>
			to be installed and active.
		</p>
	</div>
	<?php
}
