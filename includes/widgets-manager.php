<?php 

function register_widgets($widgets_manager) {
	require_once(__DIR__ . '/widgets/video-slider.php');
	$widgets_manager->register(new \VideoSlider());
}

add_action('elementor/widgets/register', 'register_widgets');
