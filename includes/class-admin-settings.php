<?php
/**
 * Admin settings page for ACF Remote Server Links.
 *
 * When ACF Pro is active, the plugin configuration is presented as an ACF
 * Options Page (registered here) so editors get a native ACF UI with
 * conditional field logic (fields visible only when relevant).
 *
 * When ACF Free is active, the page still appears but only shows the Sync
 * Status card and the "Sync Now" button; settings are managed via wp-admin
 * Settings fields as a fallback.
 */

defined( 'ABSPATH' ) || exit;

class ARSL_Admin_Settings {

	public function __construct() {
		// Register the ACF Pro options page (must happen before acf/init).
		add_action( 'acf/init', [ $this, 'register_acf_options_page' ] );

		// Admin notices for sync feedback.
		add_action( 'admin_notices', [ $this, 'show_sync_notice' ] );

		// Register the "Sync Now" POST handler (also handled in ARSL_Remote_Sync,
		// but we add the nonce field and form here).
		// The actual admin_post_ action is in class-remote-sync.php.
	}

	// -----------------------------------------------------------------------
	// ACF Pro Options Page
	// -----------------------------------------------------------------------

	/**
	 * Registers the plugin settings as an ACF Options Page.
	 *
	 * This gives the plugin a native ACF UI with all Pro field types
	 * (conditional logic, select with UI, password field, etc.).
	 *
	 * The page is accessible at wp-admin > ACF Remote Manuals, and also
	 * linked under Settings for discoverability.
	 *
	 * Requires ACF Pro (acf_add_options_page() is Pro-only).
	 * When ACF Free is active, this method returns early and the settings page
	 * is added via add_menu_page() below instead.
	 */
	public function register_acf_options_page(): void {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			// ACF Free fallback — add a basic admin page.
			add_action( 'admin_menu', [ $this, 'add_fallback_settings_page' ] );
			return;
		}

		acf_add_options_page( [
			'page_title'  => 'ACF Remote Manuals — Settings',
			'menu_title'  => 'Remote Manuals',
			'menu_slug'   => 'arsl-settings',
			'capability'  => 'manage_options',
			'parent_slug' => 'options-general.php',   // Under Settings menu.
			'position'    => false,
			'icon_url'    => 'dashicons-media-document',
			'redirect'    => false,
			// Append the sync status card below the ACF field form.
			'update_button'     => __( 'Save Settings', 'arsl' ),
			'updated_message'   => __( 'Settings saved.', 'arsl' ),
		] );

