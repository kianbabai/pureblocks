<?php
/**
 * Plugin Name:       PureBlocks
 * Plugin URI:        https://example.com/
 * Description:       A clean, dependency-free collection of focused Gutenberg blocks.
 * Version:           1.0.0
 * Author:            Your Name
 * Text Domain:       pureblocks
 *
 * @package PureBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the list of block slugs used by this plugin.
 *
 * @return array
 */
function pureblocks_get_block_slugs() {
	return array(
		'carousel',
	);
}

/**
 * Register the PureBlocks block category.
 *
 * @param array   $categories Block categories.
 * @param WP_Post $post       Current post.
 *
 * @return array
 */
function pureblocks_register_block_category( $categories, $post ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	$exists = false;

	foreach ( $categories as $category ) {
		if ( isset( $category['slug'] ) && 'pureblocks' === $category['slug'] ) {
			$exists = true;
			break;
		}
	}

	if ( ! $exists ) {
		$categories[] = array(
			'slug'  => 'pureblocks',
			'title' => __( 'PureBlocks', 'pureblocks' ),
		);
	}

	return $categories;
}

add_filter( 'block_categories_all', 'pureblocks_register_block_category', 10, 2 );

/**
 * Register all PureBlocks blocks.
 *
 * This function:
 * - Registers scripts and styles if present under build/{block}/.
 * - Registers the blocks using their block.json files under src/blocks/{block}/.
 */
function pureblocks_register_blocks() {
	$blocks = pureblocks_get_block_slugs();

	foreach ( $blocks as $block_slug ) {
		$block_dir = __DIR__ . '/src/blocks/' . $block_slug;

		if ( ! file_exists( $block_dir . '/block.json' ) ) {
			continue;
		}

		$script_handle       = null;
		$style_handle        = null;
		$editor_style_handle = null;

		$build_dir = __DIR__ . '/build/' . $block_slug . '/';
		$pluginurl = plugins_url( '', __FILE__ );

		// Editor script (per-block) or fallback to root build/index.js.
		$script_path = $build_dir . 'index.js';
		$script_url  = $pluginurl . '/build/' . $block_slug . '/index.js';
		if ( ! file_exists( $script_path ) ) {
			$script_path = __DIR__ . '/build/index.js';
			$script_url  = $pluginurl . '/build/index.js';
		}
		if ( file_exists( $script_path ) ) {
			$script_handle = 'pureblocks-' . $block_slug . '-editor-script';

			wp_register_script(
				$script_handle,
				$script_url,
				array(
					'jquery',
					'wp-blocks',
					'wp-element',
					'wp-block-editor',
					'wp-components',
					'wp-i18n',
				),
				filemtime( $script_path ),
				true
			);

			// Use the same handle for frontend to initialize blocks that need JS (e.g., carousel).
			$args['script'] = $script_handle;
			$args['view_script'] = $script_handle;
		}

		// Frontend style (per-block style.css or style-index.css; fallback to root).
		$style_path = $build_dir . 'style.css';
		$style_url  = $pluginurl . '/build/' . $block_slug . '/style.css';
		if ( ! file_exists( $style_path ) ) {
			$style_path = $build_dir . 'style-index.css';
			$style_url  = $pluginurl . '/build/' . $block_slug . '/style-index.css';
		}
		if ( ! file_exists( $style_path ) ) {
			$style_path = __DIR__ . '/build/style-index.css';
			$style_url  = $pluginurl . '/build/style-index.css';
		}
		if ( file_exists( $style_path ) ) {
			$style_handle = 'pureblocks-' . $block_slug . '-style';

			wp_register_style(
				$style_handle,
				$style_url,
				array(),
				filemtime( $style_path )
			);
		}

		// Editor style (per-block editor.css or style-index.css; fallback to root).
		$editor_style_path = $build_dir . 'editor.css';
		$editor_style_url  = $pluginurl . '/build/' . $block_slug . '/editor.css';
		if ( ! file_exists( $editor_style_path ) ) {
			$editor_style_path = $build_dir . 'style-index.css';
			$editor_style_url  = $pluginurl . '/build/' . $block_slug . '/style-index.css';
		}
		if ( ! file_exists( $editor_style_path ) ) {
			$editor_style_path = __DIR__ . '/build/style-index.css';
			$editor_style_url  = $pluginurl . '/build/style-index.css';
		}
		if ( file_exists( $editor_style_path ) ) {
			$editor_style_handle = 'pureblocks-' . $block_slug . '-editor-style';

			wp_register_style(
				$editor_style_handle,
				$editor_style_url,
				array( 'wp-edit-blocks' ),
				filemtime( $editor_style_path )
			);
		}

		$args = array(
			'editor_script' => $script_handle,
			'style'         => $style_handle,
			'editor_style'  => $editor_style_handle,
		);

		register_block_type( $block_dir, $args );
	}
}

