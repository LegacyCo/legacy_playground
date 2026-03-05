<?php
/**
 * Registers all ACF field groups via code (acf_add_local_field_group).
 *
 * Uses ACF Pro-specific field types where they add meaningful value:
 *
 *  - Repeater  : stores multiple versioned file entries per manual.
 *  - Options Page: used by ARSL_Admin_Settings for plugin configuration.
 *
 * All groups are code-registered so they are version-controlled and survive
 * database resets.  They appear in ACF > Field Groups with a "Local" badge.
 */

defined( 'ABSPATH' ) || exit;

class ARSL_ACF_Fields {

	public function __construct() {
		add_action( 'acf/init', [ $this, 'register_manual_meta_group' ] );
		add_action( 'acf/init', [ $this, 'register_post_page_relations_group' ] );
		add_action( 'acf/init', [ $this, 'register_options_page_group' ] );

		// Make sync-managed fields read-only in the admin UI.
		add_filter( 'acf/load_field/name=remote_file_url',  [ $this, 'make_readonly' ] );
		add_filter( 'acf/load_field/name=remote_file_name', [ $this, 'make_readonly' ] );
		add_filter( 'acf/load_field/name=file_size',        [ $this, 'make_readonly' ] );
		add_filter( 'acf/load_field/name=last_modified',    [ $this, 'make_readonly' ] );
		add_filter( 'acf/load_field/name=file_versions',    [ $this, 'make_readonly' ] );
	}

