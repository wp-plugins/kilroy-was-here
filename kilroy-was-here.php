<?php
/*
Plugin Name: Kilroy was here
Plugin URI: http://wordpress.org/extend/plugins/kilroy-was-here/
Description: Adds a text tag to the footer of posts & pages
Version: 1.0.2
Author: Walter Ebert 
Author URI: http://walterebert.com
License: GPLv2 or later
*/

class kilroywashere
{
	/**
	 * Add hooks
	 */
	public function __construct()
	{
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'wp_footer', array( $this, 'show' ), 99 );
		
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array ( $this, 'action_link' ) );

		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
	}

	/**
	 * Add settings
	 */
	public function register_settings()
	{
		register_setting( 'kilroywashere-settings-group', 'kilroywashere-content' );
	}

	/**
	 * Add administration menu
	 */
	public function admin_menu()
	{
		add_options_page( 'Kilroy was here', 'Kilroy was here', 'administrator', __FILE__, array( $this, 'options' ) );
	}

	/**
	 * Options form
	 */
	public function options()
	{
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32">
				<br>
			</div>
			<h2>Kilroy was here</h2>
			<form action="<?php echo admin_url('options.php'); ?>" method="post">
				<?php settings_fields( 'kilroywashere-settings-group' ); ?>
				<p>
					<textarea name="kilroywashere-content" class="code" cols="50" rows="10"><?php echo esc_html( get_option( 'kilroywashere-content' ) ); ?></textarea>
				</p>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
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
	public function action_link( $links )
	{
		return array_merge(
			$links,
			array( '<a href="options-general.php?page=' . plugin_basename( __FILE__ ) . '">' . __( 'Settings' ) . '</a>' )
		);
	}

	/**
	 * Show content
	 */
	public function show()
	{
		$content = get_option( 'kilroywashere-content' );
		if ( $content ) {
			echo '<pre id="kilroywashere">' . esc_html( $content ) . '</pre>';
		}
	}

	/**
	 * Run on activation
	 */
	public function install()
	{
		add_option( 'kilroywashere-content', "`     ,,,\n     (o o)\n--ooO-(_)-Ooo---" );
	}

	/**
	 * Run on deactivation
	 */
	public function uninstall()
	{
		delete_option( 'kilroywashere-content' );
	}

}

// Create instance
$kilroywashere = new kilroywashere;