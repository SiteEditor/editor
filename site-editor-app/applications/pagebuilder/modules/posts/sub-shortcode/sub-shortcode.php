<?php
/******************[sed_posts_wrapper]***********************************/

class PBPostsWrapper extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_posts_wrapper",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostsWrapper;
/******************[sed_post_header]***********************************/

class PBPostHeader extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_header",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostHeader;
/******************[sed_post_content]***********************************/

class PBPostContent extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_content",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostContent;
/******************[sed_post_footer]***********************************/

class PBPostFooter extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_footer",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostFooter;
/******************[sed_post_title]***********************************/

class PBPostTitle extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_title",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostTitle;
/******************[sed_post_thumb]***********************************/

class PBPostThumb extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_thumb",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostThumb;
/******************[sed_post_meta]***********************************/

class PBPostMeta extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_meta",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostMeta;
/******************[sed_post_link_author]***********************************/

class PBPostLinkAuthor extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_link_author",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostLinkAuthor;
/******************[sed_post_date]***********************************/

class PBPostDate extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_date",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostDate;
/******************[sed_post_number_comments]***********************************/

class PBPostNumberComments extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_number_comments",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostNumberComments;
/******************[sed_post_edit_admin]***********************************/

class PBPostEditAdmin extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_edit_admin",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostEditAdmin;
/******************[sed_post_category]***********************************/

class PBPostCategory extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_category",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostCategory;
/******************[sed_post_tags]***********************************/

class PBPostTags extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_tags",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts',
        );

        return $atts;
    }
}
new PBPostTags;