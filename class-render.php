<?php 
/**
 * The Render Class
 *
 * @package PSV
 */


/**
* This class renders the split view
*/
class PSV_Render_View {
	
	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 *
	 * @var object
	 */
	protected static $instance = null;



	/**
	 * Holds the post id for the split view
	 * 
	 * @var string
	 */
	protected $id = '';
	



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
	 * intentionally left blank
	 */
	function __construct() {}



	/**
	 * Checks that we are in the right post type and displays the view.
	 * 
	 * @return string shortcode html for view
	 */
	public function init( $content ) {
		
		global $post;
		
		if ( 'premise_split_view' == $post->post_type ) {

			$this->id = $post->ID;
			
			return do_shortcode( '[psview id="'.$this->id.'"]' );
		}

		else {
			return $post->post_content;
		}
	}
}

?>