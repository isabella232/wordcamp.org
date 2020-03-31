<?php

namespace WordCamp\SpeakerFeedback\Form;

use WP_Post, WP_Query;
use function WordCamp\SpeakerFeedback\{ get_views_path, get_assets_url };
use function WordCamp\SpeakerFeedback\Post\post_accepts_feedback;
use const WordCamp\SpeakerFeedback\{ OPTION_KEY, QUERY_VAR };
use const WordCamp\SpeakerFeedback\Post\ACCEPT_INTERVAL_IN_SECONDS;

defined( 'WPINC' ) || die();

add_filter( 'the_content', __NAMESPACE__ . '\render' );
add_filter( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_assets' );

/**
 * Check if the current page should include the feedback form.
 *
 * @global WP_Query $wp_query
 *
 * @return bool
 */
function has_feedback_form() {
	global $wp_query;
	return false !== $wp_query->get( QUERY_VAR, false );
}

/**
 * Short-circuit the content, and output the feedback form (or the session select).
 *
 * This assumes that `wcb_session` is the only post type supporting speaker feedback. This will need to be updated
 * if more post type support is added.
 *
 * @global WP_Post $post
 *
 * @return string
 */
function render( $content ) {
	global $post;

	$now = date_create( time(), wp_timezone() );

	if ( has_feedback_form() ) {
		$accepts_feedback = post_accepts_feedback( $post->ID );

		if ( is_wp_error( $accepts_feedback ) ) {
			$message = $accepts_feedback->get_error_message();
			$file    = 'form-not-available.php';
		} else {
			$file = 'form-feedback.php';
		}

		ob_start();
		require get_views_path() . $file;

		$content = $content . ob_get_clean(); // Append form to the normal content.
	} elseif ( is_page( get_option( OPTION_KEY ) ) ) {
		$wordcamp   = get_wordcamp_post();
		$start_date = $wordcamp->meta['Start Date (YYYY-mm-dd)'][0] ?? $now->getTimestamp() + YEAR_IN_SECONDS;
		$end_date   = $wordcamp->meta['End Date (YYYY-mm-dd)'][0] ?? $start_date + DAY_IN_SECONDS;

		if ( $now->getTimestamp() < absint( $start_date ) ) {
			$message = __( 'Feedback forms are not available until the event has started.', 'wordcamporg' );
			$file    = 'form-not-available.php';
		} elseif ( $now->getTimestamp() > absint( $end_date ) + ACCEPT_INTERVAL_IN_SECONDS ) {
			$message = __( 'Feedback forms are closed for this event.', 'wordcamporg' );
			$file    = 'form-not-available.php';
		} else {
			$file = 'form-select-sessions.php';
		}

		ob_start();
		require get_views_path() . $file;

		$content = ob_get_clean(); // Replace the content.
	}

	return $content;
}

/**
 * Add stylesheet to the form page.
 */
function enqueue_assets() {
	if ( has_feedback_form() || is_page( get_option( OPTION_KEY ) ) ) {
		wp_enqueue_style(
			'speaker-feedback',
			get_assets_url() . 'build/style.css',
			array(),
			filemtime( dirname( __DIR__ ) . '/assets/build/style.css' )
		);

		wp_enqueue_script(
			'speaker-feedback',
			get_assets_url() . 'js/script.js',
			array(),
			filemtime( dirname( __DIR__ ) . '/assets/js/script.js' ),
			true
		);
	}
}
