<?php
class Mappress_Template extends Mappress_Obj {

	var $name,
		$label,
		$content,
		$exists,
		$path,
		$standard
		;

	static $tokens;

	function __construct($atts = null) {
		parent::__construct($atts);
	}

	static function register() {
		add_action('wp_ajax_mapp_tpl_get', array(__CLASS__, 'ajax_get'));
		add_action('wp_ajax_mapp_tpl_save', array(__CLASS__, 'ajax_save'));
		add_action('wp_ajax_mapp_tpl_delete', array(__CLASS__, 'ajax_delete'));
		add_filter('mappress_poi_props', array(__CLASS__, 'filter_poi_props'), 0, 3);

		self::$tokens = array(
			'address' => __('address', 'mappress-google-maps-for-wordpress'),
			'body' => __('body', 'mappress-google-maps-for-wordpress'),
			'icon' => __('icon', 'mappress-google-maps-for-wordpress'),
			'thumbnail' => __('thumbnail', 'mappress-google-maps-for-wordpress'),
			'title' => __('title', 'mappress-google-maps-for-wordpress'),
			'url' => __('url', 'mappress-google-maps-for-wordpress'),
			'custom' => __('custom field', 'mappress-google-maps-for-wordpress')
		);
	}

	static function ajax_delete() {
		$name = (isset($_POST['name'])) ? $_POST['name'] : null;
		$filepath = get_stylesheet_directory() . '/' . $name . '.php';

		$result = @unlink($filepath);
		if ($result === false)
			Mappress::ajax_response('Unable to delete');

		Mappress::ajax_response('OK');
	}

	static function ajax_get() {
		$name = (isset($_GET['name'])) ? $_GET['name'] : null;

		$filename = $name . '.php';
		$filepath = get_stylesheet_directory() . '/' . $filename;

		$html = @file_get_contents($filepath);
		$standard = @file_get_contents(Mappress::$basedir . "/templates/$filename");

		if (!$standard)
			Mappress::ajax_response('Invalid template');

		$template = new Mappress_Template(array(
			'name' => $name,
			'content' => ($html) ? $html : $standard,
			'path' => $filepath,
			'standard' => $standard,
			'exists' => ($html) ? true : false
		));

		Mappress::ajax_response('OK', $template);
	}


	static function ajax_save() {
		$name = (isset($_POST['name'])) ? $_POST['name'] : null;
		$content = (isset($_POST['content'])) ? stripslashes($_POST['content']) : null;
		$filepath = get_stylesheet_directory() . '/' . $name . '.php';

		$result = @file_put_contents($filepath, $content);
		if ($result === false)
			Mappress::ajax_response('Unable to save');

		// Return filepath after save
		Mappress::ajax_response('OK', $filepath);
	}

	static function load($footer) {
		if ($footer) {
			add_action('wp_footer', array(__CLASS__, 'print_templates'), -10);
			add_action('admin_footer', array(__CLASS__, 'print_templates'), -10);
		} else {
			self::print_templates();
		}
	}

	static function print_templates() {
		// Parse tokens and print
		$names = array('map-controls', 'map-popup', 'map-loop', 'map-item');
		if (Mappress::$pro)
			$names = array_merge($names, array('mashup-popup', 'mashup-loop', 'mashup-item'));

		foreach($names as $name) {
			$template = self::get_template($name);
			printf("<script type='text/html' id='mapp-tmpl-$name'>%s</script>", $template);
		}
	}

	static function locate_template($template_name) {
		$template_name .= ".php";
		$template_file = locate_template($template_name, false);
		if (!Mappress::$pro || is_admin() || empty($template_file))
			$template_file = Mappress::$basedir . "/templates/$template_name";

		// Template exists, return it
		if (file_exists($template_file))
			return $template_file;

		// Check forms directory
		$template_file = Mappress::$basedir . "/forms/$template_name";
		if (file_exists($template_file))
			return $template_file;

		return null;
	}

	/**
	* Get template.
	*/
	static function get_template($template_name, $args = array()) {
		ob_start();
		foreach($args as $arg => $value)
			$$arg = $value;
		$template_file = self::locate_template($template_name);
		if ($template_file)
			require($template_file);
		$html = ob_get_clean();
		$html = str_replace(array("\r\n", "\t"), array(), $html);  // Strip chars that won't display in html anyway
		return $html;
	}

	static function filter_poi_props($props, $postid) {
		$tokens = self::get_custom_tokens();
		foreach($tokens as $token)
			$props[$token] = get_post_meta($postid, $token, true);
		return $props;
	}

	static function get_custom_tokens() {
		$tokens = array();
		foreach(array('map-popup', 'map-item', 'mashup-item', 'mashup-popup') as $name) {
			$template = self::get_template($name);
			// shortcode: preg_match_all("/\[([^\]]*)\]/", $template, $matches);
			preg_match_all("/{{([\s\S]+?)}}/", $template, $matches);
			if ($matches[1])
				$tokens = array_merge($tokens, $matches[1]);
		}
		// Remove '{', 'poi.', 'poi.props.'
		$tokens = str_replace(array('{', 'poi.', 'props.'), '', $tokens);

		// Remove standard tokens, make a unique list
		$tokens = array_unique(array_diff($tokens, self::$tokens));
		return $tokens;
	}
}
?>