<?php
/**
 * Home / hero section.
 *
 * Mirrors the Stitch design: numbered code-style greeting card, orange
 * accent square, floating background tech icons, "Work with me" CTA,
 * and a "Read more" scroll cue.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

// Floating background icons. slug => [label, position, size]. Icons via simpleicons CDN.
$portfolio_tech = array(
	array( 'wordpress',  'WordPress',  'top:20%;left:75%;', 'w-12 h-12' ),
	array( 'cpanel',     'cPanel',     'top:45%;left:85%;', 'w-10 h-10' ),
	array( 'python',     'Python',     'top:75%;left:70%;', 'w-12 h-12' ),
	array( 'postgresql', 'PostgreSQL', 'top:85%;left:50%;', 'w-14 h-14' ),
	array( 'django',     'Django',     'top:75%;left:30%;', 'w-12 h-12' ),
	array( 'nodedotjs',  'Node.js',    'top:45%;left:20%;', 'w-12 h-12' ),
	array( 'react',      'React',      'top:20%;left:35%;', 'w-14 h-14' ),
	array( 'php',        'PHP',        'top:10%;left:55%;', 'w-10 h-10' ),
);
?>
<section id="home" data-section="home" class="section relative min-h-screen flex flex-col justify-center items-center p-10 overflow-hidden">

	<!-- Floating tech icons (background) -->
	<div class="absolute inset-0 pointer-events-none overflow-hidden z-0" aria-hidden="true">
		<?php foreach ( $portfolio_tech as $tech ) : ?>
			<div class="floating-icon <?php echo esc_attr( $tech[3] ); ?>" style="<?php echo esc_attr( $tech[2] ); ?>transform:translate(-50%,-50%);">
				<img
					alt="<?php echo esc_attr( $tech[1] ); ?>"
					src="https://cdn.simpleicons.org/<?php echo esc_attr( $tech[0] ); ?>"
					loading="lazy"
				>
			</div>
		<?php endforeach; ?>
	</div>

	<!-- Top-right CTA -->
	<div class="absolute top-12 right-12 z-20">
		<a class="btn-glow bg-primary text-white px-8 py-3 rounded-full font-semibold flex items-center gap-2 hover:bg-orange-700 transition-colors shadow-btn-glow" href="#">
			<?php esc_html_e( 'Work with me', 'portfolio' ); ?>
			<svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
		</a>
	</div>

	<!-- Greeting card -->
	<div class="relative z-10 w-full max-w-2xl flex flex-col items-center">
		<div class="absolute bg-primary w-24 h-24 rounded-4xl -top-6 -left-8 z-0"></div>

		<div class="bg-white rounded-4xl p-12 w-full relative z-10 border border-gray-100 shadow-code-box">
			<div class="font-mono text-xl sm:text-2xl leading-relaxed text-gray-800">
				<div class="flex items-center gap-6 mb-4">
					<span class="text-neutral text-lg">01</span>
					<div><span class="text-neutral">&lt;</span> <span class="font-bold"><?php esc_html_e( "Hi, I'm", 'portfolio' ); ?> <span class="text-primary">Brice</span> !</span> <span class="text-neutral">&gt;</span></div>
				</div>
				<div class="flex items-center gap-6 mb-4">
					<span class="text-neutral text-lg">02</span>
					<div><span class="text-neutral">&lt;</span> <span class="font-bold">A <span class="text-primary"><?php esc_html_e( 'passionate backend dev', 'portfolio' ); ?></span></span></div>
				</div>
				<div class="flex items-center gap-6">
					<span class="text-neutral text-lg">03</span>
					<div class="ml-2"><span class="font-bold">and <span class="text-primary"><?php esc_html_e( 'WordPress lover', 'portfolio' ); ?> .</span></span> <span class="text-neutral">&gt;</span></div>
				</div>
			</div>
		</div>
	</div>

	<!-- Read more scroll cue -->
	<div class="absolute bottom-12 left-1/2 -translate-x-1/2 flex items-center gap-4 bg-white/80 backdrop-blur px-6 py-2 rounded-full shadow-sm z-20">
		<span class="font-medium text-gray-600"><?php esc_html_e( 'Read more', 'portfolio' ); ?></span>
		<div class="bg-primary text-white p-2 rounded-full shadow-btn-glow cursor-pointer hover:bg-orange-700 transition-colors">
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 14l-7 7m0 0l-7-7m7 7V3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
		</div>
	</div>
</section>
