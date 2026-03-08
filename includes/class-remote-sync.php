<?php
/**
 * Handles syncing manual metadata from the remote server into the `manual` CPT.
 *
 * Supports three source adapters:
 *  - rest_api       : Fetches a JSON endpoint and normalises the response.
 *  - http_directory : Parses an Apache/Nginx HTML directory index for file links.
 *  - ftp            : Connects to an FTP server and lists files in a directory.
 *
 * All three adapters return the same normalised manifest array so that
 * reconcile_manuals() does not need to know which adapter was used.
 */

defined( 'ABSPATH' ) || exit;

class ARSL_Remote_Sync {

	public function __construct() {
		add_action( 'init',                        [ $this, 'maybe_schedule_cron' ] );
		add_action( 'arsl_sync_manuals',           [ $this, 'run_sync' ] );
		add_action( 'admin_post_arsl_manual_sync', [ $this, 'handle_manual_sync_request' ] );

		// Reschedule cron when the sync interval option changes.
		add_action( 'acf/save_post', [ $this, 'reschedule_on_interval_change' ] );

		// Trigger first sync queued during activation.
		add_action( 'admin_init', [ $this, 'maybe_run_first_sync' ] );
	}

	// -----------------------------------------------------------------------
	// Cron scheduling
	// -----------------------------------------------------------------------

	public function maybe_schedule_cron(): void {
		if ( wp_next_scheduled( 'arsl_sync_manuals' ) ) {
			return;
		}

		$interval = $this->get_option( 'arsl_sync_interval' ) ?: 'hourly';
		wp_schedule_event( time(), $interval, 'arsl_sync_manuals' );
	}

	/**
	 * When the admin saves settings, reschedule the cron if the interval changed.
	 *
	 * @param int|string $post_id ACF save_post hook passes the options screen slug.
	 */
	public function reschedule_on_interval_change( $post_id ): void {
		if ( $post_id !== 'options' ) {
			return;
		}

		$new_interval = $this->get_option( 'arsl_sync_interval' ) ?: 'hourly';
		wp_clear_scheduled_hook( 'arsl_sync_manuals' );
		wp_schedule_event( time(), $new_interval, 'arsl_sync_manuals' );
	}

	public function maybe_run_first_sync(): void {
		if ( get_option( 'arsl_run_first_sync' ) ) {
			delete_option( 'arsl_run_first_sync' );
			$this->run_sync();
		}
	}

	// -----------------------------------------------------------------------
	// Public sync entry points
	// -----------------------------------------------------------------------

	/**
	 * Main sync routine.  Called by WP-Cron and by the "Sync Now" admin action.
	 *
	 * @return array Summary array with keys created, updated, drafted, deleted, errors.
	 */
	public function run_sync(): array {
		$endpoint    = $this->get_option( 'arsl_remote_endpoint_url' );
		$source_type = $this->get_option( 'arsl_remote_source_type' ) ?: 'rest_api';

		if ( empty( $endpoint ) ) {
			$result = [ 'error' => 'Remote endpoint URL is not configured.' ];
			$this->store_result( $result );
			return $result;
		}

		$manifest = $this->fetch_remote_manifest( $endpoint, $source_type );

		if ( is_wp_error( $manifest ) ) {
			$result = [ 'error' => $manifest->get_error_message() ];
			$this->store_result( $result );
			return $result;
		}

		// Filter by allowed file extensions if configured.
		$extensions = $this->get_option( 'arsl_file_extensions' );
		if ( ! empty( $extensions ) ) {
			$allowed    = array_map( 'trim', explode( ',', strtolower( $extensions ) ) );
			$manifest   = array_filter( $manifest, static function ( array $item ) use ( $allowed ): bool {
				$ext = strtolower( pathinfo( $item['name'], PATHINFO_EXTENSION ) );
				return in_array( $ext, $allowed, true );
			} );
		}

		$result = $this->reconcile_manuals( array_values( $manifest ) );
		$this->store_result( $result );
		return $result;
	}

