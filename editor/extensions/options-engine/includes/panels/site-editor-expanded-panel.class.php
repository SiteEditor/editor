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

			$classes        = "sed-accordion-header go-panel-element sed-panel-{$this->type} {$atts['class']}";

			$pkey			= "{$this->option_group}_{$this->id}";

			?>

			<div class="accordion-panel-settings">

                <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="<?php echo $this->description;?>"></span>

				<div class="<?php echo $classes;?>" data-panel-id="<?php echo $pkey; ?>" id="sed_pb_<?php echo $pkey; ?>" <?php echo $atts_string;?>>
					<?php echo $this->title;?>
				</div>

				<div id="<?php echo $pkey; ?>_ac_panel" class="sed-accordion-content" data-title="<?php echo $this->title;?>" class="sed-dialog content " >
					<?php $this->render_content( ); ?>
				</div>

			<div>

			<?php

		}

	}
}

$this->register_panel_type( 'expanded' , 'SiteEditorExpandedOptionsPanel' );
