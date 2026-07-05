<?php
/**
 * Left fixed sidebar: profile, nav, language switcher, social links.
 *
 * Nav items carry data-target matching the section IDs in the content
 * area. assets/js/main.js toggles .is-active as the user scrolls.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$portfolio_uri = get_template_directory_uri();

// Nav items: label => section id. Swap labels for your own once sections exist.
$portfolio_nav = array(
	'home'     => array(
		'label' => __( 'Home', 'portfolio' ),
		'icon'  => '<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
	),
	'projects' => array(
		'label' => __( 'Projects', 'portfolio' ),
		'icon'  => '<path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
	),
	'about'    => array(
		'label' => __( 'About Me', 'portfolio' ),
		'icon'  => '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
	),
);
?>
<aside class="hidden md:flex w-72 flex-col justify-between py-16 px-6 border-r border-gray-100 z-30 bg-white min-h-screen fixed left-0 top-0 bottom-0 items-center">

	<!-- Profile + nav -->
	<div class="flex flex-col items-center w-full">
		<div class="relative w-40 h-40 mb-12">
			<div class="absolute inset-0 rounded-full flex items-center justify-center overflow-hidden border-4 border-white shadow-lg">
				<img
					alt="<?php esc_attr_e( 'Profile picture', 'portfolio' ); ?>"
					class="w-full h-full object-cover"
					src="<?php echo esc_url( $portfolio_uri . '/assets/img/profile.svg' ); ?>"
				>
			</div>
		</div>

		<nav class="flex flex-col gap-6 w-full items-center text-lg font-medium mt-16" aria-label="<?php esc_attr_e( 'Primary', 'portfolio' ); ?>">
			<?php foreach ( $portfolio_nav as $target => $item ) : ?>
				<a
					class="nav-item<?php echo 'home' === $target ? ' is-active' : ''; ?>"
					href="#<?php echo esc_attr( $target ); ?>"
					data-target="<?php echo esc_attr( $target ); ?>"
				>
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG path. ?></svg>
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
	</div>

	<!-- Language switcher (wire up to Polylang later) -->
	<div class="font-medium text-neutral">
		<button class="hover:text-primary transition-colors text-lg" type="button">EN / <span class="font-mono">ने</span></button>
	</div>

	<!-- Social links -->
	<div class="flex flex-col items-center gap-6 text-dark">
		<div class="flex gap-4">
			<a class="hover:text-primary transition-colors" href="#" aria-label="Instagram">
				<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path></svg>
			</a>
			<a class="hover:text-primary transition-colors" href="#" aria-label="LinkedIn">
				<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
			</a>
		</div>
		<div class="flex gap-4">
			<a class="hover:text-primary transition-colors" href="#" aria-label="TikTok">
				<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 448 512"><path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"></path></svg>
			</a>
			<a class="hover:text-primary transition-colors" href="#" aria-label="Behance">
				<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 512 512"><path d="M232 237.2c31.8-15.2 48.4-38.2 48.4-74 0-70.6-52.6-87.8-113.3-87.8H0v354.4h171.8c64.4 0 124.9-30.9 124.9-102.9 0-44.5-21.1-77.4-64.7-89.7zM68.3 133h82.8c34 0 57.3 11 57.3 46 0 35.8-23 48.8-59 48.8H68.3V133zm92 239.3H68.3V273.8h93.1c42.5 0 71 14.2 71 53.6 0 40.5-29 54.9-72.1 54.9zM418.1 199.1c-54.6 0-97.3 44.9-97.3 103.3 0 60.1 42 105.7 101.4 105.7 41.5 0 76.5-19.8 89.2-56.1h-58.4c-8.1 14.6-23.7 20.1-33.1 20.1-23.4 0-38.6-16-41-45.9h142.1c0-67.6-32.5-127.1-102.9-127.1zm-39.7 75.3c3.8-24.8 21.1-39.8 41.3-39.8 22.3 0 35.5 15.6 37 39.8h-78.3z"></path><path d="M375.3 137.9h91.1v31.4h-91.1z"></path></svg>
			</a>
		</div>
	</div>
</aside>
