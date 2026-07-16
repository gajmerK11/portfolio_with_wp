<?php
/**
 * Front page: single-scroll layout assembled from section template-parts.
 *
 * Each section is a self-contained file under template-parts/sections/.
 * Add new sections by dropping a file there and loading it below.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/sections/home' );
get_template_part( 'template-parts/sections/projects' );
get_template_part( 'template-parts/sections/quote' );
get_template_part( 'template-parts/sections/about' );

get_footer();
