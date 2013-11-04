<?php
/**
 * Base class for plugin admin functions.
 */
class IS_Admin {

	/**
	 * @var array All sections
	 */
	var $sections;

	/**
	 * @var array All settings
	 */
	var $settings;
	
	function __construct() {

		$this->sections_init(); 
		$this->settings_init(); 
		
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	/**
	 * Populate $this->sections with all section arguements
	 * 
	 * @return void
	 */
	public function sections_init() {
		$this->sections = array(
			'login' => __( 'Login Info', IS_PLUGIN_SLUG ),
		);
	}

	/**
	 * Populate $this->settings with all settings arguements
	 * 
	 * @return void
	 */
	public function settings_init() {
		$this->settings = array(

			'imap_server' => array(
				'title'       => __( 'IMAP Server', IS_PLUGIN_SLUG ),
				'description' => __( 'For example, <code>imap.gmail.com</code>', IS_PLUGIN_SLUG ),
				'default'     => '',
				'type'        => 'input',
				'section'     => 'login',
			),

			'username' => array(
				'title'       => __( 'Username', IS_PLUGIN_SLUG ),
				'description' => __( 'For example, <code>awesomesauce</code> or <code>awesomesauce@gmail.com</code>', IS_PLUGIN_SLUG ),
				'default'     => '',
				'type'        => 'input',
				'section'     => 'login',
			),

			'password' => array(
				'title'       => __( 'Password', IS_PLUGIN_SLUG ),
				'description' => __( 'Password for you e-mail account.<br/> If using Gmail 2-step verification, use an <a href="https://support.google.com/accounts/answer/185833" target="_blank">application specific password</a>.', IS_PLUGIN_SLUG ),
				'default'     => '',
				'type'        => 'password',
				'section'     => 'login',
			),
		);
	}

	public function admin_menu() {
		
		add_options_page(
			IS_PLUGIN_NAME,                 // Page title
			IS_PLUGIN_NAME,                 // Menu title
			'manage_options',               // Capability
			IS_PLUGIN_SLUG,                 // Menu slug
			array( $this, 'admin_options' ) // Page display callback
		);

	}

	/**
	 * Output the options page view.
	 * 
	 * @return null Outputs views/licenses.php and exits.
	 */
	function admin_options() {
		IS_Inbox_Status::get_template( 'admin-options' );
	}

	/**
	* Register settings
	*/
	public function register_settings() {
		
		register_setting( IS_PLUGIN_SLUG, IS_Inbox_Status::OPTION_KEY, array ( $this, 'validate_settings' ) );
		
		foreach ( $this->sections as $slug => $title ) {
			add_settings_section(
				$slug,
				$title,
				null, // Section display callback
				IS_PLUGIN_SLUG
			);
		}
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}
		
	}

	/**
	 * Create settings field
	 *
	 * @since 1.0
	 */
	public function create_setting( $args = array() ) {
		
		$defaults = array(
			'id'          => 'default_field',
			'title'       => __( 'Default Field', IS_PLUGIN_SLUG ),
			'description' => __( 'Default description.', IS_PLUGIN_SLUG ),
			'default'     => '',
			'type'        => 'text',
			'section'     => 'general',
			'choices'     => array(),
			'class'       => ''
		);
			
		extract( wp_parse_args( $args, $defaults ) );
		
		$field_args = array(
			'type'        => $type,
			'id'          => $id,
			'description' => $description,
			'default'     => $default,
			'choices'     => $choices,
			'label_for'   => $id,
			'class'       => $class
		);
		
		add_settings_field(
			$id,
			$title,
			array( $this, 'display_setting' ),
			IS_Inbox_Status::OPTION_KEY,
			$section,
			$field_args
		);
	}

	/**
	 * Load view for setting, passing arguments
	 */
	public function display_setting( $args = array() ) {
		
		$options = get_option( IS_Inbox_Status::OPTION_KEY );
		
		if ( !isset( $options[$id] ) ) {
			$options[$id] = $default;
		}

		$id = $args['id'];
		$args['option_value'] = $options[ $id ];
		$args['option_name'] = IS_Inbox_Status::OPTION_KEY . '[' . $id . ']';

		$template = 'setting-' . $args['type'];

		IS_Inbox_Status::get_template( $template, $args );
		
	}

	/**
	* Validate settings
	*/
	public function validate_settings( $input ) {
		if ( true ) {
			return $input;
		}

		return false;
	}

}