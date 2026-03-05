<?php
/**
 * Template snippet — Related Manuals
 *
 * Copy this snippet into your theme template (e.g. single.php, page.php) to
 * output the manuals linked to a post or page via the ACF Relationship field.
 *
 * All file metadata (URL, size, last modified) is retrieved from the
 * `manual` CPT entries that were synced from the remote server.
 *
 * ACF Pro note: if you added extra fields (e.g. via the Repeater sub-fields
 * for versioned files), access them via get_field('file_versions', $manual->ID).
 */

// Retrieve the array of related Manual WP_Post objects.
$related_manuals = get_field( 'related_manuals' );

if ( $related_manuals ) : ?>

	<section class="related-manuals" aria-label="<?php esc_attr_e( 'Related Manuals', 'arsl' ); ?>">
		<h2><?php esc_html_e( 'Related Manuals', 'arsl' ); ?></h2>

		<ul class="manuals-list">
			<?php foreach ( $related_manuals as $manual ) :
				$remote_url    = get_field( 'remote_file_url',  $manual->ID );
				$file_name     = get_field( 'remote_file_name', $manual->ID );
				$file_size     = get_field( 'file_size',        $manual->ID );
				$last_modified = get_field( 'last_modified',    $manual->ID );
				$display_name  = $file_name ?: $manual->post_title;

				// ACF Pro: retrieve versioned file history (Repeater field).
				$versions = get_field( 'file_versions', $manual->ID );
			?>
			<li class="manual-item">

				<?php if ( $remote_url ) : ?>
					<a href="<?php echo esc_url( $remote_url ); ?>"
					   class="manual-link"
					   target="_blank"
					   rel="noopener noreferrer">
						<?php echo esc_html( $display_name ); ?>
					</a>
				<?php else : ?>
					<span class="manual-name"><?php echo esc_html( $display_name ); ?></span>
				<?php endif; ?>

				<span class="manual-meta">
					<?php if ( $file_size ) : ?>
						<span class="manual-size"><?php echo esc_html( $file_size ); ?></span>
					<?php endif; ?>

					<?php if ( $last_modified ) : ?>
						<time class="manual-modified"
						      datetime="<?php echo esc_attr( $last_modified ); ?>">
							<?php
							echo esc_html(
								wp_date( get_option( 'date_format' ), strtotime( $last_modified ) )
							);
							?>
						</time>
					<?php endif; ?>
				</span>

				<?php
				// ACF Pro — output older versions if any exist.
				if ( $versions ) : ?>
					<details class="manual-versions">
						<summary><?php esc_html_e( 'Previous versions', 'arsl' ); ?></summary>
						<ul>
							<?php foreach ( $versions as $version ) :
								$v_url   = $version['version_file_url']  ?? '';
								$v_label = $version['version_label']      ?? '';
								$v_size  = $version['version_file_size']  ?? '';
								$v_date  = $version['version_modified']   ?? '';
							?>
							<li>
								<?php if ( $v_url ) : ?>
									<a href="<?php echo esc_url( $v_url ); ?>"
									   target="_blank"
									   rel="noopener noreferrer">
										<?php echo esc_html( $v_label ?: basename( $v_url ) ); ?>
									</a>
								<?php else : ?>
									<?php echo esc_html( $v_label ); ?>
								<?php endif; ?>

								<?php if ( $v_size ) : ?>
									<span class="version-size">(<?php echo esc_html( $v_size ); ?>)</span>
								<?php endif; ?>

								<?php if ( $v_date ) : ?>
									&ndash;
									<time datetime="<?php echo esc_attr( $v_date ); ?>">
										<?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $v_date ) ) ); ?>
									</time>
								<?php endif; ?>
							</li>
							<?php endforeach; ?>
						</ul>
					</details>
				<?php endif; ?>

			</li>
			<?php endforeach; ?>
		</ul>
	</section>

<?php endif;
