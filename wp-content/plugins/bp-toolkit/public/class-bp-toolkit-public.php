<?php

/**
* The public-facing functionality of the plugin.
*
* @link       https://www.therealbenroberts.com
* @since      1.0.0
*
* @package    BP_Toolkit
* @subpackage BP_Toolkit/public
*/

/**
* The public-facing functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the public-facing stylesheet and JavaScript.
*
* @package    BP_Toolkit
* @subpackage BP_Toolkit/public
* @author     Ben Roberts <me@therealbenroberts.com>
*/
class BP_Toolkit_Public {

	/**
	* The ID of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $bp_toolkit    The ID of this plugin.
	*/
	private $bp_toolkit;

	/**
	* The version of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	* @param      string    $bp_toolkit       The name of the plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $bp_toolkit, $version ) {

		$this->bp_toolkit = $bp_toolkit;
		$this->version = $version;

	}

	/**
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in BP_Toolkit_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The BP_Toolkit_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/

		wp_enqueue_style( $this->bp_toolkit, plugin_dir_url( __FILE__ ) . 'css/bp-toolkit-public.css', array(), $this->version, 'all' );

	}

	/**
	* Register the JavaScript for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in BP_Toolkit_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The BP_Toolkit_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/

		wp_enqueue_script( $this->bp_toolkit, plugin_dir_url( __FILE__ ) . 'js/bp-toolkit-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->bp_toolkit, 'settings', array(
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
			'error'      => __( 'Sorry, something went wrong. Please try again or refresh the page.', 'bp-toolkit' )
		));
	}

	/**
	* Register the styles for the custom CSS service
	*
	* @since    1.0.1
	*/
	public function bptk_add_custom_styles() {

		$options = get_option( 'styling_section' );

		if ( isset( $options[ 'bptk_custom_css' ] ) ) {
			wp_add_inline_style( $this->bp_toolkit, $options[ 'bptk_custom_css' ] );
		}
	}



}
