<?php 
/**
 * Plugin Name: Premise Split View
 * Plugin URI:  https://github.com/PremiseWP/premise-split-view
 * Description: Create Split Views.
 * Version:     0.2.0
 * Author:      Premise WP
 * Author URI:  http://premisewp.com
 * License:     GPL
 *
 * @package PSV
 */



// Block direct access to this file.
defined( 'ABSPATH' ) or die();




/**
 * Define plugin path
 */
define( 'PREMISE_SPLITV_PATH', plugin_dir_path( __FILE__ ) );




/**
 * Define plugin url
 */
define( 'PREMISE_SPLITV_URL', plugin_dir_url( __FILE__ ) );




// Instantiate our main class and setup plugin
// Must use 'plugins_loaded' hook.
add_action( 'plugins_loaded', array( Premise_Split_View::get_instance(), 'setup' ) );

/**
 * Load plugin!
 *
 * This is the plugin's main class.
 */
class Premise_Split_View {


	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 *
	 * @var object
	 */
	protected static $instance = null;




	/**
	 * Plugin url
	 *
	 * @var string
	 */
	public $plugin_url = PREMISE_SPLITV_URL;




	/**
	 * Plugin path
	 *
	 * @var strin
	 */
	public $plugin_path = PREMISE_SPLITV_PATH;



	/**
	 * The arguments used to create our custom post type
	 * 
	 * @var array
	 */
	public $cpt_args = array(
		'name' => array(
		    'post_type_name' => 'premise_split_view',
		    'singular' => 'Split View',
		    'plural' => 'Split Views',
		    'slug' => 'split-view'
		),
		'args' => array(
			'supports' => array( 'title' ),
		),
	);





	/**
	 * Constructor. Intentionally left empty and public.
	 *
	 * @see 	setup()
	 * @since 	1.0
	 */
	public function __construct() {}





	/**
	 * Access this plugin’s working instance
	 *
	 * @since   1.0
	 * @return  object instance of this class
	 */
	public static function get_instance() {
		null === self::$instance and self::$instance = new self;

		return self::$instance;
	}





	/**
	 * Setup Premise
	 *
	 * Does includes and registers hooks.
	 *
	 * @since   1.0
	 */
	public function setup() {
		
		$this->includes();
		
		// Create custom post type for split Views
		if ( class_exists( 'PremiseCPT' ) )
			new PremiseCPT( $this->cpt_args['name'], $this->cpt_args['args'] );

		add_action( 'admin_init', array( $this, 'init_ui' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'fe_scripts' ) );

		add_filter( 'the_content', array( PSV_Render_View::get_instance(), 'init' ) );

		add_shortcode( 'psview', array( PSV_Shortcode::get_instance(), 'init' ) );
	}



	/**
	 * Include all necessary files for our plugin to work properly.
	 */
	public function includes() {

		// Require Premise WP.
		if ( ! class_exists( 'Premise_WP' ) ) {

			// Require Premise WP plugin with the help of TGM Plugin Activation.
			require_once PREMISE_SPLITV_PATH . 'TGM-Plugin-Activation/class-tgm-plugin-activation.php';

			add_action( 'tgmpa_register', array( $this, 'psview_register_required_plugins' ) );
		}

		include 'class-cpt-ui.php';
		include 'class-render.php';
		include 'class-shortcode.php';
	}



	/**
	 * initiate UI
	 */
	public function init_ui() {
		add_action( 'load-post.php', array( PSV_CPT_UI::get_instance(), 'render_ui' ) );
		add_action( 'load-post-new.php', array( PSV_CPT_UI::get_instance(), 'render_ui' ) );
	}




	public function admin_scripts() {
		wp_register_style( 'psv_admin_css', $this->plugin_url . '/css/admin/psv-admin.min.css' );
		wp_enqueue_style( 'psv_admin_css' );

		wp_register_script( 'psv_admin_js', $this->plugin_url . '/js/admin/psv-admin.min.js' );
		wp_enqueue_script( 'psv_admin_js' );
	}




	public function fe_scripts() {
		wp_register_style( 'psv_fe_css', $this->plugin_url . '/css/style.min.css' );
		wp_enqueue_style( 'psv_fe_css' );

		wp_register_script( 'psv_fe_js', $this->plugin_url . '/js/frontend/psv-fe.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'psv_fe_js' );
	}








	/**
	 * Register the required plugins for this theme.
	 *
	 * We register one plugin:
	 * - Premise-WP from a GitHub repository
	 *
	 * @link https://github.com/PremiseWP/Premise-WP
	 */
	function psview_register_required_plugins() {
		/*
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(

			// Include Premise-WP plugin.
			array(
				'name'               => 'Premise-WP', // The plugin name.
				'slug'               => 'Premise-WP', // The plugin slug (typically the folder name).
				'source'             => 'https://github.com/PremiseWP/Premise-WP/archive/master.zip', // The plugin source.
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				// 'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				// 'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
				// 'external_url'       => '', // If set, overrides default API URL and points to an external URL.
				// 'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
			),
		);

		/*
		 * Array of configuration settings.
		 */
		$config = array(
			'id'           => 'psview-tgmpa',         // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'plugins.php',            // Parent menu slug.
			'capability'   => 'install_plugins',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
		);

		tgmpa( $plugins, $config );
	}

}