add_action( 'init', 'pureblocks_register_blocks' );

/**
 * Ensure front-end assets are enqueued when blocks are present.
 */
function pureblocks_enqueue_frontend_assets() {
	if ( is_admin() ) {
		return;
	}

	$slugs = pureblocks_get_block_slugs();

	foreach ( $slugs as $slug ) {
		if ( has_block( 'pureblocks/' . $slug ) ) {
			$script_handle = 'pureblocks-' . $slug . '-editor-script';
			$style_handle  = 'pureblocks-' . $slug . '-style';

			if ( wp_script_is( $script_handle, 'registered' ) ) {
				wp_enqueue_script( $script_handle );
			}

			if ( wp_style_is( $style_handle, 'registered' ) ) {
				wp_enqueue_style( $style_handle );
			}
		}
	}
}

add_action( 'enqueue_block_assets', 'pureblocks_enqueue_frontend_assets' );

/**
 * Fallback: always enqueue carousel assets on the front end to guarantee Slick loads.
 */
function pureblocks_enqueue_carousel_always() {
	if ( is_admin() ) {
		return;
	}

	$slug           = 'carousel';
	$script_handle  = 'pureblocks-' . $slug . '-editor-script';
	$style_handle   = 'pureblocks-' . $slug . '-style';
	$editor_style   = 'pureblocks-' . $slug . '-editor-style';
	$build_dir      = __DIR__ . '/build/' . $slug . '/';
	$pluginurl      = plugins_url( '', __FILE__ );

	// Register on the fly if not registered (safety).
	if ( ! wp_script_is( $script_handle, 'registered' ) ) {
		$script_path = $build_dir . 'index.js';
		if ( file_exists( $script_path ) ) {
			wp_register_script(
				$script_handle,
				$pluginurl . '/build/' . $slug . '/index.js',
				array(
					'jquery',
					'wp-blocks',
					'wp-element',
					'wp-block-editor',
					'wp-components',
					'wp-i18n',
				),
				filemtime( $script_path ),
				true
			);
		}
	}

	wp_enqueue_script( $script_handle );

	// Register styles on the fly if not registered.
	if ( ! wp_style_is( $style_handle, 'registered' ) ) {
		$style_path = $build_dir . 'style-index.css';
		if ( ! file_exists( $style_path ) ) {
			$style_path = $build_dir . 'style.css';
		}
		if ( file_exists( $style_path ) ) {
			wp_register_style(
				$style_handle,
				$pluginurl . '/build/' . $slug . '/' . basename( $style_path ),
				array(),
				filemtime( $style_path )
			);
		}
	}

	wp_enqueue_style( $style_handle );

	if ( ! wp_style_is( $editor_style, 'registered' ) ) {
		$editor_style_path = $build_dir . 'editor.css';
		if ( ! file_exists( $editor_style_path ) ) {
			$editor_style_path = $build_dir . 'style-index.css';
		}
		if ( file_exists( $editor_style_path ) ) {
			wp_register_style(
				$editor_style,
				$pluginurl . '/build/' . $slug . '/' . basename( $editor_style_path ),
				array( 'wp-edit-blocks' ),
				filemtime( $editor_style_path )
			);
		}
	}

	wp_enqueue_style( $editor_style );
}

add_action( 'wp_enqueue_scripts', 'pureblocks_enqueue_carousel_always', 20 );