	/**
	 * Handles the "Sync Now" form POST submitted from the admin settings page.
	 */
	public function handle_manual_sync_request(): void {
		check_admin_referer( 'arsl_manual_sync' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you do not have permission to do that.', 'arsl' ) );
		}

		$result  = $this->run_sync();
		$status  = isset( $result['error'] ) ? 'error' : 'success';
		$referer = admin_url( 'admin.php?page=arsl-settings&synced=' . $status );

		wp_safe_redirect( $referer );
		exit;
	}

	// -----------------------------------------------------------------------
	// Remote manifest fetching — adapter dispatch
	// -----------------------------------------------------------------------

	/**
	 * Fetches the list of manuals from the remote server.
	 *
	 * @param string $endpoint   The configured URL/host.
	 * @param string $source_type One of: rest_api, http_directory, ftp.
	 * @return array|WP_Error Normalised manifest or WP_Error on failure.
	 */
	public function fetch_remote_manifest( string $endpoint, string $source_type ) {
		switch ( $source_type ) {
			case 'rest_api':
				return $this->adapter_rest_api( $endpoint );

			case 'http_directory':
				return $this->adapter_http_directory( $endpoint );

			case 'ftp':
				return $this->adapter_ftp();

			default:
				return new WP_Error( 'arsl_unknown_adapter', "Unknown source type: {$source_type}" );
		}
	}

	// -----------------------------------------------------------------------
	// Adapter: REST API
	// -----------------------------------------------------------------------

