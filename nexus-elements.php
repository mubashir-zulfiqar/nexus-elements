<?php

/**
 * Plugin Name: Nexus Elements
 * Description: A premium collection of custom Elementor widgets.
 * Plugin URI:  https://yourfreelancesite.com/
 * Version:     1.2.2
 * Author:      Mubashir Zulfiqar
 * Text Domain: nexus-elements
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'NEXUS_ELEMENTS_VERSION', '1.2.2' );
define( 'NEXUS_ELEMENTS_MINIMUM_ELEMENTOR_VERSION', '3.0.0' );

/**
 * Show an admin notice if Elementor is not active or is too old.
 */
function nexus_elements_admin_notice_missing_elementor() {
    $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor 3: Required version */
        esc_html__( '"%1$s" requires "%2$s" version %3$s or greater to be installed and activated.', 'nexus-elements' ),
        '<strong>Nexus Elements</strong>',
        '<strong>Elementor</strong>',
        NEXUS_ELEMENTS_MINIMUM_ELEMENTOR_VERSION
    );
    printf( '<div class="notice notice-error"><p>%s</p></div>', $message );
}

/**
 * Check for Elementor before registering anything.
 */
function nexus_elements_init() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'nexus_elements_admin_notice_missing_elementor' );
        return;
    }

    if ( ! version_compare( ELEMENTOR_VERSION, NEXUS_ELEMENTS_MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
        add_action( 'admin_notices', 'nexus_elements_admin_notice_missing_elementor' );
        return;
    }

    add_action( 'elementor/elements/categories_registered', 'nexus_elements_add_category' );
    add_action( 'elementor/widgets/register', 'nexus_elements_register_widgets' );
}

add_action( 'plugins_loaded', 'nexus_elements_init' );

/**
 * Enqueue frontend assets for the Video Library widget (only when addon active).
 */
add_action( 'wp_enqueue_scripts', function () {
    if ( ! defined( 'NEXUS_VIDEO_LIBRARY' ) || ! defined( 'NVL_URL' ) ) return;
    wp_register_style(
        'nvl-widget-css',
        NVL_URL . 'widget/widget.css',
        [],
        NEXUS_VIDEO_LIBRARY
    );
    wp_register_script(
        'nvl-widget-js',
        NVL_URL . 'widget/widget.js',
        [],
        NEXUS_VIDEO_LIBRARY,
        true
    );
} );

/**
 * Register the custom Elementor widget category.
 */
function nexus_elements_add_category( $elements_manager ) {
    $elements_manager->add_category(
        'nexus-category',
        [
            'title' => esc_html__( 'Nexus Elements', 'nexus-elements' ),
            'icon'  => 'fa fa-plug',
        ]
    );
}

/**
 * Require and register widgets.
 */
function nexus_elements_register_widgets( $widgets_manager ) {
    require_once __DIR__ . '/widgets/interactive-switcher.php';
    require_once __DIR__ . '/widgets/nexus-gallery.php';

    $widgets_manager->register( new \Nexus_Interactive_Switcher_Widget() );
    $widgets_manager->register( new \Nexus_Gallery_Widget() );

    // Nexus Video Library widget — only when addon plugin is active
    if ( defined( 'NEXUS_VIDEO_LIBRARY' ) ) {
        require_once WP_PLUGIN_DIR . '/nexus-video-library/widget/widget.php';
        $widgets_manager->register( new \Nexus_Video_Library_Widget() );
    }
}
