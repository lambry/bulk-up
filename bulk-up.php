<?php
/**
 * Plugin Name: Bulk Up
 * Plugin URI: https://github.com/lambry/bulk-up
 * Description: A simple plugin to bulk update post data.
 * Version: 0.1.0
 * Author: Lambry
 * Author URI: http://lambry.com
 * Requires at least: 4.0
 * Tested up to: 4.3
 */

namespace BulkUp;

if ( ! defined( 'ABSPATH' ) ) exit;

/* Setup Class */
class Init {

    /* Variables */
    private $updater;
    private $options;
    private $url;
    private $page;
    private $view;

    /**
     * Constructor
     */
    public function __construct() {

        if ( ! is_admin() ) return;

        $this->url = admin_url() . 'admin.php?page=bulk-up';
        $this->view = ( isset( $_GET[ 'view' ] ) ) ? $_GET[ 'view' ] : '';

        // Load text domain
        load_plugin_textdomain( 'bulk-up', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        // Add admin menu
        add_action( 'admin_menu', [ $this, 'add_menu' ] );

        // Include assets
        add_action( 'admin_enqueue_scripts', [ $this, 'include_assets' ] );

        // Include classes
        add_action( 'init', [ $this, 'include_classes' ] );

    }

    /**
     * Add Menu
     *
     * Adds the appropriate menu type.
     *
     * @access public
     * @return null
     */
    public function add_menu() {

        $this->page = add_management_page( 'Bulk Up', 'Bulk Up', 'manage_options', 'bulk-up', [ $this, 'register_page' ] );

    }

    /**
     * Include Assets
     *
     * Include the needed assets.
     *
     * @access public
     */
    public function include_assets( $hook ) {

        if ( $this->page !== $hook ) return;

        // Load main css file
        wp_enqueue_style( 'bulk-up-styles', plugin_dir_url( __FILE__ ) . 'assets/styles/admin.css', [], '0.1.0' );

        // Load main js file
        if ( ! $this->view ) {
            wp_enqueue_script( 'bulk-up-scripts', plugin_dir_url( __FILE__ ) . 'assets/scripts/admin.min.js', ['jquery'], '0.1.0', true );
        }

    }

    /**
     * Include Classes
     *
     * Include the needed classes.
     *
     * @access public
     */
    public function include_classes() {

        // Require helpers
        require_once plugin_dir_path( __FILE__ ) . 'includes/helpers.php';

        // Require options
        require_once plugin_dir_path( __FILE__ ) . 'includes/options.php';
        $this->options = new Options;

        // Require updater
        require_once plugin_dir_path( __FILE__ ) . 'includes/updater.php';
        $this->updater = new Updater;

    }

    /**
     * Register Page
     *
     * Register page and contents.
     *
     * @access public
     */
    public function register_page() { ?>

         <div class="wrap bulk-up">
            <h2><?php _e( 'Bulk Up', 'bulk-up' ); ?></h2>
            <p class="menu">
                <a href="<?php echo $this->url; ?>" class="add-new-h2 <?php echo ( ! $this->view ? 'active' : '' ); ?>">
                    <?php _e( 'Bulk Update', 'bulk-up' ); ?>
                </a>
                <a href="<?php echo $this->url . '&view=options'; ?>" class="add-new-h2 <?php echo ( $this->view === 'options' ? 'active' : '' ); ?>">
                    <?php _e( 'Plugin Options' ); ?>
                </a>
            </p>

            <div class="bulk-up view-content">
                <?php
                    if ( $this->view === 'options' ) {
                        $this->options->load_view();
                    } else {
                        $this->updater->load_view();
                    }
                ?>
            </div>
        </div>

    <?php }

}

// Load the plugin
add_action( 'plugins_loaded', function() {
    new Init;
});
