<?php 
/**
 * Shortcode class
 *
 * @package PSV
 */


/**
* The shortcode class. Loads and registers our plugin's shortcode.
*/
class PSV_Shortcode {

	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 *
	 * @var object
	 */
	protected static $instance = null;



	/**
	 * holds the shortcode attributes
	 * 
	 * @var array
	 */
	public $atts = array();




	/**
	 * holds the options for each split view
	 * 
	 * @var array
	 */
	public $split_view = array();




	/**
	 * holds HTML string for this shortcode
	 * 
	 * @var string
	 */
	public $html = '';
	



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
	 * initiate our class. Gets shortcode atts and if id exists it builds
	 * our object and split view. Ohterwise, it retunrs an error message saying
	 * that the id is required.
	 *
	 * @return string html for the split view or error message
	 */
	public function init( $atts ) {
		
		$this->atts = shortcode_atts( array(
	        'id' => ''
	    ), $atts, 'psview' );

	    // first, check if there is an id
		if ( isset( $this->atts['id'] ) && ! empty( $this->atts['id'] ) ) {
			$this->prepare();
			return $this->output();
		}
		else
			return '<p>You must provide an <code>id</code> in order to properly display a Split View.</p>';
	}



	/**
	 * gets the split view data and builds the view if data not empty
	 */
	protected function prepare() {

		// get the split view 
		$this->split_view = premise_get_value( 'premise_split_view', array( 'context' => 'post', 'id' => (int) $this->atts['id'] ) );

		if ( $this->split_view && ! empty( $this->split_view ) ) {

			$this->left  = ( isset( $this->split_view['left'] ) && ! empty( $this->split_view['left'] ) )   ? $this->split_view['left']  : array();
			$this->right = ( isset( $this->split_view['right'] ) && ! empty( $this->split_view['right'] ) ) ? $this->split_view['right'] : array();

			$this->build();
		}
	}



	/**
	 * Builds the split view
	 * 
	 * @return string html for split view
	 */
	protected function build() {
		$_html = '<div class="psv-compare-wrapper">
			<div class="psv-compare-inner">';
				// Get right and left side views
				foreach( $this->split_view as $side => $view ) {
					// get content if type exists and is not empty 
					if ( isset( $view['type'] ) && ! empty( $view['type'] ) )
						$_html .= $this->get_view( $side );
				}
		$_html .= '</div>
			</div>';

		$this->html = $_html;
	}




	/**
	 * get each view. Left or Right side.
	 * 
	 * @param  string $side left or right. determines which side to get
	 * @return string       html for one side
	 */
	protected function get_view( $side ) {
		
		if ( empty( $side ) || ! is_string( $side ) )
			return false;

		$view = ( 'left' == $side ) ? $this->left : $this->right;

		$handle = '<div class="psv-compare-handle">
			<a href="javascript:;" class="psv-slide-left"><i class="fa fa-caret-left"></i></a>
			<a href="javascript:;" class="psv-slide-right"><i class="fa fa-caret-right"></i></a>
		</div>';

		$_view = '';

		if ( isset( $view['type'] ) && ! empty( $view['type'] ) ) {

			$_view = '<div class="psv-compare-it psv-compare-'.$side;

			$_view .= ( 'right' == $side ) ? ' psv-compare-front" style="background: #FFFFFF;">'.$handle : '">';

			$_view .= '<div class="psv-compare-it-inner">';

			// get the content for each view
			$_view .= '<div class="psv-content">' . $this->content( $view ) . '</div>';

			$_view .= '</div></div>';
		}

		return $_view;
	}




	/**
	 * Get content depending on the type
	 * 
	 * @param  array  $view left or right view data ( type => content )
	 * @return string       html for content
	 */
	protected function content( $view = array() ) {
		$type = isset( $view['type'] ) && ! empty( $view['type'] ) ? $view['type'] : '';
		$cont = isset( $view[$type] ) && ! empty( $view[$type] )   ? $view[$type]  : '';

		switch ( $type ) {
			// Get a post
			case 'Post':
				$_html = $this->post( $cont );
				break;

			// Get a YouTube Video
			case 'YouTube':
				$_html = $this->youtube( $cont );
				break;

			// Get a YouTube Video
			case 'Image':
				$_html = $this->image( $cont );
				break;

			// Get a Shortcode
			case 'Shortcode':
				$_html = do_shortcode( $cont );
				break;
			
			// return empty string as default
			default:
				$_html = '';
				break;
		}

		return $_html;
	}



	/**
	 * returns the content for a post
	 * 
	 * @param  string|int $id id of post to retreive
	 * @return string     html for content. or empty string
	 */
	protected function post( $id = '' ) {
		if ( empty( $id ) || ! is_numeric( $id ) )
			return '';

		$post = (object) get_post( $id );

		if ( $post ) {
			$_html = '<div class="psv-content-post">
				<div class="psv-post-title">
					<h3>'.$post->post_title.'</h3>
				</div>
				<div class="psv-post-content">'.wpautop( wptexturize( $post->post_content ) ).'</div>
			</div>';
		}
		else {
			$_html = '';
		}

		return $_html;
	}




	/**
	 * get a youtube video
	 * 
	 * @param  string $video video id
	 * @return string        html for video
	 */
	protected function youtube( $video = '' ) {
		if ( empty( $video ) || ! is_string( $video ) )
			return '';

		$_html = '<div class="psv-content-video">
			<div class="psv-youtube-video" width="100%" height="100%" data-psv-video="'.$video.'"></div>
		</div>';

		return $_html;
	}




	/**
	 * get an image
	 * 	
	 * @param  string $url url for image
	 * @return string      div with image as background
	 */
	protected function image( $url = '' ) {
		if ( empty( $url ) || ! is_string( $url ) )
			return '';

		$_html = '<div class="psv-content-image" style="background-image: url('.$url.');"></div>';

		return $_html;
	}



	/**
	 * output the shortcode
	 * 
	 * @return string the shortcode's html
	 */
	public function output() {
		if ( '' !== $this->html ) 
			return $this->html;
		return '<p>Looks like there was an issue building the Split View.</p>';
	}

}