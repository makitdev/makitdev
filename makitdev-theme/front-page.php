<?php
/**
 * Front page template.
 *
 * Mirrors the repository's index.html section for section. Copy lives here so
 * it stays easy to edit later; when the two diverge, this file wins for the
 * WordPress build and index.html wins for the static build.
 *
 * @package makitdev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$makitdev_github    = 'https://github.com/makitdev';
$makitdev_repo      = 'https://github.com/makitdev/makitdev';
$makitdev_contribs  = home_url( '/contributors/' );
$makitdev_theme_uri = get_template_directory_uri();
?>

<main id="main">

	<!-- ============================ Hero ============================ -->
	<section class="hero" id="home" aria-labelledby="hero-title">

		<div class="hero-media" aria-hidden="true">
			<picture>
				<source media="(max-width: 720px)" srcset="<?php echo esc_url( $makitdev_theme_uri . '/assets/images/hero-bg-mobile.png' ); ?>">
				<img class="hero-img" src="<?php echo esc_url( $makitdev_theme_uri . '/assets/images/hero-bg.png' ); ?>" alt="" width="1920" height="1200" fetchpriority="high" decoding="async">
			</picture>
			<canvas class="hero-canvas"></canvas>
		</div>
		<div class="hero-shade" aria-hidden="true"></div>
		<div class="grain" aria-hidden="true"></div>
		<div class="hero-vignette" aria-hidden="true"></div>

		<div class="container">
			<div class="hero-inner">

				<p class="hero-badge" data-hero style="--hero-delay:60ms">
					<span class="hero-badge-dot" aria-hidden="true"></span>
					<?php esc_html_e( 'Open Source · Built in the open', 'makitdev' ); ?>
				</p>

				<h1 id="hero-title">
					<span class="hero-line"><span style="--line-delay:140ms"><?php esc_html_e( 'Build.', 'makitdev' ); ?></span></span>
					<span class="hero-line"><span style="--line-delay:230ms"><?php esc_html_e( 'Share.', 'makitdev' ); ?></span></span>
					<span class="hero-line"><span style="--line-delay:320ms"><?php esc_html_e( 'Improve', 'makitdev' ); ?><em>.</em></span></span>
				</h1>

				<p class="hero-copy" data-hero style="--hero-delay:520ms">
					<?php esc_html_e( 'Practical open-source software for developers, security researchers, and curious builders.', 'makitdev' ); ?>
				</p>

				<div class="hero-actions" data-hero style="--hero-delay:620ms">
					<a class="btn" href="<?php echo esc_url( $makitdev_github ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Explore GitHub', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8599;</span>
					</a>
					<a class="btn btn--ghost" href="#about">
						<?php esc_html_e( 'Discover makitdev', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8595;</span>
					</a>
				</div>

				<p class="hero-float" data-hero style="--hero-delay:760ms">
					<?php esc_html_e( 'Built by developers, for developers', 'makitdev' ); ?>
				</p>

			</div>
		</div>

		<ul class="hero-metrics" aria-label="<?php esc_attr_e( 'What makitdev is', 'makitdev' ); ?>">
			<li>
				<span class="metric-label"><?php esc_html_e( 'Open Source', 'makitdev' ); ?></span>
				<span class="metric-value"><?php esc_html_e( 'MIT License', 'makitdev' ); ?></span>
			</li>
			<li>
				<span class="metric-label"><?php esc_html_e( 'Developer Tools', 'makitdev' ); ?></span>
				<span class="metric-value"><?php esc_html_e( 'Security', 'makitdev' ); ?></span>
			</li>
			<li>
				<span class="metric-label"><?php esc_html_e( 'Web', 'makitdev' ); ?></span>
				<span class="metric-value"><?php esc_html_e( 'Experiments', 'makitdev' ); ?></span>
			</li>
			<li>
				<span class="metric-label"><?php esc_html_e( 'Community', 'makitdev' ); ?></span>
				<span class="metric-value"><?php esc_html_e( 'Contributions', 'makitdev' ); ?></span>
			</li>
		</ul>
	</section>

	<!-- =========================== 01 About =========================== -->
	<section class="section" id="about" aria-labelledby="about-title">
		<div class="container about-grid">
			<p class="label about-label reveal"><?php esc_html_e( '01 / About', 'makitdev' ); ?></p>
			<div class="about-body">
				<h2 class="section-title reveal" id="about-title" style="--reveal-delay:80ms"><?php esc_html_e( 'What is makitdev?', 'makitdev' ); ?></h2>
				<p class="lede reveal" style="--reveal-delay:150ms">
					<?php esc_html_e( 'makitdev is an open-source organization focused on building practical software and sharing it openly.', 'makitdev' ); ?>
				</p>
				<p class="lede reveal" style="--reveal-delay:220ms">
					<?php esc_html_e( 'We experiment with ideas, build useful tools, and release software that others can inspect, use, modify, and improve.', 'makitdev' ); ?>
				</p>
				<div class="about-note reveal" style="--reveal-delay:300ms">
					<span class="about-note-mark" aria-hidden="true">&ldquo;</span>
					<p>
						<?php esc_html_e( 'make it developers — small tools that solve a real problem, released under an open-source license, maintained in public.', 'makitdev' ); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ====================== 02 What we build ====================== -->
	<section class="section" id="builds" aria-labelledby="builds-title">
		<div class="container">
			<div class="section-head">
				<p class="label reveal"><?php esc_html_e( '02 / What We Build', 'makitdev' ); ?></p>
				<h2 class="section-title reveal" id="builds-title" style="--reveal-delay:80ms"><?php esc_html_e( 'Software for curious minds.', 'makitdev' ); ?></h2>
			</div>

			<ul class="build-grid">
				<?php
				$makitdev_cards = array(
					array(
						'num'  => '01',
						'art'  => 'card-art--tools',
						'title' => __( 'Developer Tools', 'makitdev' ),
						'copy' => __( 'CLI tools, libraries, APIs and utilities.', 'makitdev' ),
					),
					array(
						'num'  => '02',
						'art'  => 'card-art--security',
						'title' => __( 'Security', 'makitdev' ),
						'copy' => __( 'Security tools, analysis utilities and defensive software.', 'makitdev' ),
					),
					array(
						'num'  => '03',
						'art'  => 'card-art--apps',
						'title' => __( 'Applications', 'makitdev' ),
						'copy' => __( 'Useful software built around real-world problems.', 'makitdev' ),
					),
					array(
						'num'  => '04',
						'art'  => 'card-art--lab',
						'title' => __( 'Experiments', 'makitdev' ),
						'copy' => __( 'Prototypes, research and technical experiments.', 'makitdev' ),
					),
				);

				$makitdev_delay = 0;
				foreach ( $makitdev_cards as $makitdev_card ) :
					?>
					<li class="card reveal" tabindex="0" data-glow style="--reveal-delay:<?php echo esc_attr( $makitdev_delay ); ?>ms">
						<span class="card-art <?php echo esc_attr( $makitdev_card['art'] ); ?>" aria-hidden="true"></span>
						<div class="card-top">
							<span class="card-num"><?php echo esc_html( $makitdev_card['num'] ); ?></span>
							<span class="card-arrow" aria-hidden="true">&rarr;</span>
						</div>
						<div class="card-body">
							<h3><?php echo esc_html( $makitdev_card['title'] ); ?></h3>
							<p><?php echo esc_html( $makitdev_card['copy'] ); ?></p>
						</div>
					</li>
					<?php
					$makitdev_delay += 80;
				endforeach;
				?>
			</ul>
		</div>
	</section>

	<!-- ========================= 03 Principles ========================= -->
	<section class="section" id="principles" aria-labelledby="principles-title">
		<div class="container">
			<div class="section-head">
				<p class="label reveal"><?php esc_html_e( '03 / Principles', 'makitdev' ); ?></p>
				<h2 class="section-title reveal" id="principles-title" style="--reveal-delay:80ms"><?php esc_html_e( 'How we build.', 'makitdev' ); ?></h2>
			</div>

			<div class="manifesto">
				<?php
				$makitdev_manifesto = array(
					array(
						'num'   => '01',
						'word'  => __( 'Build', 'makitdev' ),
						'copy'  => __( 'Create software that solves real problems.', 'makitdev' ),
						'delay' => 0,
					),
					array(
						'num'   => '02',
						'word'  => __( 'Share', 'makitdev' ),
						'copy'  => __( 'Keep useful software open and accessible.', 'makitdev' ),
						'delay' => 90,
					),
					array(
						'num'   => '03',
						'word'  => __( 'Improve', 'makitdev' ),
						'copy'  => __( 'Learn from others and make things better together.', 'makitdev' ),
						'delay' => 180,
					),
				);

				foreach ( $makitdev_manifesto as $makitdev_item ) :
					?>
					<article class="manifesto-item reveal" style="--reveal-delay:<?php echo esc_attr( $makitdev_item['delay'] ); ?>ms">
						<p class="manifesto-num"><?php echo esc_html( $makitdev_item['num'] ); ?></p>
						<h3 class="manifesto-word"><?php echo esc_html( $makitdev_item['word'] ); ?></h3>
						<p class="manifesto-copy"><?php echo esc_html( $makitdev_item['copy'] ); ?></p>
					</article>
					<?php
				endforeach;
				?>
			</div>
		</div>
	</section>

	<!-- ==================== 04 Built in the open ==================== -->
	<section class="section" id="open" aria-labelledby="open-title">
		<div class="container center">
			<p class="label label--bare reveal" style="justify-content:center"><?php esc_html_e( '04 / Built In The Open', 'makitdev' ); ?></p>
			<h2 class="section-title reveal" id="open-title" style="--reveal-delay:80ms"><?php esc_html_e( 'Open by default.', 'makitdev' ); ?></h2>
			<p class="statement-copy reveal" style="--reveal-delay:150ms">
				<?php esc_html_e( 'Development happens in public. Source code, documentation, issues, discussions, and contributions are part of the process.', 'makitdev' ); ?>
			</p>

			<ol class="flow" data-flow aria-label="<?php esc_attr_e( 'How work moves through makitdev', 'makitdev' ); ?>">
				<span class="flow-rail" aria-hidden="true"><span class="flow-progress"></span></span>
				<?php
				$makitdev_flow = array(
					array( 'Idea', __( 'A problem worth solving.', 'makitdev' ) ),
					array( 'Build', __( 'Small, readable, tested.', 'makitdev' ) ),
					array( 'Open', __( 'Public source and issues.', 'makitdev' ) ),
					array( 'Share', __( 'Documented and released.', 'makitdev' ) ),
					array( 'Improve', __( 'Feedback becomes patches.', 'makitdev' ) ),
				);

				foreach ( $makitdev_flow as $makitdev_step ) :
					?>
					<li class="flow-step">
						<span class="flow-node" aria-hidden="true"></span>
						<p class="flow-word"><?php echo esc_html( $makitdev_step[0] ); ?></p>
						<p class="flow-note"><?php echo esc_html( $makitdev_step[1] ); ?></p>
					</li>
					<?php
				endforeach;
				?>
			</ol>
		</div>
	</section>

	<!-- ========================= 05 Projects ========================= -->
	<section class="section" id="projects" aria-labelledby="projects-title">
		<div class="container">
			<div class="section-head">
				<p class="label reveal"><?php esc_html_e( '05 / Projects', 'makitdev' ); ?></p>
				<h2 class="section-title reveal" id="projects-title" style="--reveal-delay:80ms"><?php esc_html_e( 'Small software. Useful ideas.', 'makitdev' ); ?></h2>
				<p class="lede reveal" style="--reveal-delay:150ms">
					<?php esc_html_e( 'makitdev releases small tools and components across a few areas. The repositories below are read live from the organization on GitHub.', 'makitdev' ); ?>
				</p>
			</div>

			<div class="eco">
				<?php
				$makitdev_cats = array(
					'01' => __( 'Developer Tools', 'makitdev' ),
					'02' => __( 'Security', 'makitdev' ),
					'03' => __( 'Applications', 'makitdev' ),
					'04' => __( 'Web Infrastructure', 'makitdev' ),
					'05' => __( 'Experiments', 'makitdev' ),
				);
				?>
				<ul class="eco-cats reveal">
					<?php foreach ( $makitdev_cats as $makitdev_num => $makitdev_cat ) : ?>
						<li class="eco-cat">
							<span class="eco-cat-num"><?php echo esc_html( $makitdev_num ); ?></span>
							<?php echo esc_html( $makitdev_cat ); ?>
							<span class="eco-cat-mark" aria-hidden="true"></span>
						</li>
					<?php endforeach; ?>
				</ul>

				<div class="eco-repos reveal" style="--reveal-delay:120ms">
					<ul class="repos" id="repos" aria-label="<?php esc_attr_e( 'Repositories', 'makitdev' ); ?>">
						<li class="people-skeleton" aria-hidden="true" style="grid-column:1/-1"><span></span><span></span><span></span></li>
					</ul>
					<p class="repo-note" id="repo-note"></p>
				</div>
			</div>
		</div>
	</section>

	<!-- ====================== 06 Join the build ====================== -->
	<section class="section" id="join" aria-labelledby="join-title">
		<div class="container center">
			<p class="label label--bare reveal" style="justify-content:center"><?php esc_html_e( '06 / Join The Build', 'makitdev' ); ?></p>
			<h2 class="section-title reveal" id="join-title" style="--reveal-delay:80ms"><?php esc_html_e( 'Build with us.', 'makitdev' ); ?></h2>
			<p class="statement-copy reveal" style="--reveal-delay:150ms">
				<?php esc_html_e( 'Explore the source. Follow the work. Share an idea. Report a problem. Contribute.', 'makitdev' ); ?>
			</p>

			<?php
			$makitdev_actions = array(
				array( 'icon' => '⭐', 'label' => __( 'Star a project', 'makitdev' ), 'text' => __( 'Open the org', 'makitdev' ), 'url' => $makitdev_github ),
				array( 'icon' => '🐛', 'label' => __( 'Report a bug', 'makitdev' ), 'text' => __( 'Open an issue', 'makitdev' ), 'url' => $makitdev_repo . '/issues' ),
				array( 'icon' => '💡', 'label' => __( 'Suggest an improvement', 'makitdev' ), 'text' => __( 'Start a discussion', 'makitdev' ), 'url' => $makitdev_repo . '/discussions' ),
				array( 'icon' => '📝', 'label' => __( 'Improve documentation', 'makitdev' ), 'text' => __( 'Read CONTRIBUTING.md', 'makitdev' ), 'url' => $makitdev_repo . '/blob/main/CONTRIBUTING.md' ),
				array( 'icon' => '🧪', 'label' => __( 'Add tests', 'makitdev' ), 'text' => __( 'Open a pull request', 'makitdev' ), 'url' => $makitdev_repo . '/pulls' ),
				array( 'icon' => '🔧', 'label' => __( 'Fix an issue', 'makitdev' ), 'text' => __( 'Pick something up', 'makitdev' ), 'url' => $makitdev_repo . '/issues' ),
				array( 'icon' => '🤝', 'label' => __( 'Submit a pull request', 'makitdev' ), 'text' => __( 'Open a pull request', 'makitdev' ), 'url' => $makitdev_repo . '/pulls' ),
			);
			?>
			<ul class="actions reveal" style="--reveal-delay:220ms">
				<?php foreach ( $makitdev_actions as $makitdev_action ) : ?>
					<li class="action">
						<span class="action-icon" aria-hidden="true"><?php echo esc_html( $makitdev_action['icon'] ); ?></span>
						<p class="action-label"><?php echo esc_html( $makitdev_action['label'] ); ?></p>
						<a class="action-link tlink" href="<?php echo esc_url( $makitdev_action['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $makitdev_action['text'] ); ?> <span class="arrow" aria-hidden="true">&#8599;</span></a>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="cta-actions reveal" style="--reveal-delay:280ms">
				<a class="btn" href="<?php echo esc_url( $makitdev_github ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Explore makitdev on GitHub', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8599;</span>
				</a>
				<a class="btn btn--ghost" href="<?php echo esc_url( $makitdev_contribs ); ?>">
					<?php esc_html_e( 'View Contributors', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8599;</span>
				</a>
			</div>
		</div>
	</section>

	<!-- ======================= 07 Contributors ======================= -->
	<section class="section section--tight" id="contributors" aria-labelledby="contributors-title">
		<div class="container people">
			<div>
				<p class="label reveal"><?php esc_html_e( '07 / Contributors', 'makitdev' ); ?></p>
				<h2 class="section-title reveal" id="contributors-title" style="--reveal-delay:80ms"><?php esc_html_e( 'Built in public.', 'makitdev' ); ?></h2>
				<p class="statement-copy reveal" style="--reveal-delay:150ms">
					<?php esc_html_e( 'Avatars and commit counts load live from GitHub, so this roster only ever shows real accounts and real contributions.', 'makitdev' ); ?>
				</p>
				<div class="people-meta reveal" style="--reveal-delay:220ms">
					<p class="people-count" id="people-count">&nbsp;</p>
					<a class="tlink" href="<?php echo esc_url( $makitdev_contribs ); ?>"><?php esc_html_e( 'Become a contributor', 'makitdev' ); ?> <span class="arrow" aria-hidden="true">&#8599;</span></a>
				</div>
			</div>
			<div>
				<ul class="people-avatars reveal" id="people-avatars" aria-label="<?php esc_attr_e( 'Contributors', 'makitdev' ); ?>">
					<li class="people-skeleton" aria-hidden="true"><span></span></li>
					<li class="people-skeleton" aria-hidden="true"><span></span></li>
					<li class="people-skeleton" aria-hidden="true"><span></span></li>
				</ul>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();