<?php
/**
 * Custom post type UI class
 *
 * @package PSV\classes\cpt
 */

/**
 * The admin UI class. Loads our plugin custom post type UI.
 */
class PSV_CPT_UI {

	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 *
	 * @var object
	 */
	protected static $instance = null;



	/**
	 * Holds array of types of content available for a user to insert.
	 *
	 * @var array
	 */
	public $type_options = array(
		'Insert...'              => '',
		'Post / Page'            => 'Post',
		'Shortcode'              => 'Shortcode',
		'YouTube or Vimeo Video' => 'YouTube',
		'Image / Media'          => 'Image',
	);




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
	 * Intentionally left blank
	 */
	function __construct() {}



	/**
	 * Register hooks for metabox and saving fields into post
	 */
	public function render_ui() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post'     , array( $this, 'save' ) );
	}





	/**
	 * Adds the meta box container.
	 *
	 * @param string $post_type Post type.
	 */
	public function add_meta_box( $post_type ) {
		$post_types = array( 'premise_split_view' );

		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box(
				'premise-split-view',
				'Build A Split View',
				array( $this, 'split_view_ui' ),
				$post_type,
				'advanced',
				'high'
			);
		}
	}






	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function split_view_ui( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'premise_split_view', 'premise_split_view_nonce' );
		?>
		<div class="premise-ui-intro">
			<p>Insert the content you would like to display on each side of the Split View.
			<br>To insert this Split View anywhere in your site use the following shortcode <code>[psview id="<?php echo $post->ID; ?>"]</code></p>
		</div>
		<div class="premise-row premise-relative">
			<div class="col2 premise-align-center">
				<div class="psv-cpt-ui psv-ui-left">
					<?php $this->select_type( 'left' ); ?>
				</div>
			</div>

			<div class="psv-ui-separator premise-absolute"></div>

			<div class="col2 premise-align-center">
				<div class="psv-cpt-ui psv-ui-right">
					<?php $this->select_type( 'right' ); ?>
				</div>
			</div>

		</div>
		<div class="premise-ui-color">
			<p>Change the color of the Split View controls.</p>
		</div>
		<div class="premise-row">
			<?php premise_field(
				'wp_color',
				array(
					'default'       => '#1652db', // Default blue.
					'name'          => 'premise_split_view[color]',
					'wrapper_class' => 'span12',
				)
			); ?>
		</div>
		<?php
	}



	/**
	 * Insert the selct type fields
	 *
	 * The first step in creating a split view
	 *
	 * @param  string $side which side fields belong to.
	 * @return string       html for fields for left or right side.
	 */
	public function select_type( $side = 'left' ) {
		premise_field( 'select', array(
			'context' => 'post',
			'name'    => 'premise_split_view['.$side.'][type]',
			'options' => $this->type_options,
		));

		echo '<div class="psv-ui-insert premise-relative">';
			$this->insert_content( $side );
		echo '</div>';
	}




	/**
	 * Insert content fields
	 *
	 * @return string html for insert content sections.
	 */
	public function insert_content( $side = 'left' ) {
		$_types = array(
			'Post'      => 'select',
			'Shortcode' => 'text',
			'YouTube'   => 'video',
			'Image'     => 'wp_media',
		);

		$html = '';

		foreach ( $_types as $k => $v ) {
			$args = array(
				'context' => 'post',
				'name'    => 'premise_split_view['.$side.']['.$k.']',
			);

			if ( 'Post' == $k ) {
				$args['options'] = $this->get_post_options();
			}

			$html .= '<div class="psv-insert-content premise-absolute psv-insert-' . $k;
			$html .= $k == premise_get_value( 'premise_split_view[' . $side . '][type]', 'post' ) ? ' psv-content-active">' : '">';

				$html .= premise_field( $v, $args, false );

			$html .= '</div>';
		}

		echo $html;
	}



	/**
	 * Get a list of all post and pages for our select dropdown
	 *
	 * @return array all posts and pages in array format: post_title => id
	 */
	protected function get_post_options() {
		$_posts = get_posts( array(
			'post_type'     => array( 'post', 'page' ),
			'post_status'   => 'publish',
			'posts_er_page' => -1
		) );

		$options = array();
		$options['Select a Post/Page..'] = '';
		foreach ( $_posts as $k => $v ) {
			$options[ $v->post_title ] = $v->ID;
		}
		return $options;
	}





	/**
	 * Save the meta when the post is saved.
	 *
	 * @todo add validation before saving data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
		if ( ! isset( $_POST['premise_split_view_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['premise_split_view_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'premise_split_view' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( 'premise_split_view' !== $_POST['post_type'] ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$mydata = $_POST['premise_split_view'];

		update_post_meta( $post_id, 'premise_split_view', $mydata );
	}
}