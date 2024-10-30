<?php

/**
 * Plugin Name:     Interactive Promo
 * Plugin URI:      https://essential-blocks.com
 * Description:     Engage your potential audience with an exciting promo.
 * Version:         1.2.6
 * Author:          WPDeveloper
 * Author URI:      https://wpdeveloper.net
 * License:         GPL-3.0-or-later
 * License URI:     https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:     interactive-promo
 *
 * @package         interactive-promo
 */

/**
 * Registers all block assets so that they can be enqueued through the block editor
 * in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
define( 'INTERACTIVE_PROMO_DIR', dirname( __FILE__ ) );

require_once __DIR__ . '/includes/font-loader.php';
require_once __DIR__ . '/includes/post-meta.php';
require_once __DIR__ . '/includes/admin-enqueue.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/lib/style-handler/style-handler.php';

function create_block_interactive_promo_block_init() {
    define( 'INTERACTIVE_PROMO_BLOCKS_VERSION', "1.2.6" );
    define( 'INTERACTIVE_PROMO_BLOCKS_ADMIN_URL', plugin_dir_url( __FILE__ ) );
    define( 'INTERACTIVE_PROMO_BLOCKS_ADMIN_PATH', dirname( __FILE__ ) );

    $script_asset_path = INTERACTIVE_PROMO_BLOCKS_ADMIN_PATH . "/dist/index.asset.php";
    if ( ! file_exists( $script_asset_path ) ) {
        throw new Error(
            'You need to run `npm start` or `npm run build` for the "interactive-promo/interactive-promo" block first.'
        );
    }
    $index_js         = INTERACTIVE_PROMO_BLOCKS_ADMIN_URL . 'dist/index.js';
    $script_asset     = require $script_asset_path;
    $all_dependencies = array_merge( $script_asset['dependencies'], [
        'wp-blocks',
        'wp-i18n',
        'wp-element',
        'wp-block-editor',
        'interactive-promo-blocks-controls-util',
        'essential-blocks-eb-animation'
    ] );

    wp_register_script(
        'interactive-promo-block-editor-js',
        $index_js,
        $all_dependencies,
        $script_asset['version']
    );

    $load_animation_js = INTERACTIVE_PROMO_BLOCKS_ADMIN_URL . 'assets/js/eb-animation-load.js';
    wp_register_script(
        'essential-blocks-eb-animation',
        $load_animation_js,
        [],
        INTERACTIVE_PROMO_BLOCKS_VERSION,
        true
    );

    $animate_css = INTERACTIVE_PROMO_BLOCKS_ADMIN_URL . 'assets/css/animate.min.css';
    wp_register_style(
        'essential-blocks-animation',
        $animate_css,
        [],
        INTERACTIVE_PROMO_BLOCKS_VERSION
    );

    $hover_style = 'assets/css/hover-effects.css';
    wp_register_style(
        'hover-effects-style',
        plugins_url( $hover_style, __FILE__ ),
        [],
        filemtime( INTERACTIVE_PROMO_BLOCKS_ADMIN_PATH . "/$hover_style" ),
        'all'
    );

    if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'essential-blocks/interactive-promo' ) ) {
        register_block_type(
            Interactive_Promo_Helper::get_block_register_path( 'interactive-promo/interactive-promo', INTERACTIVE_PROMO_BLOCKS_ADMIN_PATH ),
            [
                'editor_script'   => 'interactive-promo-block-editor-js',
                'render_callback' => function ( $attributes, $content ) {
                    if ( ! is_admin() ) {
                        wp_enqueue_style( 'hover-effects-style' );
                        wp_enqueue_style( 'essential-blocks-animation' );
                        wp_enqueue_script( 'essential-blocks-eb-animation' );
                    }
                    return $content;
                }
            ]
        );
    }
}

add_action( 'init', 'create_block_interactive_promo_block_init', 99 );
