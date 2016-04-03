<?php
/**
 * Options
 *
 * The main options page and functions.
 *
 * @package BulkUp
 */

namespace BulkUp;

class Options {

	/* Variables */
	private $options;

	/**
	 * Construct
	 */
	function __construct() {

		// Register settings
		add_action( 'admin_init', [ $this, 'register_options' ] );

	}

	/**
	 * Register Options
	 *
	 * Register all options.
	 *
	 * @access public
	 * @return void
	 */
	public function register_options() {

		// Create option
		if ( ! get_option( 'bulk-up-options' ) ) {
			add_option( 'bulk-up-options' );
		}

		// Get options
		$this->options = get_option( 'bulk-up-options' );

		// Add settings section
		add_settings_section( 'options', 'Plugin Options', [], 'bulk-up-options' );

		// Add settings fields
		add_settings_field( 'post_types', 'Post Types', [ $this, 'add_field' ], 'bulk-up-options', 'options', 'post_types' );
		add_settings_field( 'custom_fields', 'Custom Fields', [ $this, 'add_field' ], 'bulk-up-options', 'options', 'custom_fields' );

		// Register setting
		register_setting( 'bulk-up-options', 'bulk-up-options' );

	}

	/**
	 * Add Field
	 *
	 * Adds the appropriate fields.
	 *
	 * @access public
	 * @return void
	 */
	public function add_field( $type ) {

		switch ( $type ) {

			case 'post_types':
				$this->get_post_types();
				break;

			case 'custom_fields':
				$this->get_custom_fields();
				break;

		}

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

		<div class="bulk-up-options wrap">

			<?php settings_errors(); ?>

			<form action="options.php" method="post" enctype="post">
				<?php
					settings_fields( 'bulk-up-options' );
					do_settings_sections( 'bulk-up-options' );
					submit_button();
				?>
			</form>
		</div>

	<?php }

	/**
	 * Get Post Types
	 *
	 * Create all post type inputs.
	 *
	 * @access private
	 * @return void
	 */
	private function get_post_types() {

		$custom_types = [];
		$types = array_diff( get_post_types(), $this->remove_types() );

		foreach ( $types as $type ) {
			$custom_types[$type] = ucfirst( str_replace( '_', ' ', $type ) );
		}

		foreach ( $custom_types as $value => $label ) :
			$active = ( isset( $this->options['post-types'][$value] ) ) ? true : false; ?>
			<label><span><?php echo $label; ?></span>
				<input type="checkbox" name="bulk-up-options[post-types]<?php echo "[{$value}]"; ?>"
					   value="<?php echo $label; ?>" <?php checked( $active ); ?>>
			</label>
		<?php endforeach;

	}

	/**
	 * Get Custom Fields
	 *
	 * Create all custom fields inputs.
	 *
	 * @access private
	 * @return void
	 */
	private function get_custom_fields() {

		$custom_fields = get_meta_keys();

		foreach ( $custom_fields as $field ) :
			$active = ( isset( $this->options['custom-fields'][$field] ) ) ? true : false; ?>
			<label><?php echo str_replace( '_', ' ', $field ); ?>
				<input type="checkbox" name="bulk-up-options[custom-fields]<?php echo "[{$field}]"; ?>"
					   value="<?php echo $field; ?>" <?php checked( $active ); ?>>
			</label>
		<?php endforeach;

	}

	/**
	 * Remove Types
	 *
	 * Create array of disallowed types.
	 *
	 * @access private
	 * @return array $types
	 */
	private function remove_types() {

		return [
			'attachment',
			'revision',
			'nav_menu_item'
		];

	}

}
