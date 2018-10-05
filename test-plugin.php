<?php
/**
 * The plugin bootstrap file
 *
 * @wordpress-plugin
 * Plugin Name:       AATest Plugin
 * Version:           1.0.0
 */

// namespace Test\Wp;
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */

$plugin_path = plugin_dir_path( __FILE__ );

require __DIR__ . '/vendor/autoload.php';

add_action(
	'admin_init', function() {
		wp_register_script( 'test-admin-script', plugin_dir_url( __DIR__ ) . 'test-plugin/dist/test-admin.js' );
	}
);

add_action(
	'admin_menu', function() {
		$admin_page = new Ngearing\Wp\AdminPage(
			'TEST', [
				'template_path' => __DIR__ . '/templates',
				'template_name' => 'admin-page',
			]
		);
		$admin_page->register();

		echo '<pre>' . print_r( $admin_page->get_template_path(), true ) . '</pre>';
	}
);

add_action(
	'admin_init', function() {

		if ( isset( $_POST['test-submit'] ) ) {
			global $wpdb;

			$images = $wpdb->get_results(
				"
				SELECT group_concat(cast(post_id as char)) as ids, meta_value as hash
				FROM artwp_postmeta
				WHERE meta_key = 'mdd_hash'
				AND meta_value != 'not-found'
				GROUP BY meta_value
				HAVING count(*) > 1
				LIMIT 20
				"
			);

			foreach ( $images as $image ) :
				$ids = explode( ',', $image->ids );

				foreach ( $ids as $id ) :

					$post = get_post( $id );

					$in_meta = $wpdb->get_results(
						$wpdb->prepare(
							"
						SELECT *
						FROM artwp_postmeta
						WHERE meta_value = %d
						OR (meta_value LIKE '%%%s%' AND post_id != %1\$d)
						",
							$post->ID,
							basename( $post->guid )
						)
					);

					$in_content = $wpdb->get_results(
						$wpdb->prepare(
							"
							SELECT *
							FROM artwp_posts
							WHERE post_content LIKE '%%%s%'
							",
							basename( $post->guid )
						)
					);

					if ( ! $in_meta && ! $in_content ) {

						echo '<pre>' . print_r( [ $post->ID, $post->post_name ], true ) . '</pre>';
					}

				endforeach;

			endforeach;
		}
	}
);

function list_dupe_images() {
	global $wpdb;

	$images = $wpdb->get_results(
		"
		SELECT group_concat(cast(post_id as char)) as ids
		FROM $wpdb->postmeta
		WHERE meta_key = 'mdd_hash'
		AND meta_value != 'not-found'
		GROUP BY meta_value
		HAVING count(*) > 1
		LIMIT 20
		"
	);

	if ( $images ) {
		foreach ( $images as $image_group ) :

			foreach ( explode( ',', $image_group->ids ) as $post ) :
				$post = get_post( $post );
				printf(
					'<div>%s (%s)</div>',
					$post->post_title,
					$post->post_name
				);
			endforeach;

		endforeach;
	}
}

add_action( 'rest_api_init', 'wp_rest_filter_add_filters' );
 /**
  * Add the necessary filter to each post type
  **/
function wp_rest_filter_add_filters() {
	foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
		add_filter( 'rest_' . $post_type->name . '_query', 'wp_rest_filter_add_filter_param', 10, 2 );
	}
}
/**
 * Add the filter parameter
 *
 * @param  array           $args    The query arguments.
 * @param  WP_REST_Request $request Full details about the request.
 * @return array $args.
 **/
function wp_rest_filter_add_filter_param( $args, $request ) {
	// Bail out if no filter parameter is set.
	if ( empty( $request['filter'] ) || ! is_array( $request['filter'] ) ) {
		return $args;
	}
	$filter = $request['filter'];
	if ( isset( $filter['posts_per_page'] ) && ( (int) $filter['posts_per_page'] >= 1 && (int) $filter['posts_per_page'] <= 100 ) ) {
		$args['posts_per_page'] = $filter['posts_per_page'];
	}
	global $wp;
	$vars = apply_filters( 'rest_query_vars', $wp->public_query_vars );
	function allow_meta_query( $valid_vars ) {
		$valid_vars = array_merge( $valid_vars, array( 'meta_query', 'meta_key', 'meta_value', 'meta_compare' ) );
		return $valid_vars;
	}
	$vars = allow_meta_query( $vars );

	foreach ( $vars as $var ) {
		if ( isset( $filter[ $var ] ) ) {
			$args[ $var ] = $filter[ $var ];
		}
	}
	return $args;
}
