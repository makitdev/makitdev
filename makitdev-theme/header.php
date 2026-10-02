<?php
/**
 * Theme header: doctype, head, floating pill navigation, and mobile drawer.
 *
 * The markup mirrors the repository's index.html so the WordPress build and
 * the static build stay visually identical.
 *
 * @package makitdev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$makitdev_github    = 'https://github.com/makitdev';
$makitdev_contribs  = home_url( '/contributors/' );
$makitdev_theme_uri = get_template_directory_uri();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="makitdev is an open-source organization focused on building practical software and sharing it openly — developer tools, security utilities, applications and experiments.">
	<meta name="theme-color" content="#070807">

	<meta property="og:type" content="website">
	<meta property="og:title" content="makitdev — Build. Share. Improve.">
	<meta property="og:description" content="Practical open-source software for developers, security researchers, and curious builders.">
	<meta property="og:site_name" content="makitdev">
	<meta property="og:image" content="<?php echo esc_url( home_url( '/assets/images/og-1200x630.png' ) ); ?>">
	<meta property="og:image:width" content="1200">
	<meta property="og:image:height" content="630">
	<meta property="og:image:alt" content="makitdev — Build. Share. Improve.">

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="makitdev — Build. Share. Improve.">
	<meta name="twitter:image" content="<?php echo esc_url( home_url( '/assets/images/og-1200x630.png' ) ); ?>">

	<link rel="preconnect" href="https://api.github.com" crossorigin>
	<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( $makitdev_theme_uri . '/assets/images/favicon.svg' ); ?>">
	<link rel="preload" href="<?php echo esc_url( $makitdev_theme_uri . '/assets/fonts/space-grotesk-latin.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="<?php echo esc_url( $makitdev_theme_uri . '/assets/fonts/inter-latin.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
	<?php wp_head(); ?>

	<script>
		(function () {
			var d = document.documentElement;
			d.classList.add("js");
			if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
				d.classList.add("is-loaded");
			} else {
				// Two frames on purpose: the first lets the browser paint the
				// initial hidden state, the second flips to the visible state.
				// A single rAF fires before the first paint, so the entrance
				// transition never ran at all.
				requestAnimationFrame(function () {
					requestAnimationFrame(function () {
						d.classList.add("is-loaded");
					});
				});
			}
		})();
	</script>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'makitdev' ); ?></a>

<header class="site-header">
	<div class="nav">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="brand-mark" aria-hidden="true"></span>
			<span><?php esc_html_e( 'makitdev', 'makitdev' ); ?></span>
		</a>
		<nav aria-label="<?php esc_attr_e( 'Primary', 'makitdev' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_class'     => 'nav-links',
						'container'      => false,
						'depth'          => 1,
					)
				);
			} else {
				?>
				<ul class="nav-links">
					<li><a href="<?php echo esc_url( home_url( '/#home' ) ); ?>"><?php esc_html_e( 'Home', 'makitdev' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#about' ) ); ?>"><?php esc_html_e( 'About', 'makitdev' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#projects' ) ); ?>"><?php esc_html_e( 'Projects', 'makitdev' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#principles' ) ); ?>"><?php esc_html_e( 'Principles', 'makitdev' ); ?></a></li>
					<li><a href="<?php echo esc_url( $makitdev_contribs ); ?>"><?php esc_html_e( 'Contributors', 'makitdev' ); ?></a></li>
				</ul>
				<?php
			}
			?>
		</nav>
		<a class="nav-gh" href="<?php echo esc_url( $makitdev_github ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'GitHub', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8599;</span></a>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-drawer">
			<span class="nav-toggle-bars" aria-hidden="true"><span></span><span></span><span></span></span>
			<?php esc_html_e( 'Menu', 'makitdev' ); ?>
		</button>
	</div>
</header>

<div class="nav-scrim" hidden></div>
<div class="nav-drawer" id="nav-drawer" hidden>
	<nav aria-label="<?php esc_attr_e( 'Mobile', 'makitdev' ); ?>">
		<ul class="nav-drawer-list">
			<li><a href="<?php echo esc_url( home_url( '/#home' ) ); ?>" style="--drawer-delay:80ms"><span><?php esc_html_e( 'Home', 'makitdev' ); ?></span><span class="nav-drawer-index">01</span></a></li>
			<li><a href="<?php echo esc_url( home_url( '/#about' ) ); ?>" style="--drawer-delay:130ms"><span><?php esc_html_e( 'About', 'makitdev' ); ?></span><span class="nav-drawer-index">02</span></a></li>
			<li><a href="<?php echo esc_url( home_url( '/#projects' ) ); ?>" style="--drawer-delay:180ms"><span><?php esc_html_e( 'Projects', 'makitdev' ); ?></span><span class="nav-drawer-index">03</span></a></li>
			<li><a href="<?php echo esc_url( home_url( '/#principles' ) ); ?>" style="--drawer-delay:230ms"><span><?php esc_html_e( 'Principles', 'makitdev' ); ?></span><span class="nav-drawer-index">04</span></a></li>
			<li><a href="<?php echo esc_url( $makitdev_contribs ); ?>" style="--drawer-delay:280ms"><span><?php esc_html_e( 'Contributors', 'makitdev' ); ?></span><span class="nav-drawer-index">05</span></a></li>
		</ul>
		<a class="btn btn--ghost nav-drawer-gh" href="<?php echo esc_url( $makitdev_github ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'GitHub', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8599;</span></a>
	</nav>
</div>