<?php

$css .= <<<CSS


	.module.module-button.sed-button .btn.btn-default {
	    color: {$main_text_color};
	    background-color: {$background_color};
	    border-color: {$border_color}
	}

	.module.module-button.sed-button .btn-default:hover,
	.module.module-button.sed-button .btn-default:focus,
	.module.module-button.sed-button .btn-default.focus,
	.module.module-button.sed-button .btn-default:active,
	.module.module-button.sed-button .btn-default.active {
	    color: {$main_text_color};
	    background-color: {$secondary_background_color};
	    border-color: {$border_color}
	}

	.module.module-button.sed-button .btn.btn-default-outline {
	    color: {$main_text_color};
	    background-color: transparent;
	    border-color: {$border_color}
	}

	.module.module-button.sed-button .btn-default-outline:focus,
	.module.module-button.sed-button .btn-default-outline.focus,
	.module.module-button.sed-button .btn-default-outline:hover,
	.module.module-button.sed-button .btn-default-outline:active,
	.module.module-button.sed-button .btn-default-outline.active {
	    color: {$main_text_color};
	    background-color: {$secondary_background_color};
	    border-color: {$border_color}
	}

	.module.module-button.sed-button .btn.btn-primary {
	    color: {$main_bg_text_color};
	    background-color: {$first_main_color};
	    border-color: {$first_main_color}
	}

	.module.module-button.sed-button .btn-primary:hover,
	.module.module-button.sed-button .btn-primary:focus,
	.module.module-button.sed-button .btn-primary.focus,
	.module.module-button.sed-button .btn-primary:active,
	.module.module-button.sed-button .btn-primary.active {
	    color: {$main_bg_text_color};
	    background-color: {$first_main_active_color};
	    border-color: {$first_main_active_color}
	}

	.module.module-button.sed-button .btn.btn-primary-outline {
	    color: {$first_main_color};
	    background-color: transparent;
	    border-color: {$first_main_color}
	}

	.module.module-button.sed-button .btn-primary-outline:focus,
	.module.module-button.sed-button .btn-primary-outline.focus,
	.module.module-button.sed-button .btn-primary-outline:hover,
	.module.module-button.sed-button .btn-primary-outline:active,
	.module.module-button.sed-button .btn-primary-outline.active {
	    color: {$main_bg_text_color};
	    background-color: {$first_main_active_color};
	    border-color: {$first_main_active_color}
	}

	.module.module-button.sed-button .btn.btn-perfect {
	    color: {$second_main_bg_text_color};
	    background-color: {$second_main_color};
	    border-color: {$second_main_color}
	}

	.module.module-button.sed-button .btn-perfect:hover,
	.module.module-button.sed-button .btn-perfect:focus,
	.module.module-button.sed-button .btn-perfect.focus,
	.module.module-button.sed-button .btn-perfect:active,
	.module.module-button.sed-button .btn-perfect.active {
	    color: {$second_main_bg_text_color};
	    background-color: {$second_main_active_color};
	    border-color: {$second_main_active_color}
	}

	.module.module-button.sed-button .btn.btn-perfect-outline {
	    color: {$second_main_color};
	    background-color: transparent;
	    border-color: {$second_main_color}
	}

	.module.module-button.sed-button .btn-perfect-outline:focus,
	.module.module-button.sed-button .btn-perfect-outline.focus, 
	.module.module-button.sed-button .btn-perfect-outline:hover,
	.module.module-button.sed-button .btn-perfect-outline:active,
	.module.module-button.sed-button .btn-perfect-outline.active {
	    color: {$second_main_bg_text_color};
	    background-color: {$second_main_active_color};
	    border-color: {$second_main_active_color}
	}


	.module.module-button.sed-button .btn-link {
	    color: {$main_text_color};
	}

	.module.module-button.sed-button .btn-link:hover,
	.module.module-button.sed-button .btn-link:focus {
	    color: {$first_main_color};
	}

	.module.module-button.sed-button .btn-link[disabled]:hover,
	fieldset[disabled] .btn-link:hover,
	.module.module-button.sed-button .btn-link[disabled]:focus,
	fieldset[disabled] .btn-link:focus {
	    color: {$secondary_text_color};
	}



CSS;
