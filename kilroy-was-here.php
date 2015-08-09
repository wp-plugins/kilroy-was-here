<?php
/**
 * Plugin Name: Kilroy was here
 * Plugin URI: http://wordpress.org/extend/plugins/kilroy-was-here/
 * Description: Adds a text tag to the footer of posts & pages
 * Version: 1.2
 * Author: Walter Ebert
 * Author URI: http://walterebert.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kilroywashere
 * Domain Path: /languages
 *
 * @package Kilroy_was_here
 */

// Deny direct access.
if ( ! function_exists( 'add_filter' ) ) {
	header( 'HTTP/1.1 403 Forbidden' );
	die( esc_html( __( 'Access denied' ) ) );
}

define( 'KILROYWASHERE_BASENAME', plugin_basename( __FILE__ ) );
define( 'KILROYWASHERE_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'KILROYWASHERE_DIR_PATH_BASENAME', basename( KILROYWASHERE_DIR_PATH ) );

/**
 * Plugin class
 */
class KilroyWasHere {
	/**
	 * Plugin text domain
	 * @const TEXT_DOMAIN
	 */
	const TEXT_DOMAIN = 'kilroywashere';

	/**
	 * Register settings group
	 * @const SETTINGS
	 */
	const SETTINGS = 'kilroywashere-settings-group';

	/**
	 * Disallow direct access
	 */
	private function __construct() {}

	/**
	 * Create instance
	 *
	 * @return object
	 */
	public static function init() {
		$instance = new self;
		$instance->load_textdomain();

		add_action( 'admin_init', array( $instance, 'register_settings' ) );
		add_action( 'admin_menu', array( $instance, 'admin_menu' ) );

		if ( $instance->in_head() ) {
			add_action( 'wp_head', array( $instance, 'html_comment' ), $instance::priority() );
		} else {
			add_action( 'wp_footer', array( $instance, 'show' ), $instance::priority() );
		}

		add_filter( 'plugin_action_links_' . KILROYWASHERE_BASENAME, array( $instance, 'action_link' ) );

		return $instance;
	}

	/**
	 * Load translations
	 */
	public function load_textdomain() {
		$mo = KILROYWASHERE_DIR_PATH . 'languages/' . get_locale() . '.mo';
		if ( file_exists( $mo ) ) {
			load_textdomain( self::TEXT_DOMAIN, $mo );
		}
	}

	/**
	 * Add settings
	 */
	public function register_settings() {
		register_setting( self::SETTINGS, 'kilroywashere-in_head' );
		register_setting( self::SETTINGS, 'kilroywashere-html_comment' );
		register_setting( self::SETTINGS, 'kilroywashere-content' );
		register_setting( self::SETTINGS, 'kilroywashere-priority' );
	}

	/**
	 * Add administration menu
	 */
	public function admin_menu() {
		add_options_page( __( 'Kilroy was here', self::TEXT_DOMAIN ), __( 'Kilroy was here', self::TEXT_DOMAIN ), 'manage_options', KILROYWASHERE_DIR_PATH_BASENAME, array( $this, 'options' ) );
	}

	/**
	 * Options form
	 */
	public function options() {
		$content = self::content();
		$priority = self::priority();
		$in_head = get_option( 'kilroywashere-in_head' );
		$html_comment = $in_head || get_option( 'kilroywashere-html_comment' );
		?>
		<div class="wrap">
			<h2><?php echo esc_html( __( 'Kilroy was here', self::TEXT_DOMAIN ) ); ?></h2>
			<form action="options.php" method="post">
				<?php settings_fields( self::SETTINGS ); ?>
				<table class="form-table">
					<tr>
						<th><label for="kilroywashere-content"><?php echo esc_html( __( 'Text', self::TEXT_DOMAIN ) ); ?></label></th>
						<td><textarea name="kilroywashere-content" id="kilroywashere-content" class="code" cols="50" rows="10"><?php echo esc_textarea( $content ); ?></textarea></td>
					</tr>
					<tr>
						<th><label for="kilroywashere-priority"><?php echo esc_html( __( 'Priority', self::TEXT_DOMAIN ) ); ?></label></th>
						<td>
							<input type="number" name="kilroywashere-priority" id="kilroywashere-priority" min="1" value="<?php echo absint( $priority ); ?>" />
							<p class="description" id="kilroywashere-priority-description"><?php echo esc_html( __( 'A lower value means a higher priority. The default priority in WordPress is 10.', self::TEXT_DOMAIN ) ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="kilroywashere-in_head"><?php echo esc_html( __( 'Display in HTML &lt;head&gt;', self::TEXT_DOMAIN ) ); ?></label></th>
						<td><input type="checkbox" name="kilroywashere-in_head" id="kilroywashere-in_head" value="1" <?php echo intval( $in_head ) ? 'checked="checked"' : ''; ?>/></td>
					</tr>
					<tr>
						<th><label for="kilroywashere-html_comment"><?php echo esc_html( __( 'Display as HTML comment', self::TEXT_DOMAIN ) ); ?></label></th>
						<td><input type="checkbox" name="kilroywashere-html_comment" id="kilroywashere-html_comment" value="1" <?php echo intval( $html_comment ) ? 'checked="checked"' : ''; ?>/></td>
					</tr>
				</table>
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
	 * @param array $links Action links.
	 * @return array
	 */
	public function action_link( $links ) {
		return array_merge(
			array( '<a href="options-general.php?page=' . esc_attr( KILROYWASHERE_DIR_PATH_BASENAME ) . '">' . esc_html( __( 'Settings' ) ) . '</a>' ),
			$links
		);
	}

	/**
	 * Get content
	 *
	 * @return string
	 */
	public static function content() {
		return (string) get_option( 'kilroywashere-content' );
	}

	/**
	 * Show content
	 */
	public static function show() {
		if ( get_option( 'kilroywashere-html_comment' ) ) {
			self::html_comment();
		} else {
			$content = self::content();
			if ( $content ) {
				echo '<pre id="kilroywashere">' . esc_html( $content ) . '</pre>';
			}
		}
	}

	/**
	 * Display as HTML comment. Replaces pointy brackets, just to be safe.
	 */
	public static function html_comment() {
		$content = self::content();
		if ( $content ) {
			echo "\n<!--\n" . str_replace( array( '>', '<' ), array( '›', '‹' ), $content ) . "\n-->\n\n";
		}
	}

	/**
	 * Get priority
	 *
	 * @return integer
	 */
	public static function priority() {
		$priority = 99;

		if ( defined( 'KILROYWASHERE_PRIORITY' ) ) {
			return absint( KILROYWASHERE_PRIORITY );
		}

		$option = get_option( 'kilroywashere-priority' );
		if ( is_int( $option ) || ctype_digit( $option ) ) {
			$priority = $option;
		}

		return absint( $priority );
	}

	/**
	 * Run on activation
	 */
	public static function install() {
		// Add Kilroy.
		add_option( 'kilroywashere-content', "`     ,,,\n     (o o)\n--ooO-(_)-Ooo---" );
	}

	/**
	 * Setting if content should be displayed in HTML head
	 *
	 * @return boolean
	 */
	protected function in_head() {
		return (bool) get_option( 'kilroywashere-in_head' );
	}

}

add_action( 'plugins_loaded', 'KilroyWasHere::init' );
register_activation_hook( __FILE__, 'KilroyWasHere::install' );