	// -----------------------------------------------------------------------
	// Field group 1: Metadata stored on each `manual` CPT post.
	// -----------------------------------------------------------------------
	public function register_manual_meta_group(): void {
		acf_add_local_field_group( [
			'key'                   => 'group_arsl_manual_meta',
			'title'                 => 'Manual — Remote File Details',
			'fields'                => [

				// Primary / canonical remote file URL.
				[
					'key'           => 'field_arsl_remote_file_url',
					'label'         => 'Remote File URL',
					'name'          => 'remote_file_url',
					'type'          => 'url',
					'instructions'  => 'Canonical URL of this manual on the remote server. Managed by the sync process.',
					'required'      => 0,
					'wrapper'       => [ 'width' => '100' ],
				],

				// Original filename on the remote server.
				[
					'key'           => 'field_arsl_remote_file_name',
					'label'         => 'File Name',
					'name'          => 'remote_file_name',
					'type'          => 'text',
					'instructions'  => 'Original filename as it appears on the remote server.',
					'required'      => 0,
					'wrapper'       => [ 'width' => '50' ],
				],

				// Human-readable file size.
				[
					'key'           => 'field_arsl_file_size',
					'label'         => 'File Size',
					'name'          => 'file_size',
					'type'          => 'text',
					'instructions'  => 'e.g. 2.4 MB — populated by sync.',
					'required'      => 0,
					'wrapper'       => [ 'width' => '25' ],
				],

				// Last-modified datetime from the remote server.
				[
					'key'               => 'field_arsl_last_modified',
					'label'             => 'Last Modified (Remote)',
					'name'              => 'last_modified',
					'type'              => 'date_time_picker',
					'instructions'      => 'Datetime stamp from the remote server, populated by sync.',
					'required'          => 0,
					'display_format'    => 'd/m/Y H:i',
					'return_format'     => 'Y-m-d H:i:s',
					'first_day'         => 1,
					'wrapper'           => [ 'width' => '25' ],
				],

				// ---------------------------------------------------------------
				// ACF Pro — Repeater: versioned file history.
				// Each entry stores a distinct version of this manual document.
				// ---------------------------------------------------------------
				[
					'key'               => 'field_arsl_file_versions',
					'label'             => 'File Versions',
					'name'              => 'file_versions',
					'type'              => 'repeater',
					'instructions'      => 'Versioned history of this manual. Older versions are added here automatically during sync when the primary URL changes.',
					'required'          => 0,
					'collapsed'         => 'field_arsl_fv_version_label',
					'min'               => 0,
					'max'               => 0,
					'layout'            => 'table',
					'button_label'      => 'Add Version',
					'sub_fields'        => [
						[
							'key'       => 'field_arsl_fv_version_label',
							'label'     => 'Version Label',
							'name'      => 'version_label',
							'type'      => 'text',
							'instructions' => 'e.g. v2.1 or 2024-Q3',
							'required'  => 0,
							'wrapper'   => [ 'width' => '20' ],
						],
						[
							'key'       => 'field_arsl_fv_file_url',
							'label'     => 'File URL',
							'name'      => 'version_file_url',
							'type'      => 'url',
							'required'  => 0,
							'wrapper'   => [ 'width' => '40' ],
						],
						[
							'key'       => 'field_arsl_fv_file_size',
							'label'     => 'File Size',
							'name'      => 'version_file_size',
							'type'      => 'text',
							'required'  => 0,
							'wrapper'   => [ 'width' => '15' ],
						],
						[
							'key'               => 'field_arsl_fv_modified',
							'label'             => 'Modified Date',
							'name'              => 'version_modified',
							'type'              => 'date_time_picker',
							'display_format'    => 'd/m/Y H:i',
							'return_format'     => 'Y-m-d H:i:s',
							'required'          => 0,
							'wrapper'           => [ 'width' => '25' ],
						],
					],
				],

			],
			'location' => [
				[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'manual' ] ],
			],
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		] );
	}

	// -----------------------------------------------------------------------
	// Field group 2: Relationship picker on Posts and Pages.
	// -----------------------------------------------------------------------
	public function register_post_page_relations_group(): void {
		acf_add_local_field_group( [
			'key'    => 'group_arsl_post_relations',
			'title'  => 'Related Manuals',
			'fields' => [
				[
					'key'           => 'field_arsl_related_manuals',
					'label'         => 'Related Manuals',
					'name'          => 'related_manuals',
					'type'          => 'relationship',
					'instructions'  => 'Select one or more manuals from the remote server to associate with this content.',
					'required'      => 0,

					// Only show `manual` CPT entries in the picker.
					'post_type'     => [ 'manual' ],
					'post_status'   => [ 'publish' ],   // Only show synced (published) entries.
					'taxonomy'      => [],
					'filters'       => [ 'search' ],    // Search box in the picker UI.
					'elements'      => [],
					'min'           => 0,
					'max'           => 0,               // 0 = unlimited.
					'return_format' => 'object',         // Returns WP_Post objects.
				],
			],
			// Show on Posts AND Pages (two location blocks = OR logic).
			'location' => [
				[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ] ],
				[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ] ],
			],
			'menu_order'            => 10,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		] );
	}

	// -----------------------------------------------------------------------
	// Field group 3 (ACF Pro): Options page fields for plugin configuration.
	//
	// These fields are registered on the ACF options page added by
	// ARSL_Admin_Settings::register_acf_options_page().  Using an ACF options
	// page gives editors a native ACF UI for plugin settings, avoids the
	// WordPress Settings API boilerplate, and keeps all configuration
	// accessible via get_field('option_name', 'option').
	// -----------------------------------------------------------------------
	public function register_options_page_group(): void {
		// Guard: acf_add_options_page() is ACF Pro only.
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		acf_add_local_field_group( [
			'key'    => 'group_arsl_options',
			'title'  => 'Remote Server Settings',
			'fields' => [

				// ---- Source configuration ----------------------------------

				[
					'key'           => 'field_arsl_opt_source_type',
					'label'         => 'Remote Source Type',
					'name'          => 'arsl_remote_source_type',
					'type'          => 'select',
					'instructions'  => 'How the plugin should connect to the remote server.',
					'required'      => 1,
					'choices'       => [
						'rest_api'       => 'REST API (JSON endpoint)',
						'http_directory' => 'HTTP Directory Listing (Apache/Nginx index)',
						'ftp'            => 'FTP / SFTP Server',
					],
					'default_value' => 'rest_api',
					'allow_null'    => 0,
					'multiple'      => 0,
					'ui'            => 1,
					'return_format' => 'value',
				],

				[
					'key'           => 'field_arsl_opt_endpoint_url',
					'label'         => 'Remote Endpoint URL',
					'name'          => 'arsl_remote_endpoint_url',
					'type'          => 'url',
					'instructions'  => 'Full URL of the remote server endpoint. For REST API: the JSON endpoint. For HTTP Directory: the browsable directory URL. For FTP: the host (e.g. ftp://files.example.com/manuals/).',
					'required'      => 1,
					'placeholder'   => 'https://files.example.com/manuals/',
					'conditional_logic' => [
						[
							[
								'field'    => 'field_arsl_opt_source_type',
								'operator' => '!=',
								'value'    => 'ftp',
							],
						],
					],
				],

				// ---- FTP credentials (shown only when type = ftp) ---------

				[
					'key'           => 'field_arsl_opt_ftp_host',
					'label'         => 'FTP Host',
					'name'          => 'arsl_ftp_host',
					'type'          => 'text',
					'instructions'  => 'Hostname or IP of the FTP server (without ftp:// prefix).',
					'required'      => 0,
					'placeholder'   => 'ftp.example.com',
					'wrapper'       => [ 'width' => '50' ],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_arsl_opt_source_type',
								'operator' => '==',
								'value'    => 'ftp',
							],
						],
					],
				],

				[
					'key'           => 'field_arsl_opt_ftp_path',
					'label'         => 'FTP Remote Path',
					'name'          => 'arsl_ftp_path',
					'type'          => 'text',
					'instructions'  => 'Directory path on the FTP server containing the manuals.',
					'required'      => 0,
					'placeholder'   => '/manuals/',
					'wrapper'       => [ 'width' => '50' ],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_arsl_opt_source_type',
								'operator' => '==',
								'value'    => 'ftp',
							],
						],
					],
				],

				[
					'key'           => 'field_arsl_opt_ftp_username',
					'label'         => 'FTP Username',
					'name'          => 'arsl_ftp_username',
					'type'          => 'text',
					'required'      => 0,
					'wrapper'       => [ 'width' => '50' ],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_arsl_opt_source_type',
								'operator' => '==',
								'value'    => 'ftp',
							],
						],
					],
				],

				[
					'key'           => 'field_arsl_opt_ftp_password',
					'label'         => 'FTP Password',
					'name'          => 'arsl_ftp_password',
					'type'          => 'password',
					'instructions'  => 'Stored encrypted in the database.',
					'required'      => 0,
					'wrapper'       => [ 'width' => '50' ],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_arsl_opt_source_type',
								'operator' => '==',
								'value'    => 'ftp',
							],
						],
					],
				],

				// ---- Sync settings ----------------------------------------

				[
					'key'           => 'field_arsl_opt_sync_interval',
					'label'         => 'Sync Interval',
					'name'          => 'arsl_sync_interval',
					'type'          => 'select',
					'instructions'  => 'How often WP-Cron should automatically sync the manual list.',
					'required'      => 1,
					'choices'       => [
						'hourly'     => 'Hourly',
						'twicedaily' => 'Twice Daily',
						'daily'      => 'Daily',
					],
					'default_value' => 'hourly',
					'allow_null'    => 0,
					'ui'            => 1,
					'return_format' => 'value',
					'wrapper'       => [ 'width' => '33' ],
				],

				[
					'key'           => 'field_arsl_opt_file_extensions',
					'label'         => 'Allowed File Extensions',
					'name'          => 'arsl_file_extensions',
					'type'          => 'text',
					'instructions'  => 'Comma-separated list of extensions to sync (leave blank for all files). Example: pdf,docx,xlsx',
					'required'      => 0,
					'placeholder'   => 'pdf,docx,xlsx',
					'wrapper'       => [ 'width' => '33' ],
				],

				[
					'key'           => 'field_arsl_opt_stale_action',
					'label'         => 'When a File Is Removed Remotely',
					'name'          => 'arsl_stale_action',
					'type'          => 'select',
					'instructions'  => '"Draft" is safer: it preserves existing relationships on posts. "Delete" permanently removes the Manual entry.',
					'required'      => 1,
					'choices'       => [
						'draft'  => 'Set to Draft (recommended)',
						'delete' => 'Delete Permanently',
					],
					'default_value' => 'draft',
					'allow_null'    => 0,
					'ui'            => 1,
					'return_format' => 'value',
					'wrapper'       => [ 'width' => '34' ],
				],

			],
			// Attach this group to the ACF options page added by ARSL_Admin_Settings.
			'location' => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'arsl-settings',
					],
				],
			],
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		] );
	}

	// -----------------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------------

	/**
	 * Disables editing of sync-managed fields in the admin.
	 *
	 * @param array $field ACF field array.
	 * @return array
	 */
	public function make_readonly( array $field ): array {
		$field['disabled'] = 1;
		return $field;
	}
}
