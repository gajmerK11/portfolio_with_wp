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
	'skills'   => array(
		'label' => __( 'Skills', 'portfolio' ),
		'icon'  => '<path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
	),
	'about'    => array(
		'label' => __( 'About Me', 'portfolio' ),
		'icon'  => '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>',
	),
);
?>
<?php
// Layout lives entirely in .site-sidebar (src/input.css), not in utilities
// here: below md this becomes a floating glass icon bar and at md a full
// white column, and the two sets of properties would otherwise fight.
?>
<aside class="site-sidebar">

	<?php
	// Profile, nav and socials are three separate flex children so the aside's
	// justify-between spreads them across the full column: photo at the top,
	// nav on the centre line, socials at the foot. Nothing is nudged with
	// fixed margins, so the layout re-balances itself at any viewport height.
	// The photo and the socials are dropped below md — only icons there.
	?>
	<div class="sidebar-profile">
		<?php echo portfolio_render_sidebar_profile(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>
	</div>

	<nav class="sidebar-nav" aria-label="<?php esc_attr_e( 'Primary', 'portfolio' ); ?>">
		<?php foreach ( $portfolio_nav as $target => $item ) : ?>
			<button
				type="button"
				class="nav-item<?php echo 'home' === $target ? ' is-active' : ''; ?>"
				data-target="<?php echo esc_attr( $target ); ?>"
			>
				<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG path. ?></svg>
				<span class="nav-label"><?php echo esc_html( $item['label'] ); ?></span>
			</button>
		<?php endforeach; ?>
	</nav>

	<?php
	// Drag handle, so the floating bar can be moved clear of the content it
	// overlays. The reference does this with GSAP Draggable (index.html:1186);
	// this uses Pointer Events instead — see assets/js/main.js ->
	// setupSidebarDrag. Only the handle drags, never the whole bar, or a tap on
	// a nav icon would move it. Glyph is the reference's own img/drag.svg.
	?>
	<button
		type="button"
		class="sidebar-drag"
		data-sidebar-drag
		aria-label="<?php esc_attr_e( 'Move the navigation bar', 'portfolio' ); ?>"
	>
		<svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false"><path d="M7.99934 0C8.19818 0.000175052 8.38882 0.0793075 8.52934 0.22L10.0293 1.72C10.1618 1.86217 10.2339 2.05022 10.2305 2.24452C10.2271 2.43882 10.1484 2.62421 10.011 2.76162C9.87355 2.89903 9.68816 2.97775 9.49386 2.98117C9.29956 2.9846 9.11152 2.91248 8.96934 2.78L8.74934 2.56V4.25C8.74934 4.44891 8.67032 4.63968 8.52967 4.78033C8.38902 4.92098 8.19825 5 7.99934 5C7.80043 5 7.60966 4.92098 7.46901 4.78033C7.32836 4.63968 7.24934 4.44891 7.24934 4.25V2.56L7.02934 2.78C6.88717 2.91248 6.69912 2.9846 6.50482 2.98117C6.31052 2.97775 6.12513 2.89903 5.98772 2.76162C5.85031 2.62421 5.77159 2.43882 5.76817 2.24452C5.76474 2.05022 5.83686 1.86217 5.96934 1.72L7.46934 0.22C7.60986 0.0793075 7.80049 0.000175052 7.99934 0ZM9.99934 8C9.99934 8.53043 9.78863 9.03914 9.41355 9.41421C9.03848 9.78929 8.52977 10 7.99934 10C7.46891 10 6.9602 9.78929 6.58513 9.41421C6.21005 9.03914 5.99934 8.53043 5.99934 8C5.99934 7.46957 6.21005 6.96086 6.58513 6.58579C6.9602 6.21071 7.46891 6 7.99934 6C8.52977 6 9.03848 6.21071 9.41355 6.58579C9.78863 6.96086 9.99934 7.46957 9.99934 8ZM0.21934 7.47C0.0788896 7.61063 0 7.80125 0 8C0 8.19875 0.0788896 8.38937 0.21934 8.53L1.71934 10.03C1.86152 10.1625 2.04956 10.2346 2.24386 10.2312C2.43816 10.2277 2.62355 10.149 2.76096 10.0116C2.89838 9.87421 2.97709 9.68882 2.98052 9.49452C2.98394 9.30022 2.91182 9.11217 2.77934 8.97L2.55934 8.75H4.24934C4.44825 8.75 4.63902 8.67098 4.77967 8.53033C4.92032 8.38968 4.99934 8.19891 4.99934 8C4.99934 7.80109 4.92032 7.61032 4.77967 7.46967C4.63902 7.32902 4.44825 7.25 4.24934 7.25H2.55934L2.77934 7.03C2.91182 6.88783 2.98394 6.69978 2.98052 6.50548C2.97709 6.31118 2.89838 6.12579 2.76096 5.98838C2.62355 5.85097 2.43816 5.77225 2.24386 5.76883C2.04956 5.7654 1.86152 5.83752 1.71934 5.97L0.21934 7.47ZM7.99934 16C8.19818 15.9998 8.38882 15.9207 8.52934 15.78L10.0293 14.28C10.103 14.2113 10.1621 14.1285 10.2031 14.0365C10.2441 13.9445 10.2662 13.8452 10.2679 13.7445C10.2697 13.6438 10.2512 13.5438 10.2135 13.4504C10.1757 13.357 10.1196 13.2722 10.0484 13.201C9.97716 13.1297 9.89233 13.0736 9.79894 13.0359C9.70555 12.9982 9.60552 12.9796 9.50482 12.9814C9.40411 12.9832 9.3048 13.0052 9.2128 13.0462C9.1208 13.0872 9.038 13.1463 8.96934 13.22L8.74934 13.44V11.75C8.74934 11.5511 8.67032 11.3603 8.52967 11.2197C8.38902 11.079 8.19825 11 7.99934 11C7.80043 11 7.60966 11.079 7.46901 11.2197C7.32836 11.3603 7.24934 11.5511 7.24934 11.75V13.44L7.02934 13.22C6.88717 13.0875 6.69912 13.0154 6.50482 13.0188C6.31052 13.0223 6.12513 13.101 5.98772 13.2384C5.85031 13.3758 5.77159 13.5612 5.76817 13.7555C5.76474 13.9498 5.83686 14.1378 5.96934 14.28L7.46934 15.78C7.60934 15.921 7.80034 16 7.99934 16ZM15.7793 7.47C15.9198 7.61063 15.9987 7.80125 15.9987 8C15.9987 8.19875 15.9198 8.38937 15.7793 8.53L14.2793 10.03C14.2107 10.1037 14.1279 10.1628 14.0359 10.2038C13.9439 10.2448 13.8446 10.2668 13.7439 10.2686C13.6432 10.2704 13.5431 10.2518 13.4497 10.2141C13.3564 10.1764 13.2715 10.1203 13.2003 10.049C13.1291 9.97782 13.0729 9.89299 13.0352 9.7996C12.9975 9.70621 12.979 9.60618 12.9807 9.50548C12.9825 9.40477 13.0046 9.30546 13.0456 9.21346C13.0866 9.12146 13.1457 9.03866 13.2193 8.97L13.4393 8.75H11.7493C11.5504 8.75 11.3597 8.67098 11.219 8.53033C11.0784 8.38968 10.9993 8.19891 10.9993 8C10.9993 7.80109 11.0784 7.61032 11.219 7.46967C11.3597 7.32902 11.5504 7.25 11.7493 7.25H13.4393L13.2193 7.03C13.0869 6.88783 13.0147 6.69978 13.0182 6.50548C13.0216 6.31118 13.1003 6.12579 13.2377 5.98838C13.3751 5.85097 13.5605 5.77225 13.7548 5.76883C13.9491 5.7654 14.1372 5.83752 14.2793 5.97L15.7793 7.47Z"></path></svg>
	</button>

	<!-- Social links -->
	<div class="sidebar-social">
		<a class="hover:text-[#0A66C2] transition-colors" href="#" aria-label="LinkedIn">
			<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
		</a>
		<a class="hover:text-[#181717] transition-colors" href="#" aria-label="GitHub">
			<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"></path></svg>
		</a>
	</div>
</aside>
