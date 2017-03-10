<?php
/**
 * An Inner Box panel.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

if ( ! class_exists( 'SiteEditorInnerBoxOptionsPanel' ) ) {

	/**
	 * Expanded Panel.
	 */
	class SiteEditorInnerBoxOptionsPanel extends SiteEditorOptionsPanel {

		/**
		 * The panel type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'inner_box';

        /**
         * Show border box for button
         *
         * @access public
         * @var bool
         */
        public $has_border_box = true;

        /**
         * Go to panel button style
         *
         * @access public
         * @var string
         */
        public $btn_style = "default";

		/**
		 * Go to panel button icon
		 *
		 * @access public
		 * @var string
		 */
		public $icon = "";

		/**
		 * Render the panel container, and then its contents (via `this->render_content()`) in a subclass.
		 *
		 * Panel containers are now rendered in JS by default, see {@see WP_Customize_Panel::print_template()}.
		 *
		 * @since 4.0.0
		 * @access protected
		 */
		protected function render( ) {

			$atts           = $this->input_attrs();

			$atts_string    = $atts["atts"];

			/**
			 * go-panel-element-update Css Class For Update Panel In Js
			 *
			 */
			$classes        = "sed-btn-{$this->btn_style} go-panel-element go-panel-element-update sed-panel-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

            $sed_field_id   = 'sed_pb_' . $pkey;

            $spacing_class  = "";

            if( ! empty( $this->field_spacing ) ){
                $spacing_class = "spacing_{$this->field_spacing}";
            }

			/**
			 * @id : sed-app-panel-<?php echo esc_attr( $this->id ); ?> * required for panel main or container
			 *  element because needed for dependency in js.
			 */
			?>
			<!-- * required for panel & control container because needed for dependency in js -->
			<div class="row_settings">

				<div id="sed-app-panel-<?php echo esc_attr( $this->id ); ?>" class="row_setting_inner sed-app-container-panel <?php echo esc_attr( $spacing_class );?> sed-app-container-panel-<?php echo  esc_attr( $this->type );?> <?php if( $this->has_border_box === true ) echo "row_setting_box";?>">

					<div class="clearfix">

						<button data-related-level-box="<?php echo esc_attr( $pkey ); ?>_level_box" type="button" class="<?php echo esc_attr( $classes );?>" data-panel-id="<?php echo esc_attr( $this->id );?>"  name="<?php echo esc_attr( $sed_field_id );?>"
								id="<?php echo esc_attr( $sed_field_id );?>" <?php echo $atts_string;?>>

							<?php if( ! empty( $this->icon ) ){ ?>
								<span class="sedico fa-lg <?php echo esc_attr( $this->icon );?>"></span>
							<?php } ?>

							<?php echo $this->title;?>

							<span class="sedico sedico-chevron-right sed-arrow-right sedico-lg"></span>

						</button>

                        <?php if( !empty( $this->description ) ): ?>
                            <span class="field_desc flt-help sedico sedico-question sedico-lg " title="<?php echo $this->description;?>"></span>
                        <?php endif; ?>

					</div>

				</div>

			</div>

            <div id="<?php echo esc_attr( $pkey ); ?>_level_box" data-multi-level-box="true" data-title="<?php echo esc_attr( $this->title );?>" class="sed-dialog content " >

                <?php $this->render_content( ); ?>

            </div>

			<?php

		}

	}
}


$this->register_panel_type( 'inner_box' , 'SiteEditorInnerBoxOptionsPanel' );