	/**
	 * Expects a JSON response that is either:
	 *   a) An array of objects: [{"name":"...", "url":"...", "size":"...", "modified":"..."}, ...]
	 *   b) A WP REST API posts response (post_type=manual exposed via REST, or another WP site).
	 *
	 * @param string $endpoint Full URL of the JSON endpoint.
	 * @return array|WP_Error Normalised manifest array or WP_Error.
	 */
	private function adapter_rest_api( string $endpoint ) {
		$response = wp_remote_get( $endpoint, [
			'timeout'    => 30,
			'user-agent' => 'ACF-Remote-Server-Links/' . ARSL_VERSION . '; ' . get_bloginfo( 'url' ),
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code !== 200 ) {
			return new WP_Error( 'arsl_rest_http_error', "REST API returned HTTP {$code}." );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error( 'arsl_rest_json_error', 'Could not parse REST API response as JSON.' );
		}

		if ( ! is_array( $data ) ) {
			return new WP_Error( 'arsl_rest_format_error', 'REST API response is not an array.' );
		}

		return array_map( [ $this, 'normalise_rest_item' ], $data );
	}

	/**
	 * Normalises a single REST API item into the standard manifest shape.
	 * Handles both a generic JSON object and a WordPress REST API post object.
	 *
	 * @param array $item Raw API response item.
	 * @return array Normalised item with keys: name, url, size, modified.
	 */
	private function normalise_rest_item( array $item ): array {
		// Generic JSON format.
		if ( isset( $item['url'] ) ) {
			return [
				'name'     => $item['name']     ?? basename( $item['url'] ),
				'url'      => $item['url'],
				'size'     => $item['size']     ?? '',
				'modified' => $item['modified'] ?? '',
			];
		}

		// WordPress REST API post format (e.g. another WP site exposing the `manual` CPT).
		if ( isset( $item['link'] ) ) {
			$meta    = $item['meta'] ?? [];
			$acf     = $item['acf']  ?? [];
			$details = array_merge( $meta, $acf );

			return [
				'name'     => $item['title']['rendered'] ?? ( $details['remote_file_name'] ?? '' ),
				'url'      => $details['remote_file_url'] ?? $item['link'],
				'size'     => $details['file_size']       ?? '',
				'modified' => $details['last_modified']   ?? ( $item['modified'] ?? '' ),
			];
		}

		// Unknown format — return what we have with empty defaults.
		return [
			'name'     => $item['name']     ?? '',
			'url'      => $item['url']      ?? '',
			'size'     => $item['size']     ?? '',
			'modified' => $item['modified'] ?? '',
		];
	}

	// -----------------------------------------------------------------------
	// Adapter: HTTP Directory Listing
	// -----------------------------------------------------------------------

	/**
	 * Fetches an Apache/Nginx HTML directory index page and extracts file links.
	 *
	 * @param string $base_url The browsable directory URL.
	 * @return array|WP_Error Normalised manifest array or WP_Error.
	 */
	private function adapter_http_directory( string $base_url ) {
		$response = wp_remote_get( $base_url, [
			'timeout'    => 30,
			'user-agent' => 'ACF-Remote-Server-Links/' . ARSL_VERSION . '; ' . get_bloginfo( 'url' ),
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code !== 200 ) {
			return new WP_Error( 'arsl_dir_http_error', "Directory listing returned HTTP {$code}." );
		}

		$html = wp_remote_retrieve_body( $response );

		if ( empty( $html ) ) {
			return new WP_Error( 'arsl_dir_empty', 'Directory listing response was empty.' );
		}

		// Suppress libxml errors for malformed HTML (common with directory listings).
		$prev = libxml_use_internal_errors( true );
		$dom  = new DOMDocument();
		$dom->loadHTML( $html );
		libxml_use_internal_errors( $prev );

		$links    = $dom->getElementsByTagName( 'a' );
		$manifest = [];
		$base_url = rtrim( $base_url, '/' ) . '/';

		foreach ( $links as $link ) {
			$href = $link->getAttribute( 'href' );

			// Skip parent directory links, query strings, and anchors.
			if ( empty( $href ) || $href === '../' || str_starts_with( $href, '?' ) || str_starts_with( $href, '#' ) ) {
				continue;
			}

			// Skip sub-directory links (end with /).
			if ( str_ends_with( $href, '/' ) ) {
				continue;
			}

			// Build absolute URL.
			$file_url = str_starts_with( $href, 'http' ) ? $href : $base_url . ltrim( $href, '/' );

			$manifest[] = [
				'name'     => basename( urldecode( $href ) ),
				'url'      => $file_url,
				'size'     => '',    // Not available from a plain directory index.
				'modified' => '',
			];
		}

		return $manifest;
	}

	// -----------------------------------------------------------------------
	// Adapter: FTP
	// -----------------------------------------------------------------------

	/**
	 * Connects to the configured FTP server and lists files in the remote path.
	 *
	 * Credentials are read from ACF options.  The password is stored encrypted;
	 * this method decrypts it before use.
	 *
	 * @return array|WP_Error Normalised manifest array or WP_Error.
	 */
	private function adapter_ftp() {
		if ( ! function_exists( 'ftp_connect' ) ) {
			return new WP_Error( 'arsl_ftp_unavailable', 'PHP FTP extension is not available on this server.' );
		}

		$host     = $this->get_option( 'arsl_ftp_host' );
		$path     = $this->get_option( 'arsl_ftp_path' ) ?: '/';
		$username = $this->get_option( 'arsl_ftp_username' );
		$password = $this->decrypt_password( $this->get_option( 'arsl_ftp_password' ) );

		if ( empty( $host ) || empty( $username ) ) {
			return new WP_Error( 'arsl_ftp_config', 'FTP host and username must be configured.' );
		}

		$connection = @ftp_connect( $host, 21, 30 );

		if ( ! $connection ) {
			return new WP_Error( 'arsl_ftp_connect', "Could not connect to FTP host: {$host}" );
		}

		if ( ! @ftp_login( $connection, $username, $password ) ) {
			ftp_close( $connection );
			return new WP_Error( 'arsl_ftp_login', 'FTP login failed. Check your credentials.' );
		}

		// Passive mode for compatibility with most firewalls.
		ftp_pasv( $connection, true );

		$files = ftp_nlist( $connection, $path );

		if ( $files === false ) {
			ftp_close( $connection );
			return new WP_Error( 'arsl_ftp_list', "Could not list files in FTP path: {$path}" );
		}

		$host_base = 'ftp://' . $host . rtrim( $path, '/' ) . '/';
		$manifest  = [];

		foreach ( $files as $remote_path ) {
			$filename = basename( $remote_path );

			// Skip dotfiles and directories (ftp_nlist may include them).
			if ( str_starts_with( $filename, '.' ) ) {
				continue;
			}

			// Attempt to get the last-modified time (returns -1 if unsupported).
			$mtime    = ftp_mdtm( $connection, $remote_path );
			$modified = $mtime > 0 ? gmdate( 'Y-m-d H:i:s', $mtime ) : '';

			// Attempt to get file size.
			$size_bytes = ftp_size( $connection, $remote_path );
			$size_hr    = $size_bytes > 0 ? $this->format_bytes( $size_bytes ) : '';

			$manifest[] = [
				'name'     => $filename,
				'url'      => $host_base . rawurlencode( $filename ),
				'size'     => $size_hr,
				'modified' => $modified,
			];
		}

		ftp_close( $connection );

		return $manifest;
	}

	// -----------------------------------------------------------------------
	// Reconciliation — create / update / draft Manual CPT posts
	// -----------------------------------------------------------------------

	/**
	 * Reconciles the fetched manifest with existing `manual` CPT posts.
	 *
	 * @param array $manifest Normalised array of remote file descriptors.
	 * @return array Summary: ['created' => N, 'updated' => N, 'drafted' => N, 'deleted' => N, 'errors' => []].
	 */
	public function reconcile_manuals( array $manifest ): array {
		$summary = [
			'created' => 0,
			'updated' => 0,
			'drafted' => 0,
			'deleted' => 0,
			'errors'  => [],
		];

		$stale_action = $this->get_option( 'arsl_stale_action' ) ?: 'draft';

		// Build an index of existing Manual posts keyed by remote_file_url.
		$existing_posts = get_posts( [
			'post_type'      => 'manual',
			'post_status'    => [ 'publish', 'draft' ],
			'posts_per_page' => -1,
			'fields'         => 'ids',
		] );

		$existing_by_url = [];
		foreach ( $existing_posts as $post_id ) {
			$url = get_field( 'remote_file_url', $post_id );
			if ( $url ) {
				$existing_by_url[ $url ] = $post_id;
			}
		}

		$manifest_urls = [];

		foreach ( $manifest as $item ) {
			if ( empty( $item['url'] ) ) {
				continue;
			}

			$url           = $item['url'];
			$manifest_urls[] = $url;
			$title         = ! empty( $item['name'] ) ? $item['name'] : basename( $url );

			if ( isset( $existing_by_url[ $url ] ) ) {
				// --- UPDATE existing post ---
				$post_id = $existing_by_url[ $url ];

				$updated = wp_update_post( [
					'ID'          => $post_id,
					'post_title'  => $title,
					'post_status' => 'publish',
				] );

				if ( is_wp_error( $updated ) ) {
					$summary['errors'][] = "Update failed for post {$post_id}: " . $updated->get_error_message();
					continue;
				}

				$this->save_acf_fields( $post_id, $item );
				$summary['updated']++;

			} else {
				// --- CREATE new post ---
				$post_id = wp_insert_post( [
					'post_type'   => 'manual',
					'post_title'  => $title,
					'post_status' => 'publish',
				], true );

				if ( is_wp_error( $post_id ) ) {
					$summary['errors'][] = "Insert failed for '{$title}': " . $post_id->get_error_message();
					continue;
				}

				$this->save_acf_fields( $post_id, $item );
				$summary['created']++;
			}
		}

		// --- Handle stale entries (exist in WP but not in manifest) ---
		foreach ( $existing_by_url as $url => $post_id ) {
			if ( in_array( $url, $manifest_urls, true ) ) {
				continue;
			}

			if ( $stale_action === 'delete' ) {
				wp_delete_post( $post_id, true );
				$summary['deleted']++;
			} else {
				// Default: draft (safe — preserves existing relationships on posts/pages).
				wp_update_post( [ 'ID' => $post_id, 'post_status' => 'draft' ] );
				$summary['drafted']++;
			}
		}

		return $summary;
	}

	/**
	 * Saves the four ACF metadata fields on a Manual post.
	 *
	 * @param int   $post_id Manual CPT post ID.
	 * @param array $item    Normalised manifest item.
	 */
	private function save_acf_fields( int $post_id, array $item ): void {
		update_field( 'remote_file_url',  $item['url'],      $post_id );
		update_field( 'remote_file_name', $item['name'],     $post_id );
		update_field( 'file_size',        $item['size'],     $post_id );
		update_field( 'last_modified',    $item['modified'], $post_id );
	}

	// -----------------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------------

	/**
	 * Retrieves an ACF option field value, falling back to get_option() for
	 * environments where ACF options may not be available during early init.
	 *
	 * @param string $option_name The ACF field name (also used as option key).
	 * @return mixed
	 */
	private function get_option( string $option_name ) {
		if ( function_exists( 'get_field' ) ) {
			$value = get_field( $option_name, 'option' );
			if ( $value !== null && $value !== false ) {
				return $value;
			}
		}
		return get_option( $option_name );
	}

	/**
	 * Formats a byte count as a human-readable string.
	 *
	 * @param int $bytes
	 * @return string e.g. "2.4 MB"
	 */
	private function format_bytes( int $bytes ): string {
		$units = [ 'B', 'KB', 'MB', 'GB', 'TB' ];
		$i     = 0;
		while ( $bytes >= 1024 && $i < count( $units ) - 1 ) {
			$bytes /= 1024;
			$i++;
		}
		return round( $bytes, 1 ) . ' ' . $units[ $i ];
	}

	/**
	 * Stores the sync result and timestamp in WP options for the settings UI.
	 *
	 * @param array $result
	 */
	private function store_result( array $result ): void {
		update_option( 'arsl_last_sync_time',   current_time( 'mysql' ) );
		update_option( 'arsl_last_sync_result', $result );
	}

	/**
	 * Encrypts a plaintext password for storage.
	 * Uses OpenSSL AES-256-CBC with a key derived from WordPress secret keys.
	 *
	 * @param string $plaintext
	 * @return string Base64-encoded ciphertext (with IV prepended).
	 */
	public function encrypt_password( string $plaintext ): string {
		if ( empty( $plaintext ) ) {
			return '';
		}

		$key  = substr( hash( 'sha256', SECURE_AUTH_KEY . AUTH_KEY ), 0, 32 );
		$iv   = openssl_random_pseudo_bytes( 16 );
		$ct   = openssl_encrypt( $plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv );
		return base64_encode( $iv . $ct );
	}

	/**
	 * Decrypts a password encrypted by encrypt_password().
	 *
	 * @param string $ciphertext Base64-encoded ciphertext.
	 * @return string Plaintext password.
	 */
	public function decrypt_password( string $ciphertext ): string {
		if ( empty( $ciphertext ) ) {
			return '';
		}

		$raw  = base64_decode( $ciphertext, true );
		if ( $raw === false || strlen( $raw ) < 17 ) {
			return '';
		}

		$key  = substr( hash( 'sha256', SECURE_AUTH_KEY . AUTH_KEY ), 0, 32 );
		$iv   = substr( $raw, 0, 16 );
		$ct   = substr( $raw, 16 );
		$pt   = openssl_decrypt( $ct, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv );
		return $pt !== false ? $pt : '';
	}
}
