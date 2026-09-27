<?php
/**
 * Plugin Name: Cloud Environment Footer
 * Shows the Cloud Run revision, instance ID and build version in the page footer
 */
function cloud_env_instance_id() {
	$cache = '/tmp/cloud-run-instance-id';
	if ( is_readable( $cache ) ) {
		return trim( file_get_contents( $cache ) );
	}
	$response = wp_remote_get(
		'http://metadata.google.internal/computeMetadata/v1/instance/id',
		array(
			'headers' => array( 'Metadata-Flavor' => 'Google' ),
			'timeout' => 1,
		)
	);
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return 'unknown';
	}
	$id = trim( wp_remote_retrieve_body( $response ) );
	file_put_contents( $cache, $id );
	return $id;
}
add_action( 'wp_footer', function () {
	$version_file = ABSPATH . 'VERSION';
	$version      = is_readable( $version_file ) ? trim( file_get_contents( $version_file ) ) : 'unknown';
	$revision     = getenv( 'K_REVISION' ) ?: 'local';
	$instance     = cloud_env_instance_id();
	// Full instance IDs are very long; the last 12 characters are enough to tell
	// instances apart and to match the instanceId label in Cloud Logging.
	$instance_short = strlen( $instance ) > 12 ? '…' . substr( $instance, -12 ) : $instance;
	printf(
		'<div style="position:fixed;bottom:0;left:0;right:0;z-index:99999;padding:6px 12px;background:#1d2327;color:#f0f0f1;font:12px/1.4 monospace;text-align:center;">'
		. 'Version <strong>%s</strong> &middot; Revision <strong>%s</strong> &middot; Instance <strong title="%s">%s</strong>'
		. '</div>',
		esc_html( $version ),
		esc_html( $revision ),
		esc_attr( $instance ),
		esc_html( $instance_short )
	);
} );
