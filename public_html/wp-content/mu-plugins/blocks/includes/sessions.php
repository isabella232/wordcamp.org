<?php
namespace WordCamp\Blocks\Sessions;
defined( 'WPINC' ) || die();

use WordCamp\Blocks;

/**
 * Register block types and enqueue scripts.
 *
 * @return void
 */
function init() {
	register_block_type(
		'wordcamp/sessions',
		[
			'attributes'      => get_attributes_schema(),
			'render_callback' => __NAMESPACE__ . '\render',
			'editor_script'   => 'wordcamp-blocks',
			'editor_style'    => 'wordcamp-blocks',
			'style'           => 'wordcamp-blocks',
		]
	);
}

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Render the block on the front end.
 *
 * @param array $attributes Block attributes.
 *
 * @return string
 */
function render( $attributes ) {
	$defaults   = wp_list_pluck( get_attributes_schema(), 'default' );
	$attributes = wp_parse_args( $attributes, $defaults );



	$container_classes = [
		'wordcamp-sessions-block',
		sanitize_html_class( $attributes['className'] ),
	];

	$container_classes = implode( ' ', $container_classes );

	ob_start();
	require Blocks\PLUGIN_DIR . 'view/sessions.php';
	$html = ob_get_clean();

	return $html;
}

/**
 * Add data to be used by the JS scripts in the block editor.
 *
 * @param array $data
 *
 * @return array
 */
function add_script_data( array $data ) {
	$data['sessions'] = [
		'schema'  => get_attributes_schema(),
		'options' => get_options(),
	];

	return $data;
}

add_filter( 'wordcamp_blocks_script_data', __NAMESPACE__ . '\add_script_data' );

/**
 * Get the schema for the block's attributes.
 *
 * @return array
 */
function get_attributes_schema() {
	return [
		'mode'          => [
			'type'    => 'string',
			'enum'    => wp_list_pluck( get_options( 'mode' ), 'value' ),
			'default' => '',
		],
		'post_ids'      => [
			'type'    => 'array',
			'default' => [],
			'items'   => [
				'type' => 'integer',
			],
		],
		'track_ids'     => [
			'type'    => 'array',
			'default' => [],
			'items'   => [
				'type' => 'integer',
			],
		],
		'category_ids'  => [
			'type'    => 'array',
			'default' => [],
			'items'   => [
				'type' => 'integer',
			],
		],
		'sort'          => [
			'type'    => 'string',
			'enum'    => wp_list_pluck( get_options( 'sort' ), 'value' ),
			'default' => 'title_asc',
		],
		'filter_date'   => [
			'type'    => 'string',
			'default' => '',
		],
		'className'     => [
			'type'    => 'string',
			'default' => '',
		],
		'show_images'   => [
			'type'    => 'bool',
			'default' => true,
		],
		'image_size'    => [
			'type'    => 'integer',
			'minimum' => 25,
			'maximum' => 600,
			'default' => 150,
		],
		'image_align'   => [
			'type'    => 'string',
			'enum'    => wp_list_pluck( get_options( 'align' ), 'value' ),
			'default' => 'none',
		],
		'content'       => [
			'type'    => 'string',
			'enum'    => wp_list_pluck( get_options( 'content' ), 'value' ),
			'default' => 'full',
		],
		'excerpt_more'  => [
			'type'    => 'bool',
			'default' => false,
		],
		'show_category' => [
			'type'    => 'bool',
			'default' => false,
		],
		'show_track'    => [
			'type'    => 'bool',
			'default' => false,
		],
		'show_speaker'  => [
			'type'    => 'bool',
			'default' => false,
		],
	];
}

/**
 * Get the label/value pairs for all options or a specific type.
 *
 * @param string $type
 *
 * @return array
 */
