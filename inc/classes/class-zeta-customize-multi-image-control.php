<?php

/**
 * Zeta Customize Multi Image Control
 * 
 * @package Zeta
 * @subpackage Customizer
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Zeta_Customize_Multi_Image_Control' ) ) :
/**
 * Zeta Customize Multi Image Control
 *
 * A customizer control to select multiple images.
 *
 * @since 1.0.0
 */
class Zeta_Customize_Multi_Image_Control extends WP_Customize_Image_Control {

	/**
	 * The type of customize control being rendered.
	 * 
	 * @since 1.0.0
	 * @var string
	 */
	public $type = 'zeta_multi_image';

	/**
	 * Holds all selected images
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $images = array();

	/**
	 * Minimum width for selectable images.
	 *
	 * @since 1.0.0
	 * @var integer
	 */
	public $min_width = 0;

	/**
	 * Minimum height for selectable images.
	 *
	 * @since 1.0.0
	 * @var integer
	 */
	public $min_height = 0;

	/**
	 * Maximum width for selectable images.
	 *
	 * @since 1.0.0
	 * @var integer
	 */
	public $max_width = 0;

	/**
	 * Maximum height for selectable images.
	 *
	 * @since 1.0.0
	 * @var integer
	 */
	public $max_height = 0;

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Upload_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$this->button_labels = array(
			'select'       => __( 'Select Images', 'zeta' ),
			'change'       => __( 'Change Selection', 'zeta' ),
			'remove'       => __( 'Remove All', 'zeta' ),
			'placeholder'  => __( 'No images selected', 'zeta' ),
			'frame_title'  => __( 'Select Images', 'zeta' ),
			'frame_button' => __( 'Choose Images', 'zeta' ),
		);
	}

	/**
	 * Enqueue styles/scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		parent::enqueue();

		wp_enqueue_script( 'zeta_customize_controls', get_template_directory_uri() . '/js/customize-controls.js', array( 'jquery', 'customize-controls', 'media-models', 'media-views' ), '20150820', true );

		/**
		 * Mimic styles for media handling controls 
		 * 
		 * @see wp-admin/css/customize-controls.css
		 */
		wp_add_inline_style( 'customize-controls', "
			.customize-control-{$this->type} .current { margin: 8px 0; }
			.customize-control-{$this->type} .upload-button { white-space: normal; width: 48%; height: auto; float: " . ( is_rtl() ? 'left' : 'right' ) . "; }
			.customize-control-{$this->type} .current .container { overflow: hidden; -webkit-border-radius: 2px; border: 1px solid #eee; -webkit-border-radius: 2px; border-radius: 2px; min-height: 40px; }
			.customize-control-{$this->type} .placeholder { width: 100%; position: relative; text-align: center; cursor: default; }
			.customize-control-{$this->type} .inner { display: none; position: absolute; width: 100%; color: #555; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; display: block; min-height: 40px; line-height: 20px; top: 10px; }
			.customize-control-{$this->type} .actions { margin-bottom: 0px; }
			.customize-control-{$this->type} .attachment-media-view { width: 24.25%; margin: 0 1% 1% 0; float: left; }
			.customize-control-{$this->type} .attachment-media-view:nth-child(4n) { margin-right: 0; }
			.customize-control-{$this->type} img { -webkit-border-radius: 2px; border-radius: 2px; }
			"
		);
	}

	/**
	 * Refresh the parameters passed to the Javascript via JSON
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Customize_Control::to_json()
	 *
	 * @uses Zeta_Customize_Multi_Image_Control::get_current_images()
	 * @uses wp_prepare_attachment_for_js()
	 */
	public function to_json() {

		// Skip WP_Customize_Media_Control::to_json() since it prevents
		// us from using an array value to return from ::value().
		WP_Customize_Control::to_json();

		// WP_Customize_Media_Control
		$this->json['label'] = html_entity_decode( $this->label, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$this->json['mime_type'] = $this->mime_type;
		$this->json['button_labels'] = $this->button_labels;
		$this->json['canUpload'] = current_user_can( 'upload_files' );

		// Add custom params
		$this->json['minWidth']  = absint( $this->min_width );
		$this->json['minHeight'] = absint( $this->min_height );
		$this->json['maxWidth']  = absint( $this->max_width );
		$this->json['maxHeight'] = absint( $this->max_height );

		$value = $this->get_current_attachments();

		if ( is_object( $this->setting ) && $value ) {
			$attachments = array_filter( array_map( 'wp_prepare_attachment_for_js', $value ) );
			if ( ! empty( $attachments ) ) {
				$this->json['attachments'] = $attachments;
			}
		}
	}

	/**
	 * Render a JS template for the content of the media control.
	 *
	 * @since 1.0.0
	 */
	public function content_template() {
		?>
		<label for="{{ data.settings['default'] }}-button">
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
		</label>

		<# if ( ! _.isEmpty( data.attachments ) ) { #>
			<div class="current">
				<div class="container">

					<# _.each( data.attachments, function( att ) { #>
					<div class="attachment-media-view attachment-media-view-{{ att.type }} {{ att.orientation }}">
						<div class="thumbnail thumbnail-{{ att.type }}">
							<# if ( att.sizes && att.sizes.thumbnail ) { #>
								<img class="attachment-thumb" src="{{ att.sizes.thumbnail.url }}" draggable="false" />
							<# } else { #>
								<img class="attachment-thumb" src="{{ att.sizes.full.url }}" draggable="false" />
							<# } #>
						</div>
					</div>
					<# }); #>
				</div>
			</div>
			<div class="actions">
				<# if ( data.canUpload ) { #>
				<button type="button" class="button remove-button"><?php echo $this->button_labels['remove']; ?></button>
				<button type="button" class="button upload-button" id="{{ data.settings['default'] }}-button"><?php echo $this->button_labels['change']; ?></button>
				<div style="clear:both"></div>
				<# } #>
			</div>
		<# } else { #>
			<div class="current">
				<div class="container">
					<div class="placeholder">
						<div class="inner">
							<span>
								<?php echo $this->button_labels['placeholder']; ?>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="actions">
				<# if ( data.canUpload ) { #>
				<button type="button" class="button upload-button" id="{{ data.settings['default'] }}-button"><?php echo $this->button_labels['select']; ?></button>
				<# } #>
				<div style="clear:both"></div>
			</div>
		<# } #>
		<?php
	}

	/**
	 * Return the currently selected attachment ids
	 *
	 * @since 1.0.0
	 *
	 * @uses attachment_url_to_postid()
	 * @return array Selected attachment ids
	 */
	public function get_current_attachments() {
		$value = $this->value();
		$attachments = array();
		foreach ( (array) $value as $attachment_id ) {
			if ( ! is_numeric( $attachment_id ) ) {
				$attachments[] = attachment_url_to_postid( $attachment_id );
			} else {
				$attachments[] = $attachment_id;
			}
		}

		return $attachments;
	}
}

endif; // class_exists