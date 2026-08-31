<?php
/**
 * Programming quote that opens the About section.
 *
 * The section opens on a rotating programming quote. This started out as a
 * fallback for an empty "Lead heading 1" field; that field has since been
 * removed from the profile, so the quote is simply what the block opens with.
 *
 * The upstream service (quotes-github-readme.vercel.app) is a README badge: it
 * only ever answers with an SVG, at a fixed 600px, in its own font and its own
 * theme colours. Dropping that image straight into the page would put a dark
 * card in Poppins in the middle of a light section set in Sora. So the SVG is
 * fetched server-side and the quote and author are read back out of it, and the
 * section renders them in its own markup — same element, same classes, same
 * font and weight as an authored heading.
 *
 * The response is cached in a transient because it is a third-party request on
 * a front-page render: without it every uncached page view would block on an
 * external host, and a slow or down service would take the page with it. A
 * failed fetch is cached too, for a shorter time, so an outage costs one
 * request an hour rather than one per visitor.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

const PORTFOLIO_QUOTE_TRANSIENT = 'portfolio_about_quote';
const PORTFOLIO_QUOTE_ENDPOINT  = 'https://quotes-github-readme.vercel.app/api?type=horizontal&theme=radical';

/**
 * Fetch a programming quote, cached.
 *
 * @return array{text:string,author:string}|array{} Quote parts, or an empty
 *                                                  array when unavailable.
 */
function portfolio_get_about_quote() {
	$cached = get_transient( PORTFOLIO_QUOTE_TRANSIENT );

	// A cached failure is stored as an empty string, which is distinct from
	// the false get_transient() returns when nothing is cached at all.
	if ( is_array( $cached ) ) {
		return $cached;
	}
	if ( '' === $cached ) {
		return array();
	}

	$response = wp_remote_get(
		PORTFOLIO_QUOTE_ENDPOINT,
		array(
			'timeout' => 5,
			'headers' => array( 'Accept' => 'image/svg+xml' ),
		)
	);

	$quote = array();

	if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
		$quote = portfolio_parse_quote_svg( wp_remote_retrieve_body( $response ) );
	}

	if ( $quote ) {
		set_transient( PORTFOLIO_QUOTE_TRANSIENT, $quote, 6 * HOUR_IN_SECONDS );
	} else {
		// Short back-off so the quote returns on its own once the service does.
		set_transient( PORTFOLIO_QUOTE_TRANSIENT, '', HOUR_IN_SECONDS );
	}

	return $quote;
}

/**
 * Pull the quote and author out of the badge SVG.
 *
 * The badge wraps its content in a foreignObject: the quote is the only <h3>
 * and the author the only <p>, written as "- Name". Both are matched
 * non-greedily and stripped of any inner markup, so a quote containing a tag
 * cannot leak one into the page.
 *
 * @param string $svg Raw SVG response body.
 * @return array{text:string,author:string}|array{} Parsed parts, or empty when
 *                                                  the shape is not what we expect.
 */
function portfolio_parse_quote_svg( $svg ) {
	if ( ! is_string( $svg ) || '' === $svg ) {
		return array();
	}

	if ( ! preg_match( '#<h3[^>]*>(.*?)</h3>#s', $svg, $text_match ) ) {
		return array();
	}

	$text = trim( wp_strip_all_tags( html_entity_decode( $text_match[1], ENT_QUOTES, 'UTF-8' ) ) );
	if ( '' === $text ) {
		return array();
	}

	$author = '';
	if ( preg_match( '#<p[^>]*>(.*?)</p>#s', $svg, $author_match ) ) {
		$author = trim( wp_strip_all_tags( html_entity_decode( $author_match[1], ENT_QUOTES, 'UTF-8' ) ) );
		// The badge prefixes the name with a dash; the section sets its own.
		$author = ltrim( $author, "-– \t" );
	}

	return array(
		'text'   => sanitize_text_field( $text ),
		'author' => sanitize_text_field( $author ),
	);
}

/**
 * Drop the cache when the About user saves their profile.
 *
 * Saving the profile is the one moment the owner is looking at the About
 * content, so it is the natural place to let them pull a new quote: save the
 * profile, reload the front page, different quote. Nothing else can change it
 * inside the cache window.
 *
 * @param int $user_id Saved user.
 */
function portfolio_flush_about_quote( $user_id ) {
	if ( function_exists( 'portfolio_about_user_id' ) && (int) $user_id === portfolio_about_user_id() ) {
		delete_transient( PORTFOLIO_QUOTE_TRANSIENT );
	}
}
add_action( 'personal_options_update', 'portfolio_flush_about_quote' );
add_action( 'edit_user_profile_update', 'portfolio_flush_about_quote' );
