<?php

if (!defined('ABSPATH')) exit;


use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Utils;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

class VideoSlider extends Elementor\Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		wp_register_script('video-slider-handler', plugins_url('../../assets/js/video-slider-handler.js', __FILE__),
			['elementor-frontend', 'elementor-frontend-modules'], '1.0.0', true,
		);
	}

	public function get_name() {
		return 'video-slider';
	}

	public function get_title() {
		return 'Video Slider';
	}

	public function get_icon() {
		return 'eicon-slider-video';
	}

	public function get_categories() {
		return ['general'];
	}

	public function get_keywords() {
		return ['video', 'slider', 'playlist'];
	}

	public function get_style_depends() {
		wp_register_style('video-slider-style', plugins_url('../../assets/css/video-slider-style.css', __FILE__));
		return [ 'video-slider-style', ];
	}

	public function get_script_depends() {
		return ['video-slider-handler'];
	}

	protected function register_controls() {
		// SECTION PLAYLIST CONTENT
		// START SECTION
		$this->start_controls_section('section_playlist_content', [
			'label' => esc_html__('Playlist', 'brendo-cruz-custom-widgets'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		// start repeater -- playlist
		$repeater = new Repeater();
		$repeater->add_control('video_source', [
			'label'   => esc_html__('Source', 'elementor'),
			'type'    => Controls_Manager::SELECT,
			'default' => 'youtube',
			'options' => [
				'youtube' => esc_html__('Youtube', 'elementor'),
				'hosted'  => esc_html__('Self Hosted', 'elementor'),
			],
		]);
		$repeater->add_control('youtube_url', [
			'label'       => esc_html__('Link', 'elementor'),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
			'label_block' => true,
			'condition'   => ['video_source' => 'youtube'], 
		]);
		$repeater->add_control('hosted_source', [
			'label'     => esc_html__('External URL', 'elementor'),
			'type'      => Controls_Manager::SWITCHER,
			'condition' => ['video_source' => 'hosted'],
		]);
		$repeater->add_control('hosted_url', [
			'label'      => esc_html__('Choose File', 'elementor'),
			'type'       => Controls_Manager::MEDIA,
			'media_type' => 'video',
			'condition'  => [
				'video_source'  => 'hosted',
				'hosted_source' => '',
			],
		]);
		$repeater->add_control('external_url', [
			'label'       => esc_html__('Link', 'elementor'),
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
			'condition'   => [
				'video_source'  => 'hosted',
				'hosted_source' => 'yes',
			],
		]);
		$repeater->add_control('get_video_data', [
			'type'      => Controls_Manager::BUTTON,
			'text'      => esc_html__('Get Thumbnail', 'elementor'),
			'event'     => 'customVideoSliderWidget:getVideoUrlThumbnail',
			'condition' => ['video_source' => 'youtube'],
			'separator' => 'after',
		]);
		$repeater->add_control('thumbnail', [
			'label'   => esc_html__('Thumbnail', 'elementor'),
			'type'    => Controls_Manager::MEDIA,
			'default' => [
				'url' => \Elementor\Utils::get_placeholder_image_src(),
			],
			'frontend_available' => true,
		]);
		$repeater->add_control('heading', [
			'label'       => esc_html__('Title & Description', 'elementor'),
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
			'separator'   => 'before',
			'default'     => esc_html__('Your Heading', 'elementor'),
		]);
		$repeater->add_control('description', [
			'label'      => esc_html__('Description', 'elementor'),
			'type'       => Controls_Manager::TEXTAREA,
			'show_label' => false,
			'default'    => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
			'rows'       => 8,
		]);
		$this->add_control('playlist', [
			'label'  => esc_html__('Playlist', 'elementor'),
			'type'   => Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => $this->get_repeater_defaults(),
			'frontend_available' => true,
		]);
		// end repeater -- playlist

		// start heading -- playlist layout heading
		$this->add_control('playlist_layout_heading', [
			'label'     => esc_html__('Layout', 'elementor'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);
		$this->add_responsive_control('number_columns_horizontal', [
			'label'     => esc_html__('Columns', 'jbcs-custom-widgets'),
			'type'      => Controls_Manager::NUMBER,
			'min'       => 1,
			'step'      => 1,
			'default'   => 3,
			'condition' => [
				'tabs_orientation' => 'horizontal',
				'display_playlist_tab' => 'yes',
			],
		]);
		$this->add_responsive_control('number_columns_vertical', [
			'label'     => esc_html__('Columns', 'jbcs-custom-widgets'),
			'type'      => Controls_Manager::NUMBER,
			'min'       => 1,
			'step'      => 1,
			'default'   => 8,
			'condition' => [
				'tabs_orientation' => 'vertical',
				'display_playlist_tab' => 'yes',
			],
		]);
		// end heading

		// start heading
		$this->add_control('playlist_spacing_heading', [
			'label'     => esc_html__('Spacing', 'elementor'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);
		$this->add_responsive_control('playlist_items_spacing_horizontal', [
			'label'      => esc_html__('Horizontal', 'elementor'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .video-slider__playlist' => 'column-gap: {{SIZE}}{{UNIT}}',
			],
		]);
		$this->add_responsive_control('playlist_items_spacing_vertical', [
			'label'      => esc_html__('Vertical', 'elementor'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .video-slider__playlist' => 'row-gap: {{SIZE}}{{UNIT}};',
			],
		]);
		$this->add_responsive_control('playlist_padding', [
			'label'  => esc_html__('Padding', 'elementor'),
			'type'   => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
		// end heading -- playlist layout heading
		$this->end_controls_section();
		// SECTION PLAYLIST CONTENT
		// END SECTION


		// SECTION LAYOUT CONTENT
		// START SECTION
		$this->start_controls_section('section_layout_content', [
			'label' => esc_html__('Layout', 'brendo-cruz-custom-widgets'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]);
		$this->add_responsive_control('display_playlist_tab', [
			'label'     => esc_html__('Show Playlist Tab', 'elementor'),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
		]);
		$this->add_responsive_control('tabs_orientation', [
			'label'   => esc_html__('Orientation', 'jbcs-custom-widgets'),
			'type'    => Controls_Manager::CHOOSE,
			'default' => 'horizontal',
			'options' => [
				'horizontal' => [
					'title' => esc_html__('Horizontal', 'elementor'),
					'icon'  => 'eicon-navigation-horizontal',
				],
				'vertical' => [
					'title' => esc_html__('Vertical', 'elementor'),
					'icon'  => 'eicon-navigation-vertical',
				],
			],
			'condition' => [ 'display_playlist_tab' => 'yes' ],
		]);
		$this->add_responsive_control('tabs_layout_horizontal', [
			'label'     => esc_html__('Order', 'elementor'),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'first',
			'options'   => [
				'first'  => [
					'title' => esc_html__('Left', 'elementor'),
					'icon'  => 'eicon-h-align-left',
				],
				'last' => [
					'title' => esc_html__('Right', 'elementor'),
					'icon'  => 'eicon-h-align-right',
				],
			],
			'condition' => [
				'tabs_orientation' => 'horizontal',
				'display_playlist_tab' => 'yes',
			],
		]);
		$this->add_responsive_control('tabs_layout_vertical', [
			'label'     => esc_html__('Order', 'elementor'),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'first',
			'options'   => [
				'first'  => [
					'title' => esc_html__('Top', 'elementor'),
					'icon'  => 'eicon-v-align-top',
				],
				'last' => [
					'title' => esc_html__('Bottom', 'elementor'),
					'icon'  => 'eicon-v-align-bottom',
				],
			],
			'condition' => [
				'tabs_orientation' => 'vertical',
				'display_playlist_tab' => 'yes',
			],
		]);
		$this->add_responsive_control('tabs_size', [
			'label' => esc_html__('Size', 'elementor'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['%'],
			'condition' => [
				'display_playlist_tab' => 'yes',
				'tabs_orientation' => 'horizontal',
			],
		]);
		$this->end_controls_section();
		// SECTION LAYOUT CONTENT
		// END SECTION


		// SECTION DISPLAY CONTENT
		// START SECTION
		$this->start_controls_section('section_display_content', [
			'label' => esc_html__('Display', 'brendo-cruz-custom-widgets'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]);
		$this->add_responsive_control('display_video_margin', [
			'label'  => esc_html__('Video Margin', 'brendo-cruz-custom-widgets'),
			'type'   => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors' => [
				'{{WRAPPER}} .video-slider__display-item-video-container' =>
					'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
		$this->add_responsive_control('display_items_spacing', [
			'label'      => esc_html__('Spacing', 'elementor'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .video-slider__display-item > *:not(:last-child)' =>
					'display: block; margin-bottom: {{SIZE}}{{UNIT}};',
			],
		]);
		$this->add_responsive_control('display_padding', [
			'label'  => esc_html__('Padding', 'elementor'),
			'type'   => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors' => [
				'{{WRAPPER}} .video-slider__display' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
		/* $this->add_responsive_control('display_size_select', [ */
		/* 	'label'   => esc_html__('Size', 'elementor'), */
		/* 	'type'    => Controls_Manager::SELECT, */
		/* 	'default' => 'default', */
		/* 	'options' => [ */
		/* 		'default' => esc_html__('Default', 'elementor'), */
		/* 		'fixed'    => esc_html__('Fixed', 'elementor'), */
		/* 	] */
		/* ]); */
		/* $this->add_responsive_control('display_size_fixed', [ */
		/* 	'label' => esc_html__('Size', 'elementor'), */
		/* 	'type'  => Controls_Manager::SLIDER, */
		/* ]); */
		$this->end_controls_section();
		// SECTION DISPLAY CONTENT
		// END SECTION


		// SECTION CONTAINER CONTENT
		// START SECTION
		$this->start_controls_section('section_container_content', [
			'label' => esc_html__('Container', 'brendo-cruz-custom-widgets'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]);
		$this->add_responsive_control('container_spacing', [
			'label' => esc_html__('Spacing', 'elementor'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .video-slider__container' =>
					'row-gap: {{SIZE}}{{UNIT}}; column-gap: {{SIZE}}{{UNIT}};',
			],
		]);
		$this->add_responsive_control('container_padding', [
			'label' => esc_html__('Padding', 'elementor'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors' => [
				'{{WRAPPER}} .video-slider' =>
					'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
		$this->end_controls_section();
		// SECTION CONTAINER CONTENT
		// END SECTION


		// SECTION VIDEO CONTENT
		// START SECTION
		$this->start_controls_section('section_video_content', [
			'label' => esc_html__('Video', 'elementor'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]);
		$this->add_responsive_control('video_spacing', [
			'label' => esc_html__('Spacing', 'elementor'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .video-slider__playlist-item-thumbnail-container' =>
					'margin-bottom: {{SIZE}}{{UNIT}}',
			],
		]);
		$this->add_responsive_control('video_padding', [
			'label' => esc_html__('Padding', 'elementor'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item' =>
					'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
		$this->end_controls_section();
		// SECTION VIDEO CONTENT
		// END SECTION



		// SECTION ARROWS CONTENT
		// START SECTION
		$this->start_controls_section('section_arrows_content', [
			'label' => esc_html('Arrows', 'brendo-cruz-custom-widgets'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]);
		$this->add_responsive_control('arrows_display', [
			'label'     => esc_html__('Display', 'elementor'),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
		]);

		/* $this->add_control('arrow_left_heading', [ */
		/* 	'label' => esc_html__('Left', 'elementor'), */
		/* 	'type'  => Controls_Manager::HEADING, */
		/* 	'separator' => 'before', */
		/* ]); */
		/* $this->add_control('arrow_left_size', [ */
		/* 	'label' => esc_html__('Size', 'elementor'), */
		/* 	'type' => Controls_Manager::SLIDER, */
		/* 	'size_units' => ['px'], */
		/* 	'default' => [ */
		/* 		'unit' => 'px', */
		/* 		'size' => 30, */
		/* 	], */
		/* 	'range' => [ */
		/* 		'px' => [ */
		/* 			'min' => 5, */
		/* 			'max' => 250, */
		/* 		], */
		/* 	], */
		/* 	'selectors' => [ */
		/* 		'{{WRAPPER}} .video-slider__arrow--left' => 'font-size: {{SIZE}}{{UNIT}} !important;', */
		/* 	], */
		/* ]); */

		/* $this->add_responsive_control('arrow_left_position', [ */
		/* 	'label' => esc_html__('Position', 'elementor'), */
		/* 	'type' => Controls_Manager::DIMENSIONS, */
		/* 	'size_units' => ['px', 'em', '%', 'rem'], */
		/* 	'default' => [ */
		/* 		'top' => 50, */
		/* 		'left' => 0, */
		/* 		'right' => '', */
		/* 		'bottom' => '', */
		/* 		'unit' => '%', */
		/* 		'isLinked' => false, */
		/* 	], */
		/* 	'selectors' => [ */
		/* 		'{{WRAPPER}} .video-slider__arrow--left' => 'top: {{TOP}}{{UNIT}} !important;', */
		/* 		'{{WRAPPER}} .video-slider__arrow--left' => 'left: {{LEFT}}{{UNIT}} !important;', */
		/* 		'{{WRAPPER}} .video-slider__arrow--left' => 'right: {{RIGHT}}{{UNIT}} !important;', */
		/* 		'{{WRAPPER}} .video-slider__arrow--left' => 'bottom: {{BOTTOM}}{{UNIT}} !important;', */
		/* 	], */
		/* ]); */

		$this->end_controls_section();
		// SECTION ARROWS CONTENT
		// END SECTION



		// SECTION HEADING STYLE
		// START SECTION
		$this->start_controls_section('section_heading_style', [
			'label' => esc_html__('Heading', 'elementor'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);
		$this->add_responsive_control('heading_alignment', [
			'label'     => esc_html__('Alignment', 'elementor'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'left'    => [
					'title' => esc_html__('Left', 'elementor'),
					'icon'  => 'eicon-text-align-left',
				],
				'center'  => [
					'title' => esc_html__('Center', 'elementor'),
					'icon'  => 'eicon-text-align-center',
				],
				'right'   => [
					'title' => esc_html__('Right', 'elementor'),
					'icon'  => 'eicon-text-align-right',
				],
				'justify' => [
					'title' => esc_html__('Justify', 'elementor'),
					'icon'  => 'eicon-text-align-justify',
				],
			],
			'default'   => 'left',
			'selectors' => [
				'{{WRAPPER}} .video-slider__display-item-heading' => 'text-align: {{VALUE}};',
			],
		]);
		$this->add_control('heading_color', [
			'label'     => esc_html__('Color', 'elementor'),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'global'    => [
				'default' => Global_Colors::COLOR_PRIMARY,
			],
			'selectors' => [
				'{{WRAPPER}} .video-slider__display-item-heading' => 'color: {{VALUE}};',
			],
		]);
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'heading_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .video-slider__display-item-heading',
			],
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(), [
				'name'     => 'heading_shadow',
				'selector' => '{{WRAPPER}} .video-slider__display-item-heading',
			],
		);
		$this->add_control('heading_tag', [
			'label'   => esc_html__('HTML Tag', 'elementor'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3',
				'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6',
				'p' => 'p', 'span' => 'span', 
			],
			'default' => 'h2',
		]);
		$this->end_controls_section();
		// SECTION HEADING STYLE
		// END SECTION



		// SECTION DESCRIPTION STYLE
		// START SECTION
		$this->start_controls_section('section_desc_style', [
			'label' => esc_html__('Description', 'elementor'),
			'tab'  => Controls_Manager::TAB_STYLE,
		]);
		$this->add_responsive_control('desc_alignment', [
			'label'     => esc_html__('Alignment', 'elementor'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'left'    => [
					'title' => esc_html__('Left', 'elementor'),
					'icon'  => 'eicon-text-align-left',
				],
				'center'  => [
					'title' => esc_html__('Center', 'elementor'),
					'icon'  => 'eicon-text-align-center',
				],
				'right'   => [
					'title' => esc_html__('Right', 'elementor'),
					'icon'  => 'eicon-text-align-right',
				],
				'justify' => [
					'title' => esc_html__('Justify', 'elementor'),
					'icon'  => 'eicon-text-align-justify',
				],
			],
			'default'   => 'left',
			'selectors' => [
				'{{WRAPPER}} .video-slider__display-item-description' => 'text-align: {{VALUE}};',
			],
		]);
		$this->add_control('desc_color', [
			'label'     => esc_html__('Color', 'elementor'),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'global'    => [
				'default' => Global_Colors::COLOR_PRIMARY,
			],
			'selectors' => [
				'{{WRAPPER}} .video-slider__display-item-description' => 'color: {{VALUE}};',
			],
		]);
		$this->add_control('desc_background', [
			'label' => esc_html__('Background', 'elementor'),
			'type'  => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .video-slider__display-item' => 'background-color: {{VALUE}};',
			],
		]);
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'desc_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .video-slider__display-item-description',
			],
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(), [
				'name'     => 'desc_shadow',
				'selector' => '{{WRAPPER}} .video-slider__display-item-description',
			],
		);
		$this->end_controls_section();
		// SECTION DESC STYLE
		// END SECTION

		// SECTION PLAYLIST STYLE
		// START SECTION
		$this->start_controls_section('section_playlist_style', [
			'label' => esc_html__('Playlist', 'elementor'),
			'tab'  => Controls_Manager::TAB_STYLE,
		]);
		$this->add_control('playlist_background', [
			'label' => esc_html__('Background', 'elementor'),
			'type'  => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist'
					=> 'background-color: {{VALUE}};',
			],
		]);

		$this->end_controls_section();
		// SECTION PLAYLIST STYLE
		// END SECTION


		// SECTION VIDEO STYLE
		// START SECTION
		$this->start_controls_section('section_video_style', [
			'label' => esc_html__('Video', 'brendo-cruz-custom-widgets'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		// start tabs -- playlist tabs
		$this->start_controls_tabs('playlist_tabs');
		// start tab -- normal
		$this->start_controls_tab('playlist_tab_normal', [
			'label' => esc_html__('Normal', 'elementor'),
		]);
		// start controls -- item normal
		$this->add_control('item_normal_heading', [
			'label' => esc_html__('Item', 'elementor'),
			'type'  => Controls_Manager::HEADING,
		]);
		$this->add_control('item_normal_color', [
			'label' => esc_html__('Color', 'elementor'),
			'type'  => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item-title' => 'color: {{VALUE}};',
			],
		]);
		$this->add_control('item_normal_background', [
			'label' => esc_html__('Background', 'elementor'),
			'type'  => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item' => 'background-color: {{VALUE}};',
			],
		]);
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'item_normal_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT
				],
				'selector' => '{{WRAPPER}} .video-slider__playlist-item-title',
			],
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(), [
				'name'     => 'item_normal_shadow',
				'selector' => '{{WRAPPER}} .video-slider__playlist-item-title',
			],
		);
		$this->add_responsive_control('item_normal_alignment', [
			'label'     => esc_html__('Alignment', 'elementor'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'left'    => [
					'title' => esc_html__('Left', 'elementor'),
					'icon'  => 'eicon-text-align-left',
				],
				'center'  => [
					'title' => esc_html__('Center', 'elementor'),
					'icon'  => 'eicon-text-align-center',
				],
				'right'   => [
					'title' => esc_html__('Right', 'elementor'),
					'icon'  => 'eicon-text-align-right',
				],
				'justify' => [
					'title' => esc_html__('Justify', 'elementor'),
					'icon'  => 'eicon-text-align-justify',
				],
			],
			'default'   => 'center',
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item-title' => 'text-align: {{VALUE}};',
			],
		]);
		// end controls -- item normal

		// start controls -- icon normal
		$this->add_control('icon_normal_heading', [
			'label' => esc_html__('Icon', 'elementor'),
			'type'  => Controls_Manager::HEADING,
			'separator' => 'before',
		]);
		$this->add_control('icon_normal', [
			'label' => esc_html__('Icon', 'elementor'),
			'type' => Controls_Manager::ICONS,
			'default' => [
				'value' => 'fas fa-play-circle',
				'library' => 'fa-solid',
			],
			'show_label' => false,
		]);
		$this->add_control('icon_normal_color', [
			'label'     => esc_html__('Color', 'elementor'),
			'type'      => Controls_Manager::COLOR,
			'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item-icon i' => 'color: {{VALUE}};',
				'{{WRAPPER}} .video-slider__playlist-item-icon svg' => 'color: {{VALUE}};',
			],
		]);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(), [
				'name' => 'icon_normal_shadow',
				'selector' => '{{WRAPPER}} .video-slider__playlist-item-icon i',
			],
		);
		$this->add_responsive_control('icon_normal_size', [
			'label' => esc_html__('Size', 'elementor'),
			'type'  => Controls_Manager::SLIDER,
			'default' => [
				'unit' => 'px',
				'size' => 30,
			],
			'range' => [
				'px' => [
					'min' => 5,
					'max' => 250,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item-icon' => 'font-size: {{SIZE}}{{UNIT}} !important;'
			],
			'separator' => 'before',
		]);
		$this->add_responsive_control('icon_normal_rotate', [
			'label' => esc_html__('Rotate', 'elementor'),
			'type'  => Controls_Manager::SLIDER,
			'size_units' => ['deg'],
			'default' => [
				'size' => 0,
				'unit' => 'deg',
			],
			'range' => [
				'deg' => [
					'min' => 0,
					'max' => 360,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item-icon i' => 
				'transform: rotate({{SIZE}}{{UNIT}});',
			],
		]);
		// end controls -- icon normal
		$this->end_controls_tab();
		// end tab -- normal

		// start tab -- active
		$this->start_controls_tab('playlist_tab_active', [
			'label' => esc_html__('Active', 'elementor'),
		]);
		// start controls -- item active
		$this->add_control('item_active_heading', [
			'label' => esc_html__('Item', 'elementor'),
			'type'  => Controls_Manager::HEADING,
		]);
		$this->add_control('item_active_color', [
			'label' => esc_html__('Color', 'elementor'),
			'type'  => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item--active .video-slider__playlist-item-title' => 'color: {{VALUE}};',
			],
		]);
		$this->add_control('item_active_background', [
			'label' => esc_html__('Background', 'elementor'),
			'type'  => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item.video-slider__playlist-item--active'
					=> 'background-color: {{VALUE}};',
			],
		]);
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'item_active_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT
				],
				'selector' => '{{WRAPPER}} .video-slider__playlist-item--active .video-slider__playlist-item-title',
			],
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(), [
				'name'     => 'item_active_shadow',
				'selector' => '{{WRAPPER}} .video-slider__playlist-item--active .video-slider__playlist-item-title',
			],
		);
		$this->add_responsive_control('item_active_alignment', [
			'label'     => esc_html__('Alignment', 'elementor'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'left'    => [
					'title' => esc_html__('Left', 'elementor'),
					'icon'  => 'eicon-text-align-left',
				],
				'center'  => [
					'title' => esc_html__('Center', 'elementor'),
					'icon'  => 'eicon-text-align-center',
				],
				'right'   => [
					'title' => esc_html__('Right', 'elementor'),
					'icon'  => 'eicon-text-align-right',
				],
				'justify' => [
					'title' => esc_html__('Justify', 'elementor'),
					'icon'  => 'eicon-text-align-justify',
				],
			],
			/* 'default'   => 'center', */
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item--active .video-slider__playlist-item-title' => 'text-align: {{VALUE}};',
			],
		]);
		// end controls -- item active

		// start controls -- icon active
		$this->add_control('icon_active_heading', [
			'label' => esc_html__('Icon', 'elementor'),
			'type'  => Controls_Manager::HEADING,
			'separator' => 'before',
		]);
		$this->add_control('icon_active', [
			'label' => esc_html__('Icon', 'elementor'),
			'type' => Controls_Manager::ICONS,
			/* 'default' => [ */
			/* 	'value' => 'fas fa-play-circle', */
			/* 	'library' => 'fa-solid', */
			/* ], */
			'show_label' => false,
		]);
		$this->add_control('icon_active_color', [
			'label'     => esc_html__('Color', 'elementor'),
			'type'      => Controls_Manager::COLOR,
			/* 'default' => Global_Typography::TYPOGRAPHY_PRIMARY, */
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item--active .video-slider__playlist-item-icon i' => 'color: {{VALUE}};',
				'{{WRAPPER}} .video-slider__playlist-item--active .video-slider__playlist-item-icon svg' => 'color: {{VALUE}};',
			],
		]);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(), [
				'name' => 'icon_active_shadow',
				'selector' => '{{WRAPPER}} .video-slider__playlist-item--active .video-slider__playlist-item-icon i',
			],
		);
		$this->add_responsive_control('icon_active_size', [
			'label' => esc_html__('Size', 'elementor'),
			'type'  => Controls_Manager::SLIDER,
			/* 'default' => [ */
			/* 	'unit' => 'px', */
			/* 	'size' => 30, */
			/* ], */
			'range' => [
				'px' => [
					'min' => 5,
					'max' => 250,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item--active .video-slider__playlist-item-icon' => 'font-size: {{SIZE}}{{UNIT}} !important;'
			],
			'separator' => 'before',
		]);
		$this->add_responsive_control('icon_active_rotate', [
			'label' => esc_html__('Rotate', 'elementor'),
			'type'  => Controls_Manager::SLIDER,
			'size_units' => ['deg'],
			'default' => [
				'size' => 0,
				'unit' => 'deg',
			],
			'range' => [
				'deg' => [
					'min' => 0,
					'max' => 360,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .video-slider__playlist-item--active .video-slider__playlist-item-icon i' => 
				'transform: rotate({{SIZE}}deg);',
			],
		]);
		// end controls -- icon active
		$this->end_controls_tab();
		// end tab -- active
		$this->end_controls_tabs();
		// end tabs -- playlist item
		$this->end_controls_section();
		// END SECTION
	}

	protected function get_repeater_defaults() {
		$placeholder_image = Utils::get_placeholder_image_src();
		$lorem = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Placeat dolores cumque consectetur deserunt ab veritatis sapiente, iste temporibus repellendus esse ratione architecto enim, harum eius ipsam rem eos. Nihil, officiis?';
		$video_url = 'https://www.youtube.com/watch?v=XHOmBV4js_E';

		return [
			[
				'video_source' => 'youtube',
				'youtube_url' => $video_url,
				'thumbnail' => $placeholder_image,
				'heading' => 'First Heading',
				'description' => $lorem,
			],
			[
				'video_source' => 'youtube',
				'youtube_url' => $video_url,
				'thumbnail' => $placeholder_image,
				'heading' => 'Second Heading',
				'description' => $lorem,
			],
			[
				'video_source' => 'youtube',
				'youtube_url' => $video_url,
				'thumbnail' => $placeholder_image,
				'heading' => 'Third Heading',
				'description' => $lorem,
			],
		];
	}

	protected function print_video_html($repeater_item) {
		$target = $repeater_item;

		if ($target['video_source'] === 'youtube')
			$this->print_youtube_video_html($target);
		else $this->print_hosted_video_html($target);
	}

	protected function print_youtube_video_html($repeater_item) {
		$target = $repeater_item;

		if (!empty($target['youtube_url']))
			$video_url = $target['youtube_url'];
		else return;

		$video_html = \Elementor\Embed::get_embed_html($video_url, [], []);
		echo $video_html;
	}

	protected function get_video_url($repeater_item) {
		$target = $repeater_item;
		$source = $target['video_source'];
		if ($source !== 'hosted') {
			$video_url = $target[$source . '_url'];
		} else {
			if (empty($target['hosted_source']))
				$video_url = $target['hosted_url']['url'];
			else $video_url = $target['external_url'];
		}

		return [
			'source' => $source,
			'url'    => $video_url,
		];

	}

	protected function print_hosted_video_html($repeater_item) {
		$target = $repeater_item;
		
		if (empty($target['hosted_source']))
			$video_url = $target['hosted_url']['url'];
		else $video_url = $target['external_url'];

		if (empty($video_url)) return;

		?>
		<video src="<?php echo esc_attr($video_url); ?>" controls></video>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$arrows_display = $settings['arrows_display'];
		$left_indicator =  [
			'value' => 'fas fa-chevron-left',
			'library' => 'fa-solid',
		];
		$right_indicator = [
			'value' => 'fas fa-chevron-right',
			'library' => 'fa-solid',
		];

		// TABS SIZE
		if ($settings['display_playlist_tab'] === 'yes') {
			$tabsize = $settings['tabs_size']['size'];
			if ($tabsize === '') $tabsize = '1fr';
			else $tabsize .= '%';

			$main_axis_value = "$tabsize 1fr";
			if ($settings['tabs_orientation'] === 'horizontal') {
				// container style
				$this->add_render_attribute('container_style', 'style', "grid-template-columns: $main_axis_value;");
				$this->add_render_attribute('container_style', 'style', 'grid-template-rows: 100%;');

				$number_columns = $settings['number_columns_horizontal'];
				$this->add_render_attribute('playlist_style', 'style', "grid-template-columns: repeat($number_columns, 1fr);");
				$this->add_render_attribute('playlist_style', 'style', "grid-auto-rows: minmax(min-content, max-content);");
			} else if ($settings['tabs_orientation'] === 'vertical'){
				// container style
				$this->add_render_attribute('container_style', 'style', "grid-template-rows: $main_axis_value;");
				$this->add_render_attribute('container_style', 'style', 'grid-template-columns: 100%;');

				$number_columns = $settings['number_columns_vertical'];
				$this->add_render_attribute('playlist_style', 'style', "grid-template-columns: repeat($number_columns, 1fr);");
				$this->add_render_attribute('playlist_style', 'style', "grid-auto-rows: minmax(min-content, max-content);");
			}
		} else {
			$this->add_render_attribute('container_style', 'style', "grid-template-columns: 100%;");
		}

		$layout_order = $settings['tabs_layout_' . $settings['tabs_orientation']];
		if ($layout_order === 'first') {
			$this->add_render_attribute('display_style', 'style', 'order: 0;');
			$this->add_render_attribute('playlist_style', 'style', 'order: 1;');
		} else if ($layout_order === 'last') {
			$this->add_render_attribute('display_style', 'style', 'order: 1;');
			$this->add_render_attribute('playlist_style', 'style', 'order: 0;');
		}

		?>
		<div class="video-slider" data-index="0">
			<?php if ($arrows_display) { ?>
				<div class="video-slider__arrow video-slider__arrow--left video-slider__arrow--hidden">
					<?php Icons_Manager::render_icon($left_indicator, ['aria-hidden' => 'true']); ?>
				</div>
			<?php } ?>
			<div class="video-slider__container" <?php $this->print_render_attribute_string('container_style'); ?> >
				<div class="video-slider__display" <?php $this->print_render_attribute_string('display_style'); ?> >
					<?php 
					foreach ($settings['playlist'] as $index => $item) {
						// title
						$heading_setting_key = $this->get_repeater_setting_key('heading', 'playlist', $index);
						$this->add_render_attribute($heading_setting_key, 'class', 'video-slider__display-item-heading');
						$this->add_inline_editing_attributes($heading_setting_key);
						// title tag
						$title_tag = $settings['heading_tag'];

						// description
						$desc_setting_key = $this->get_repeater_setting_key('description', 'playlist', $index);
						$this->add_render_attribute($desc_setting_key, 'class', 'video-slider__display-item-description');
						$this->add_inline_editing_attributes($desc_setting_key, 'advanced');

						// DISPLAY ITEM CONTAINER
						$render_attribute = 'display-item-' . $index;
						$this->add_render_attribute($render_attribute, 'class', 'video-slider__display-item');
						// default active item
						if ($index === 0) $this->add_render_attribute($render_attribute, 'class', 'video-slider__display-item--visible');
						else $this->add_render_attribute($render_attribute, 'class', 'video-slider__display-item--hidden');
						// data attributes
						$this->add_render_attribute($render_attribute, 'data-index', $index);
						$video_data = $this->get_video_url($item);
						$this->add_render_attribute($render_attribute, 'data-source', $video_data['source']);
						$this->add_render_attribute($render_attribute, 'data-url', $video_data['url']);
					?>
						<div <?php $this->print_render_attribute_string('display-item-' . $index); ?> >
							<div class="video-slider__display-item-video-container">
								<div class="video-slider__display-item-video">
									<?php $this->print_video_html($item); ?>
								</div>
							</div>
							<<?php echo $title_tag; ?> <?php $this->print_render_attribute_string($heading_setting_key); ?> >
								<?php echo $item['heading']; ?>
							</<?php echo $title_tag; ?>>
							<div <?php $this->print_render_attribute_string($desc_setting_key); ?> >
								<?php echo $item['description']; ?>
							</div>
						</div>
					<?php } ?>
				</div>
				<?php if ($settings['display_playlist_tab'] === 'yes') { ?>
					<div class="video-slider__playlist" <?php $this->print_render_attribute_string('playlist_style'); ?> >
					<?php foreach ($settings['playlist'] as $index => $item) { ?>
						<?php
							// RENDER ATTRIBUTES -- PLAYLIST-ITEM
							$item_attrs = 'playlist-item-' . $index;
							// class attribute
							$this->add_render_attribute($item_attrs, 'class', 'video-slider__playlist-item');
							// active class
							if ($index === 0) {
								$this->add_render_attribute($item_attrs, 'class', 'video-slider__playlist-item--active');
							}
							// data-index attribute
							$this->add_render_attribute($item_attrs, 'data-index', $index);

							// ICON
							if ($index === 0) {
								$icon = $settings['icon_active'];
								if (empty($icon['value'])) { 
									$icon = $settings['icon_normal'];
								}
							} else {
								$icon = $settings['icon_normal'];
							}

							// THUMBNAIL
							// get thumbnail image
							$thumbnail_url = $item['thumbnail']['url'];
							if (empty($thumbnail_url)) $thumbnail_url = Utils::get_placeholder_image_src();
							// thumbnail style attrs
							$thumbnail_attrs = 'thumbnail-image-' . $index;
							$this->add_render_attribute($thumbnail_attrs, 'style', "background-image: url($thumbnail_url);");
							// thumbnail class attrs
							$this->add_render_attribute($thumbnail_attrs, 'class', 'video-slider__playlist-item-thumbnail');
						?>
							<div <?php $this->print_render_attribute_string($item_attrs) ?> >
								<div class="video-slider__playlist-item-thumbnail-container">
									<div <?php $this->print_render_attribute_string($thumbnail_attrs); ?> >
										<div class="video-slider__playlist-item-icon">
											<?php Icons_Manager::render_icon($icon); ?>
										</div>
									</div>
								</div>
								<div class="video-slider__playlist-item-title">
									<?php echo $item['heading']; ?>
								</div>
							</div>
						<?php }?> 
					</div>
				<?php } ?>
			</div>
			<?php if ($arrows_display) { ?>
				<div class="video-slider__arrow video-slider__arrow--right">
					<?php Icons_Manager::render_icon($right_indicator, ['aria-hidden' => 'true']); ?>
				</div>
			<?php } ?>
		</div>
		<?php
	}
}
