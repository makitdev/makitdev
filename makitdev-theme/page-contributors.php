<?php
/**
 * Template Name: Contributors Page
 *
 * Contributors page template.
 *
 * Mirrors the repository's contributors.html. The roster is rendered on the
 * server from assets/data/contributors.json — the same data the static page
 * uses through assets/data/contributors.js — and is then reconciled with live
 * GitHub contribution counts by assets/js/contributors.js, so the list still
 * works with JavaScript disabled.
 *
 * @package makitdev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'makitdev_contributor_avatar' ) ) {
	/**
	 * Avatar URL for a roster entry, falling back to the GitHub-generated one.
	 *
	 * @param array $contributor Roster entry.
	 * @return string
	 */
	function makitdev_contributor_avatar( array $contributor ) {
		$base = ! empty( $contributor['avatar'] )
			? $contributor['avatar']
			: 'https://github.com/' . rawurlencode( $contributor['username'] ) . '.png';
		$base = ( false === strpos( $base, '?' ) ) ? $base . '?s=144' : $base . '&s=144';
		return $base;
	}
}

if ( ! function_exists( 'makitdev_contributor_link' ) ) {
	/**
	 * Profile URL for a roster entry.
	 *
	 * @param array $contributor Roster entry.
	 * @return string
	 */
	function makitdev_contributor_link( array $contributor ) {
		if ( ! empty( $contributor['github'] ) ) {
			return $contributor['github'];
		}
		return 'https://github.com/' . rawurlencode( $contributor['username'] );
	}
}

$makitdev_contributors = makitdev_read_roster();

get_header();
?>

<main id="main">

	<!-- Intro -->
	<section class="page-hero" id="contributors" aria-labelledby="contributors-title">
		<div class="page-hero-media" aria-hidden="true">
			<div class="grain"></div>
		</div>
		<div class="container">
			<p class="label reveal"><?php esc_html_e( 'Contributors', 'makitdev' ); ?></p>
			<h1 class="section-title" id="contributors-title"><?php esc_html_e( 'Built in public.', 'makitdev' ); ?></h1>
			<p class="statement-copy">
				<?php esc_html_e( 'Every account here is real — nobody is invented for the sake of a screenshot. Commit counts are read live from GitHub for everyone who has pushed to a makitdev repository, and community members are credited by name.', 'makitdev' ); ?>
			</p>
		</div>
	</section>

	<!-- Roster -->
	<section class="section section--tight" id="roster" aria-labelledby="roster-heading">
		<div class="container">
			<h2 class="sr-only" id="roster-heading"><?php esc_html_e( 'Contributor list', 'makitdev' ); ?></h2>
			<ul class="contributors-grid" id="contributors-grid" aria-label="<?php esc_attr_e( 'Contributors', 'makitdev' ); ?>">
				<?php foreach ( $makitdev_contributors as $makitdev_person ) : ?>
					<li class="contributor reveal">
						<img
							class="contributor-avatar"
							src="<?php echo esc_url( makitdev_contributor_avatar( $makitdev_person ) ); ?>"
							alt="<?php echo esc_attr( ! empty( $makitdev_person['name'] ) ? $makitdev_person['name'] : $makitdev_person['username'] ); ?>"
							width="144"
							height="144"
							loading="lazy"
						>
						<div class="contributor-body">
							<div class="contributor-head">
								<div>
									<h3 class="contributor-name"><?php echo esc_html( ! empty( $makitdev_person['name'] ) ? $makitdev_person['name'] : '@' . $makitdev_person['username'] ); ?></h3>
									<p class="contributor-handle">@<?php echo esc_html( $makitdev_person['username'] ); ?></p>
								</div>
								<a class="contributor-link" href="<?php echo esc_url( makitdev_contributor_link( $makitdev_person ) ); ?>" target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'GitHub', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8599;</span>
								</a>
							</div>
							<?php if ( ! empty( $makitdev_person['role'] ) ) : ?>
								<p class="contributor-role"><?php echo esc_html( $makitdev_person['role'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $makitdev_person['contribution'] ) ) : ?>
								<p class="contributor-desc"><?php echo esc_html( $makitdev_person['contribution'] ); ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="repo-note" id="roster-note"></p>
		</div>
	</section>

	<!-- Contribute -->
	<section class="section" id="contribute" aria-labelledby="contribute-title">
		<div class="container center">
			<p class="label label--bare reveal" style="justify-content:center"><?php esc_html_e( 'Join The Build', 'makitdev' ); ?></p>
			<h2 class="section-title reveal" id="contribute-title" style="--reveal-delay:90ms"><?php esc_html_e( 'Build. Share. Improve.', 'makitdev' ); ?></h2>
			<p class="statement-copy reveal" style="--reveal-delay:160ms">
				<?php esc_html_e( 'Explore the source, open an issue, or send a pull request. Every contribution counts — documentation and tests included.', 'makitdev' ); ?>
			</p>
			<div class="cta-actions reveal" style="--reveal-delay:240ms">
				<a class="btn" href="https://github.com/makitdev/makitdev/blob/main/CONTRIBUTING.md" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'View CONTRIBUTING.md', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8599;</span>
				</a>
				<a class="btn btn--ghost" href="https://github.com/makitdev" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'GitHub Organization', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8599;</span>
				</a>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();