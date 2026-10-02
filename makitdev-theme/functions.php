<?php
/**
 * makitdev theme setup, enqueueing, and registrations.
 *
 * @package makitdev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme setup.
 */
function makitdev_setup() {
	load_theme_textdomain( 'makitdev', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'makitdev' ),
		)
	);
}
add_action( 'after_setup_theme', 'makitdev_setup' );

/**
 * Enqueue styles and scripts.
 *
 * The theme is self-contained — once installed in wp-content/themes it cannot
 * reach the repository root — so every asset it references must exist under
 * makitdev-theme/assets/. Those files are copies of the canonical sources in
 * assets/, kept in sync by `node scripts/sync-theme-assets.js`.
 *
 * Load order matches the static pages: data, GitHub API helper, renderers,
 * interactions, then the page-specific script.
 */
function makitdev_scripts() {
	$version = wp_get_theme()->get( 'Version' );
	$base    = get_template_directory_uri() . '/assets';

	wp_enqueue_style(
		'makitdev-main',
		$base . '/css/main.min.css',
		array(),
		$version
	);

	wp_enqueue_script(
		'makitdev-github',
		$base . '/js/github.js',
		array(),
		$version,
		true
	);

	wp_enqueue_script(
		'makitdev-community',
		$base . '/js/community.js',
		array( 'makitdev-github' ),
		$version,
		true
	);

	wp_enqueue_script(
		'makitdev-main',
		$base . '/js/main.js',
		array( 'makitdev-community' ),
		$version,
		true
	);

	if ( makitdev_is_contributors_page() ) {
		wp_enqueue_style(
			'makitdev-contributors',
			$base . '/css/contributors.min.css',
			array( 'makitdev-main' ),
			$version
		);

		wp_enqueue_script(
			'makitdev-contributors',
			$base . '/js/contributors.js',
			array( 'makitdev-main' ),
			$version,
			true
		);
	}

	if ( is_front_page() || makitdev_is_contributors_page() ) {
		add_action( 'wp_footer', 'makitdev_roster_data', 5 );
	}
}
add_action( 'wp_enqueue_scripts', 'makitdev_scripts' );

/**
 * True on the contributors page, whether it exists as a page or a template.
 */
function makitdev_is_contributors_page() {
	return is_page_template( 'page-contributors.php' ) || is_page( 'contributors' );
}

/**
 * Read assets/data/contributors.json, the curated roster the static build
 * exposes through assets/data/contributors.js.
 */
function makitdev_read_roster() {
	$file   = get_template_directory() . '/assets/data/contributors.json';
	$roster = array();

	if ( file_exists( $file ) ) {
		$data = json_decode( (string) file_get_contents( $file ), true );
		if ( is_array( $data ) ) {
			$roster = $data;
		}
	}

	return makitdev_unique_contributors( $roster );
}

/**
 * Drop entries without a username and any repeated login, preserving order.
 *
 * The roster is hand-maintained through scripts/add-contributor.js, so this
 * guards against the same person being listed twice.
 */
function makitdev_unique_contributors( array $contributors ) {
	$unique = array();
	$seen   = array();

	foreach ( $contributors as $person ) {
		if ( ! is_array( $person ) || empty( $person['username'] ) ) {
			continue;
		}
		$key = strtolower( $person['username'] );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}
		$seen[ $key ] = true;
		$unique[]     = $person;
	}

	return $unique;
}

/**
 * Expose the roster as window.MDK_CONTRIBUTORS.
 *
 * The static build loads assets/data/contributors.js for this; the theme reads
 * the same file and prints it inline in the footer, before the enqueued
 * scripts run, so contributors.js can merge curated entries with live GitHub
 * data either way.
 */
function makitdev_roster_data() {
	$contributors = makitdev_read_roster();

	if ( empty( $contributors ) ) {
		return;
	}

	$json = wp_json_encode( array_values( $contributors ) );
	if ( ! $json ) {
		return;
	}

	printf(
		"<script id=\"makitdev-contributors-data\">window.MDK_CONTRIBUTORS = %s;</script>\n",
		$json // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- wp_json_encode output.
	);
}

/**
 * Route /contributors automatically to page-contributors.php if not created in WP admin.
 */
function makitdev_contributors_template( $template ) {
	if ( makitdev_is_contributors_page() ) {
		$custom = get_template_directory() . '/page-contributors.php';
		if ( file_exists( $custom ) ) {
			return $custom;
		}
	}
	if ( is_404() ) {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$path        = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );
		if ( 'contributors' === $path ) {
			global $wp_query;
			$wp_query->is_404 = false;
			status_header( 200 );
			$custom = get_template_directory() . '/page-contributors.php';
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}
	}
	return $template;
}
add_filter( 'template_include', 'makitdev_contributors_template' );

/**
 * The homepage metadata short-circuit for WordPress.
 */
function makitdev_document_title_parts( $title ) {
	if ( is_front_page() ) {
		$title['title'] = __( 'Build. Share. Improve.', 'makitdev' );
	}
	return $title;
}
add_filter( 'document_title_parts', 'makitdev_document_title_parts' );