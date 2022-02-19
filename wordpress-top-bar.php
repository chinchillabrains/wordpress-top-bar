<?php
/**
 * Plugin Name: Top Bar for Wordpress
 * Description: Adds a top bar over the header
 * Version: 1.0.0
 * Author: chinchillabrains
 * Requires at least: 5.0
 * Author URI: https://chinchillabrains.com
 * Text Domain: wordpress-top-bar
 * Domain Path: /languages/
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Wordpress_Top_Bar' ) ) {
    define( 'WPTB_TEXTDOMAIN', 'wordpress-top-bar' );
    define( 'WPTB_PREFIX', 'wptb' );
    define( 'WPTB_URL', plugin_dir_url( __FILE__ ) );
    class Wordpress_Top_Bar {

        // Instance of this class.
        protected static $instance = null;

        public function __construct() {
            
            // Load translation files
            // add_action( 'init', array( $this, 'add_translation_files' ) );

            // Admin page
            add_action('admin_menu', array( $this, 'setup_menu' ));


            // Add settings link to plugins page
            add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), array( $this, 'add_settings_link' ) );

            // Register plugin settings fields
            register_setting( WPTB_TEXTDOMAIN . '_settings', WPTB_PREFIX . '-content', array('sanitize_callback' => array( 'Wordpress_Top_Bar', 'sanitize_code' ) ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
            add_action( 'wp_body_open', array( $this, 'render_bar' ) );

        }

        public function render_bar () {
            $bar_content = get_option( WPTB_PREFIX . '-content', '');
            ob_start();
            ?>
                <div id="wptb-bar" class="wptb-bar">
                    <div class="wptb-bar__inner">
                        <?= do_shortcode( $bar_content ) ?>
                    </div>
                </div>
            <?php
            $ret_html = ob_get_clean();
            echo $ret_html;
        }

        public function add_scripts () {
            // wp_enqueue_script( WPTB_PREFIX . '-script', WPTB_URL . 'assets/js/script.js' );
            // wp_enqueue_style( WPTB_PREFIX . '-styles', WPTB_URL . 'assets/css/styles.css' );
        }

        public static function sanitize_code ( $input ) {        
            $sanitized = wp_kses_post( $input );
            if ( isset( $sanitized ) ) {
                return $sanitized;
            }
            
            return '';
        }

        public function add_translation_files () {
            load_plugin_textdomain( WPTB_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }

        public function setup_menu () {
            add_management_page(
                __( 'Top Bar Content', WPTB_TEXTDOMAIN ),
                __( 'Top Bar Content', WPTB_TEXTDOMAIN ),
                'manage_options',
                WPTB_PREFIX . '_settings_page',
                array( $this, 'admin_panel_page' )
            );
        }

        public function admin_panel_page (){
            require_once( __DIR__ . '/wordpress-top-bar.admin.php' );
        }

        public function add_settings_link ( $links ) {
            $links[] = '<a href="' . admin_url( 'tools.php?page=' . WPTB_PREFIX . '_settings_page' ) . '">' . __('Settings') . '</a>';
            return $links;
        }

        // Return an instance of this class.
		public static function get_instance () {
			// If the single instance hasn't been set, set it now.
			if ( self::$instance == null ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

    }

    add_action( 'plugins_loaded', array( 'Wordpress_Top_Bar', 'get_instance' ), 0 );

}
