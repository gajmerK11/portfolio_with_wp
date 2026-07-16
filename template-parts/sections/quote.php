<?php
/**
 * Quote section.
 *
 * A random developer quote (quotes-github-readme) presented in the site's
 * code-style aesthetic. Sits between Projects and About. Intentionally has
 * no data-section attribute, so the sidebar keeps the previous nav item
 * highlighted while scrolling through it.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;
?>
<section id="quote" class="section relative flex flex-col items-center px-10 py-16">

	<h2 class="font-mono text-lg sm:text-xl font-bold text-dark mb-5 text-center">
		<span class="text-neutral">&lt;</span> quote of the <span class="text-primary">day</span> <span class="text-neutral">/&gt;</span>
	</h2>

	<img
		src="https://quotes-github-readme.vercel.app/api?type=horizontal&amp;theme=graywhite"
		alt="<?php esc_attr_e( 'Random developer quote', 'portfolio' ); ?>"
		class="w-full max-w-lg rounded-xl border border-gray-200"
		loading="lazy"
	>
</section>
