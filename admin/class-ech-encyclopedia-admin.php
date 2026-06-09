<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://127.0.0.1
 * @since      1.0.0
 *
 * @package    Ech_Encyclopedia
 * @subpackage Ech_Encyclopedia/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ech_Encyclopedia
 * @subpackage Ech_Encyclopedia/admin
 * @author     Rowan Chang <rowanchang@prohaba.com>
 */
class Ech_Encyclopedia_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ech_Encyclopedia_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ech_Encyclopedia_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if (isset($_GET['page']) && $_GET['page'] == 'reg_ech_encyclopedia_general_settings') {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ech-encyclopedia-admin.css', array(), $this->version, 'all');
        }

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ech_Encyclopedia_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ech_Encyclopedia_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        if (isset($_GET['page']) && $_GET['page'] == 'reg_ech_encyclopedia_general_settings') {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ech-encyclopedia-admin.js', array( 'jquery' ), $this->version, false);
        }

    }

    /**
     *  ^^^ Add ECH Encyclopedia admin menu
     */
    public function ech_encyclopedia_admin_menu()
    {
			if (isset($_GET['page']) && $_GET['page'] == 'reg_ech_encyclopedia_general_settings') {
        add_menu_page('ECH Encyclopedia Settings', 'ECH Encyclopedia', 'manage_options', 'reg_ech_encyclopedia_general_settings', array($this, 'ech_encyclopedia_admin_page'), 'dashicons-book-alt', 110);
			}
    }
    // return view
    public function ech_encyclopedia_admin_page()
    {
        require_once('partials/ech-encyclopedia-admin-display.php');
    }
    public function reg_ech_encyclopedia_general_settings()
    {
        // Register all settings for general setting page
        register_setting('encyclopedia_gen_settings', 'ech_encyclopedia_domain_url');
        register_setting('encyclopedia_gen_settings', 'ech_encyclopedia_access_token');
        register_setting('encyclopedia_gen_settings', 'ech_encyclopedia_ppp');
    }
}
