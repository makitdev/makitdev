<?php
/**
 * Theme footer: giant wordmark, link column, and closing markup.
 *
 * @package makitdev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$makitdev_github   = 'https://github.com/makitdev';
$makitdev_contribs = home_url( '/contributors/' );
?>

<footer class="site-footer">
	<div class="container">
		<div class="footer-top">
			<div>
				<p class="footer-word"><?php esc_html_e( 'makitdev', 'makitdev' ); ?></p>
				<p class="footer-tagline"><?php esc_html_e( 'Build. Share. Improve.', 'makitdev' ); ?></p>
			</div>
			<ul class="footer-links">
				<li><a href="<?php echo esc_url( home_url( '/#about' ) ); ?>"><?php esc_html_e( 'About', 'makitdev' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/#projects' ) ); ?>"><?php esc_html_e( 'Projects', 'makitdev' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/#principles' ) ); ?>"><?php esc_html_e( 'Principles', 'makitdev' ); ?></a></li>
				<li><a href="<?php echo esc_url( $makitdev_contribs ); ?>"><?php esc_html_e( 'Contributors', 'makitdev' ); ?></a></li>
				<li><a href="<?php echo esc_url( $makitdev_github ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'GitHub', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8599;</span></a></li>
			</ul>
		</div>
		<div class="footer-bottom">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'makitdev · Open-source software.', 'makitdev' ); ?></p>
			<p><?php esc_html_e( 'Built by developers, for developers.', 'makitdev' ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>