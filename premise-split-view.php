<?php
/**
 * Plugin Name: Premise Split View
 * Plugin URI:  https://github.com/PremiseWP/premise-split-view
 * Description: Create Split Views.
 * Version:     1.0.0
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
		    'singular'       => 'Split View',
		    'plural'         => 'Split Views',
		    'slug'           => 'split-view',
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
			require_once PREMISE_SPLITV_PATH . 'includes/class-tgm-plugin-activation.php';

			add_action( 'tgmpa_register', array( $this, 'psview_register_required_plugins' ) );
		}

		include 'classes/class-cpt-ui.php';
		include 'classes/class-render.php';
		include 'classes/class-shortcode.php';
	}



	/**
	 * initiates the UI. registers the custom post type UI
	 *
	 * @return void does not return anything
	 */
	public function init_ui() {
		add_action( 'load-post.php', array( PSV_CPT_UI::get_instance(), 'render_ui' ) );
		add_action( 'load-post-new.php', array( PSV_CPT_UI::get_instance(), 'render_ui' ) );
	}



	/**
	 * Resgisters admin scripts
	 *
	 * @return void does not return anything
	 */
	public function admin_scripts() {
		wp_register_style( 'psv_admin_css', $this->plugin_url . '/css/admin/psv-admin.min.css' );
		wp_enqueue_style( 'psv_admin_css' );

		wp_register_script( 'psv_admin_js', $this->plugin_url . '/js/admin/psv-admin.min.js' );
		wp_enqueue_script( 'psv_admin_js' );
	}



	/**
	 * resgister the front end scripts
	 *
	 * @return void does not return anything
	 */
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

			array(
				'name'               => 'Premise-WP',
				'slug'               => 'Premise-WP',
				'source'             => 'https://github.com/PremiseWP/Premise-WP/archive/master.zip',
				'required'           => true,
				'force_activation'   => false,
			),
		);

		/*
		 * Array of configuration settings.
		 */
		$config = array(
			'id'           => 'psview-tgmpa',
			'default_path' => '',
			'menu'         => 'tgmpa-install-plugins',
			'parent_slug'  => 'plugins.php',
			'capability'   => 'install_plugins',
			'has_notices'  => true,
			'dismissable'  => false,
			'dismiss_msg'  => '',
			'is_automatic' => true,
			'message'      => '',
		);

		tgmpa( $plugins, $config );
	}

}