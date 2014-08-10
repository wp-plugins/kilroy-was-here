<?php
/*
Plugin Name: Kilroy was here
Plugin URI: http://wordpress.org/extend/plugins/kilroy-was-here/
Description: Adds a text tag to the footer of posts & pages
Version: 1.1
Author: Walter Ebert 
Author URI: http://walterebert.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: kilroy-was-here
Domain Path: /languages
*/

class KilroyWasHere {
	/**
	 * Disallow direct access
	 */
	private function __construct() {}

	/**
	 * Create instance
	 */
	public static function init() {
		$instance = new self;
		$instance->load_textdomain();

		add_action( 'admin_init', array( $instance, 'register_settings' ) );
		add_action( 'admin_menu', array( $instance, 'admin_menu' ) );
		add_action( 'wp_footer', array( $instance, 'show' ), 99 );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $instance, 'action_link' ) );

		return $instance;
	}

	/**
	 * Load translations
	 */
	public function load_textdomain() {
		load_textdomain( 'kilroy-was-here', __DIR__ . '/languages/' . get_locale() . '.mo' );
	}

	/**
	 * Add settings
	 */
	public function register_settings() {
		register_setting( 'kilroywashere-settings-group', 'kilroywashere-content' );
	}

	/**
	 * Add administration menu
	 */
	public function admin_menu() {
		add_options_page( __( 'Kilroy was here', 'kilroy-was-here' ), __( 'Kilroy was here', 'kilroy-was-here' ), 'manage_options', basename( __DIR__ ), array( $this, 'options' ) );
	}

	/**
	 * Options form
	 */
	public function options() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Kilroy was here', 'kilroy-was-here' ); ?></h2>
			<form action="options.php" method="post" class="form-table">
				<?php settings_fields( 'kilroywashere-settings-group' ); ?>
				<textarea name="kilroywashere-content" class="code" cols="50" rows="10"><?php echo esc_textarea( get_option( 'kilroywashere-content' ) ); ?></textarea>
				<p class="submit">
					<input name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" type="submit" />
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Add plugins settings link
	 *
	 * @param array $links
	 * @return array
	 */
	public function action_link( $links ) {
		return array_merge(
			array( '<a href="options-general.php?page=' . basename( __DIR__ ) . '">' . __( 'Settings' ) . '</a>' ),
			$links
		);
	}

	/**
	 * Get content
	 * @return string
	 */
	public function get() {
		return (string) get_option( 'kilroywashere-content' );
	}

	/**
	 * Show content
	 */
	public function show() {
		$content = $this->get();
		if ( $content ) {
			echo '<pre id="kilroywashere">' . esc_html( $content ) . '</pre>';
		}
	}

	/**
	 * Run on activation
	 */
	public static function install() {
		add_option( 'kilroywashere-content', "`     ,,,\n     (o o)\n--ooO-(_)-Ooo---" );
	}
}

add_action( 'plugins_loaded', 'KilroyWasHere::init' );
register_activation_hook( __FILE__, 'KilroyWasHere::install' );
