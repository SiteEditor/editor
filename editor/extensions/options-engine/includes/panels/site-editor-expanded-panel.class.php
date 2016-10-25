<?php
/**
 * An expanded panel.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

if ( ! class_exists( 'SiteEditorExpandedOptionsPanel' ) ) {

	/**
	 * Class SiteEditorExpandedOptionsPanel
	 */
	class SiteEditorExpandedOptionsPanel extends SiteEditorOptionsPanel {

		/**
		 * The panel type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'expanded';

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
			$classes        = "sed-accordion-header go-panel-element go-panel-element-update sed-panel-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$spacing_class  = "";

			if( ! empty( $this->field_spacing ) ){
				$spacing_class = "spacing_{$this->field_spacing}";
			}

			/**
			 * @id : sed-app-panel-<?php echo esc_attr( $this->id ); ?> * required for panel main or container
			 *  element because needed for dependency in js.
			 */

			?>
			<!-- * required for panel & control container because needed for dependency in js. -->
			<div class="row_settings">

				<div id="sed-app-panel-<?php echo esc_attr( $this->id ); ?>" class="accordion-panel-settings row_setting_inner sed-app-container-panel <?php echo esc_attr( $spacing_class );?> sed-app-container-panel-<?php echo  esc_attr( $this->type );?>">

					<div class="<?php echo $classes;?>" data-panel-id="<?php echo $pkey; ?>" id="sed_pb_<?php echo $pkey; ?>" <?php echo $atts_string;?>>
						<?php echo $this->title;?>

						<?php if( !empty( $this->description ) ): ?>
							<span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="<?php echo $this->description;?>"></span>
						<?php endif; ?>

					</div>

					<div id="<?php echo $pkey; ?>_ac_panel" class="sed-accordion-content" data-title="<?php echo $this->title;?>" class="sed-dialog content " >
						<?php $this->render_content( ); ?>
					</div>

				</div>

			</div>

			<?php
		}

	}
}

$this->register_panel_type( 'expanded' , 'SiteEditorExpandedOptionsPanel' );
