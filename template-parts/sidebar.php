<?php
/**
 * Left fixed sidebar: profile, nav, language switcher, social links.
 *
 * Width and profile size come from the --sidebar-w / --profile-size variables
 * (src/input.css), surfaced as the Tailwind `sidebar` and `profile` spacing
 * tokens so header.php's divider line and content offset stay in step.
 *
 * Nav items carry data-target matching the section IDs in the content
 * area. assets/js/main.js toggles .is-active as the user scrolls.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

// Nav items: label => section id. Icons follow currentColor via stroke.
$portfolio_nav = array(
	'home'     => array(
		'label' => __( 'Home', 'portfolio' ),
		'icon'  => '<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
	),
	'projects' => array(
		'label' => __( 'Projects', 'portfolio' ),
		'icon'  => '<path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
	),
	'experience' => array(
		'label' => __( 'Experience', 'portfolio' ),
		'icon'  => '<path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
	),
	'about'    => array(
		'label' => __( 'About Me', 'portfolio' ),
		'icon'  => '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
	),
);
?>
<aside class="hidden md:flex w-sidebar flex-col justify-between py-14 px-8 z-30 bg-white min-h-screen fixed left-0 top-0 bottom-0 items-center">

	<?php
	// Profile, nav and socials are three separate flex children so the aside's
	// justify-between spreads them across the full column: photo at the top,
	// nav on the centre line, socials at the foot. Nothing is nudged with
	// fixed margins, so the layout re-balances itself at any viewport height.
	?>
	<div class="relative w-profile h-profile">
		<?php echo portfolio_render_sidebar_profile(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>
	</div>

	<nav class="flex flex-col gap-5 items-start text-xl font-normal" aria-label="<?php esc_attr_e( 'Primary', 'portfolio' ); ?>">
		<?php foreach ( $portfolio_nav as $target => $item ) : ?>
			<a
				class="nav-item<?php echo 'home' === $target ? ' is-active' : ''; ?>"
				href="#<?php echo esc_attr( $target ); ?>"
				data-target="<?php echo esc_attr( $target ); ?>"
			>
				<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG path. ?></svg>
				<span class="nav-label"><?php echo esc_html( $item['label'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>

	<!-- Social links -->
	<div class="flex items-center justify-center gap-6 text-dark">
		<a class="hover:text-[#0A66C2] transition-colors" href="#" aria-label="LinkedIn">
			<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
		</a>
		<a class="hover:text-[#181717] transition-colors" href="#" aria-label="GitHub">
			<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"></path></svg>
		</a>
	</div>
</aside>
