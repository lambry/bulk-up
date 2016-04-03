<?php
/**
 * Updater
 *
 * The main updater page and functions.
 *
 * @package BulkUp
 */

namespace BulkUp;

class Updater {

	/* Variables */
	private $post_types;
	private $custom_fields;
	private $bulk_update = false;

	/**
	 * Construct
	 */
	function __construct() {

		// Get and set options
		$options = get_option( 'bulk-up-options' );
		$this->post_types = ( isset( $options['post-types'] ) ) ? array_keys( $options['post-types'] ) : ['void'];
		$this->custom_fields = ( isset( $options['custom-fields'] ) ) ? $options['custom-fields'] : [];

		// Ajax actions
        add_action( 'wp_ajax_bulk_updater_update', [ $this, 'update_posts' ] );

	}

	/**
	 * Load View
	 *
	 * Display the options view.
	 *
	 * @access public
	 * @return void
	 */
	public function load_view() { ?>

		<div class="bulk-up-form wrap">
			<form class="bulk-update-form">
				<?php wp_nonce_field( 'bulk-up-update' ); ?>
				<ul class="bulk-up-posts">
					<?php $this->get_posts(); ?>
				</ul>
				<?php if ( ! $this->bulk_update ) : ?>
					<h3><?php _e( 'No posts available. Please insure you have selected a post type in the plugins options.', 'bulk-up' ) ?></h3>
				<?php endif; ?>
				<button type="submit" class="button button-primary <?php echo ( ! $this->bulk_update ) ? 'hidden' : ''; ?>">
					<?php _e( 'Save All', 'bulk-up' ); ?>
					<span class="spin-it"></span>
				</button>
				<div class="message"></div>
			</form>
		</div>

	<?php }

	/**
	 * Get Posts
	 *
	 * Get all posts to edit.
	 *
	 * @access private
	 * @return void
	 */
	private function get_posts() {

		global $wpdb;

		$posts = get_posts( [ 'post_type' => $this->post_types, 'posts_per_page' => -1] );

		if ( $posts ) {
			$this->bulk_update = true;
		}

		foreach ( $posts as $post ) : ?>
			<li class="bulk-update-post" data-id="<?php echo $post->ID; ?>">
				<?php $this->post_fields( $post ); ?>
			</li>
		<?php endforeach;

	}

	/**
	 * Post Fields
	 *
	 * Display the post fields.
	 *
	 * @access private
	 * @return void
	 */
	private function post_fields( $post ) { ?>

		<strong class="title"><?php echo $post->post_title ?></strong>

		<?php foreach ( $this->custom_fields as $field ) :

			$value = get_post_meta( $post->ID, $field, true );

			if ( is_array( $value ) || is_object( $value ) ) continue; ?>

			<label for="<?php echo $field; ?>">
				<strong><?php echo str_replace( '_', ' ', $field ); ?>:</strong>
				<input type="text" name="<?php echo $field; ?>" value="<?php echo $value; ?>">
			</label>

		<?php endforeach;

	}

	/**
	 * Update Posts
	 *
	 * Update all posts and meta data.
	 *
	 * @access public
	 * @return array $response
	 */
	public function update_posts() {

		$response = [ 'status' => 'error' ];

		if ( $_POST['action'] !== 'bulk_updater_update' || check_ajax_referer( 'bulk-up-update', 'security', false ) ) {
			$response['message'] = __( 'This doesn\'t seem right.', 'bulk-up' );
      		echo json_encode( $response );
			die();
		}

		if ( ! isset( $_POST['posts'] ) || ! is_array( $_POST['posts'] ) ) {
			$response['message'] = __( 'Please ensure your are updating some meta data.', 'bulk-up' );
      		echo json_encode( $response );
			die();
		}

		foreach ( $_POST['posts'] as $post ) {

			// Get post details
			$post_id = $post['id'];
			unset( $post['id'] );

			// Update meta fields
			foreach ( $post as $key => $value ) {
				if ( $value || get_post_meta( $post_id, $key, true ) ) {
					update_post_meta( $post_id, $key, $value );
				}
			}

		}

		$response['status'] = 'success';
		$response['message'] = __( 'All fields successfully updated!', 'bulk-up' );
		echo json_encode( $response );
		die();

	}

}