		// After the ACF fields form on the options page, render our sync card.
		add_action( 'acf/options_page/submitbox_major_actions', [ $this, 'render_sync_button' ] );
		add_action( 'acf/after_field_group', [ $this, 'maybe_render_sync_card' ], 10, 1 );
	}

	/**
	 * Fallback: simple admin settings page for ACF Free installs.
	 * ACF field groups are not available here, so it only shows the sync card.
	 */
	public function add_fallback_settings_page(): void {
		add_options_page(
			__( 'ACF Remote Manuals', 'arsl' ),
			__( 'Remote Manuals', 'arsl' ),
			'manage_options',
			'arsl-settings',
			[ $this, 'render_fallback_settings_page' ]
		);
	}

	// -----------------------------------------------------------------------
	// Sync card rendered below the ACF field group on the options page
	// -----------------------------------------------------------------------

	/**
	 * Only render the sync card when we are on our own options page.
	 *
	 * @param array $field_group The field group being rendered.
	 */
	public function maybe_render_sync_card( array $field_group ): void {
		if ( $field_group['key'] !== 'group_arsl_options' ) {
			return;
		}
		$this->render_sync_card();
	}

	/**
	 * Outputs a "Sync Status" card with last sync time, result summary, and
	 * a "Sync Now" button.
	 */
	public function render_sync_card(): void {
		$last_sync   = get_option( 'arsl_last_sync_time' );
		$last_result = get_option( 'arsl_last_sync_result', [] );
		?>
		<div class="postbox" style="margin-top:24px;">
			<div class="postbox-header">
				<h2 class="hndle"><?php esc_html_e( 'Sync Status', 'arsl' ); ?></h2>
			</div>
			<div class="inside">
				<?php if ( $last_sync ) : ?>
					<p>
						<strong><?php esc_html_e( 'Last sync:', 'arsl' ); ?></strong>
						<?php echo esc_html( $last_sync ); ?>
					</p>
					<?php if ( ! empty( $last_result['error'] ) ) : ?>
						<p style="color:#d63638;">
							<strong><?php esc_html_e( 'Error:', 'arsl' ); ?></strong>
							<?php echo esc_html( $last_result['error'] ); ?>
						</p>
					<?php else : ?>
						<ul>
							<li><?php printf( esc_html__( 'Created: %d', 'arsl' ), (int) ( $last_result['created'] ?? 0 ) ); ?></li>
							<li><?php printf( esc_html__( 'Updated: %d', 'arsl' ), (int) ( $last_result['updated'] ?? 0 ) ); ?></li>
							<li><?php printf( esc_html__( 'Drafted (stale): %d', 'arsl' ), (int) ( $last_result['drafted'] ?? 0 ) ); ?></li>
							<li><?php printf( esc_html__( 'Deleted: %d', 'arsl' ), (int) ( $last_result['deleted'] ?? 0 ) ); ?></li>
							<?php if ( ! empty( $last_result['errors'] ) ) : ?>
								<li style="color:#d63638;">
									<?php printf( esc_html__( 'Errors: %d', 'arsl' ), count( $last_result['errors'] ) ); ?>
								</li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
				<?php else : ?>
					<p><?php esc_html_e( 'No sync has been run yet.', 'arsl' ); ?></p>
				<?php endif; ?>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="arsl_manual_sync">
					<?php wp_nonce_field( 'arsl_manual_sync' ); ?>
					<p>
						<?php submit_button( __( 'Sync Now', 'arsl' ), 'secondary', 'arsl_sync_now', false ); ?>
						<span class="description" style="margin-left:8px;">
							<?php
							$next = wp_next_scheduled( 'arsl_sync_manuals' );
							if ( $next ) {
								printf(
									esc_html__( 'Next automatic sync: %s', 'arsl' ),
									esc_html( human_time_diff( $next ) . ' ' . __( 'from now', 'arsl' ) )
								);
							} else {
								esc_html_e( 'Automatic sync not scheduled.', 'arsl' );
							}
							?>
						</span>
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders the "Sync Now" button inside the ACF submit box on the options page.
	 */
	public function render_sync_button(): void {
		?>
		<div class="major-publishing-actions" style="border-top:1px solid #ddd;padding-top:10px;margin-top:10px;">
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="arsl_manual_sync">
				<?php wp_nonce_field( 'arsl_manual_sync' ); ?>
				<div class="publishing-action">
					<?php submit_button( __( 'Sync Manuals Now', 'arsl' ), 'secondary', 'arsl_sync_now', false ); ?>
				</div>
			</form>
		</div>
		<?php
	}

	// -----------------------------------------------------------------------
	// Fallback settings page (ACF Free only)
	// -----------------------------------------------------------------------

	/**
	 * Renders a minimal settings page when ACF Pro is not active.
	 * Directs the admin to activate ACF Pro for full settings UI.
	 */
	public function render_fallback_settings_page(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'ACF Remote Server Links', 'arsl' ); ?></h1>

			<div class="notice notice-warning inline">
				<p>
					<?php esc_html_e( 'ACF Pro is required for the full settings UI (conditional fields, select dropdowns, etc.). Plugin settings are shown as ACF fields on this page when ACF Pro is active.', 'arsl' ); ?>
				</p>
			</div>

			<?php $this->render_sync_card(); ?>
		</div>
		<?php
	}

	// -----------------------------------------------------------------------
	// Admin notices
	// -----------------------------------------------------------------------

	/**
	 * Shows a success or error notice after a manual sync action.
	 */
	public function show_sync_notice(): void {
		$screen = get_current_screen();

		// Only show on our settings page.
		if ( ! $screen || strpos( $screen->id, 'arsl-settings' ) === false ) {
			return;
		}

		$synced = sanitize_text_field( wp_unslash( $_GET['synced'] ?? '' ) );

		if ( $synced === 'success' ) {
			$result = get_option( 'arsl_last_sync_result', [] );
			?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					printf(
						esc_html__( 'Sync complete: %1$d created, %2$d updated, %3$d drafted, %4$d deleted.', 'arsl' ),
						(int) ( $result['created'] ?? 0 ),
						(int) ( $result['updated'] ?? 0 ),
						(int) ( $result['drafted'] ?? 0 ),
						(int) ( $result['deleted'] ?? 0 )
					);
					?>
				</p>
			</div>
			<?php
		} elseif ( $synced === 'error' ) {
			$result = get_option( 'arsl_last_sync_result', [] );
			$msg    = $result['error'] ?? __( 'Unknown error.', 'arsl' );
			?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php
					printf(
						esc_html__( 'Sync failed: %s', 'arsl' ),
						esc_html( $msg )
					);
					?>
				</p>
			</div>
			<?php
		}
	}
}
