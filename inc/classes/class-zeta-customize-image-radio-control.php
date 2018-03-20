<?php

/**
 * Zeta Customize Image Radio Control
 * 
 * @package Zeta
 * @subpackage Customizer
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Zeta_Customize_Image_Radio_Control' ) ) :
/**
 * Zeta Customize Image Radio Control
 *
 * A customizer control to select radio's through images
 *
 * @since 1.0.0
 */
class Zeta_Customize_Image_Radio_Control extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 * 
	 * @since 1.0.0
	 * @var string
	 */
	public $type = 'zeta_image_radio';

	/**
	 * The default value of the inputs.
	 * 
	 * @since 1.0.0
	 * @var string
	 */
	public $default;

	/**
	 * Enqueue required scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_style( 'zeta_customizer', get_template_directory_uri() . '/assets/css/customizer.css', array(), '20151022' );
	}

	/**
	 * Update the parameters passed to the Javacript via JSON
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();

		// Used to 'connect' the inputs
		$this->json['name'] = '_customize-radio-' . $this->id; 

		$this->json['choices'] = $this->choices;
		$this->json['value'] = $this->value();
		$this->json['link'] = $this->get_link();
	}

	/**
	 * Render the control's content.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() { ?>

		<# if ( ! data.choices ) {
			return;
		} #>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{ data.description }}</span>
		<# } #>

		<div class="choices">
		<# _.each( data.choices, function( choice, value ) { #>
			<input type="radio" id="{{ data.name }}-{{ value }}" value="{{ value }}" name="{{ data.name }}" <# if ( data.value === value ) { #>checked="checked"<# } #> {{{ data.link }}} />
			<label for="{{ data.name }}-{{ value }}">
				<# if ( choice.img ) { #>
					<div style="background-image: url({{ choice.img }});"></div>
				<# } #>
				<span>{{ choice.label || choice }}</span>
			</label>
		<# }); #>
		</div>

		<?php
	}
}

endif; // class_exists
