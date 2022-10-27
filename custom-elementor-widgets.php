<?php
/**
 *
 * Plugin Name: Custom Elementor Widgets
 * Description: My custom Elementor Widgets
 * Version: 1.0
 * Author: Brendo Cruz
 *
 */

function register_widgets($widgets_manager) {

}

add_action('elementor/widgets/register', 'register_widgets');
