<?php
/**
 * Plugin Name:  Custom Elementor Widgets
 * Description:  My custom Elementor Widgets
 * Version:      1.0.0
 * Author:       Brendo Cruz
 * Author URI:   https://github.com/brendocruz
 * Text Domain:  brendo-cruz-custom-widgets
 */

function custom_elementor_widgets() {
	require_once(__DIR__ . '/includes/widgets-manager.php');	
}

add_action('plugins_loaded', 'custom_elementor_widgets');