function get_options( $type = '' ) {
	$options = [
		'align'   => [
			[
				'label' => _x( 'None', 'alignment option', 'wordcamporg' ),
				'value' => 'none',
			],
			[
				'label' => _x( 'Left', 'alignment option', 'wordcamporg' ),
				'value' => 'left',
			],
			[
				'label' => _x( 'Center', 'alignment option', 'wordcamporg' ),
				'value' => 'center',
			],
			[
				'label' => _x( 'Right', 'alignment option', 'wordcamporg' ),
				'value' => 'right',
			],
		],
		'content' => [
			[
				'label' => _x( 'Full', 'content option', 'wordcamporg' ),
				'value' => 'full',
			],
			[
				'label' => _x( 'Excerpt', 'content option', 'wordcamporg' ),
				'value' => 'excerpt',
			],
			[
				'label' => _x( 'None', 'content option', 'wordcamporg' ),
				'value' => 'none',
			],
		],
		'mode'    => [
			[
				'label' => '',
				'value' => '',
			],
			[
				'label' => _x( 'List all sessions', 'mode option', 'wordcamporg' ),
				'value' => 'all',
			],
			[
				'label' => _x( 'Choose sessions', 'mode option', 'wordcamporg' ),
				'value' => 'specific_posts',
			],
			[
				'label' => _x( 'Choose tracks', 'mode option', 'wordcamporg' ),
				'value' => 'specific_tracks',
			],
			[
				'label' => _x( 'Choose session categories', 'mode option', 'wordcamporg' ),
				'value' => 'specific_categories',
			],
		],
		'sort'    => [
			[
				'label' => _x( 'Session Time', 'sort option', 'wordcamporg' ),
				'value' => 'session_time',
			],
			[
				'label' => _x( 'A → Z', 'sort option', 'wordcamporg' ),
				'value' => 'title_asc',
			],
			[
				'label' => _x( 'Z → A', 'sort option', 'wordcamporg' ),
				'value' => 'title_desc',
			],
			[
				'label' => _x( 'Newest to Oldest', 'sort option', 'wordcamporg' ),
				'value' => 'date_desc',
			],
			[
				'label' => _x( 'Oldest to Newest', 'sort option', 'wordcamporg' ),
				'value' => 'date_asc',
			],
		],
	];

	if ( $type ) {
		if ( ! empty( $options[ $type ] ) ) {
			return $options[ $type ];
		} else {
			return [];
		}
	}

	return $options;
}

/**
 * Get the posts to display in the block.
 *
 * @param array $attributes
 *
 * @return array
 */
function get_session_posts( array $attributes ) {
	$post_args = [
		'post_type'      => 'wcb_session',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	];

	switch ( $attributes['sort'] ) {
		case 'session_time':
			$post_args['meta_key'] = '_wcpt_session_time';
			$post_args['orderby']  = 'meta_value_num title';
			$post_args['order']    = 'asc';
			break;

		case 'title_asc':
		case 'title_desc':
		case 'date_desc':
		case 'date_asc':
			$sort = explode( '_', $attributes['sort'] );

			if ( 2 === count( $sort ) ) {
				$post_args['orderby'] = $sort[0];
				$post_args['order']   = $sort[1];
			}
			break;
	}

	switch ( $attributes['mode'] ) {
		case 'specific_posts':
			$post_args['post__in'] = $attributes['post_ids'];
			break;

		case 'specific_tracks':
			$post_args['tax_query'] = [
				[
					'taxonomy' => 'wcb_track',
					'field'    => 'id',
					'terms'    => $attributes['track_ids'],
				],
			];
			break;

		case 'specific_categories':
			$post_args['tax_query'] = [
				[
					'taxonomy' => 'wcb_session_category',
					'field'    => 'id',
					'terms'    => $attributes['category_ids'],
				],
			];
			break;
	}

	return get_posts( $post_args );
}

/**
 * Get speaker posts grouped by session.
 *
 * @param array $session_ids
 *
 * @return array
 */
function get_session_speakers( array $session_ids ) {
	$speakers_by_session = [];

	$session_args = [
		'post_type'      => 'wcb_session',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'post__in'       => $session_ids,
	];

	$session_posts = get_posts( $session_args );

	foreach ( $session_posts as $session ) {
		$speaker_ids = get_post_meta( $session->ID, '_wcpt_speaker_id', false );

		if ( ! empty( $speaker_ids ) ) {
			$speaker_args = [
				'post_type'      => 'wcb_speaker',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__in'       => $speaker_ids,
			];

			$speakers_by_session[ $session->ID ] = get_posts( $speaker_args );
		}
	}

	return $speakers_by_session;
}
