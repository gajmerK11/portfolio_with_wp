<?php
/**
 * Fallback template.
 *
 * Real homepage lives in front-page.php. This exists so WordPress
 * always has a valid template for any query.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="min-h-screen flex items-center justify-center p-10">
	<div class="font-mono text-neutral">
		<?php esc_html_e( 'Nothing here yet.', 'portfolio' ); ?>
	</div>
</section>

<?php
get_footer();
