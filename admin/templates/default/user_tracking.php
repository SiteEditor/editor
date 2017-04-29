
<div id="sed_user_tracking_allow" title="<?php echo esc_attr__("Welcome to SiteEditor" , "site-editor") ?>">

    <form action="" class="sed_user_tracking_allow_form" method="post">

        <p><?php echo __("Want to help make Site Editor even more awesome? Allow Site Editor to collect non-sensitive diagnostic data and usage information" , "site-editor"); ?></p>

        <h3><?php echo __("Confirm and enjoy from our advantages:" , "site-editor"); ?></h3>

        <ul>
            <li><?php echo __("24/7 our online support!" , "site-editor"); ?> <a href="#"><?php echo __("More Information" , "site-editor"); ?></a></li>
            <li><?php echo __("Never miss an important update - opt-in to our security and feature updates notifications" , "site-editor"); ?></li>
        </ul>

        <div class="action-bar">

            <div>
                <button type="button" class="sed_user_tracking_allow_action button button-primary" data-value="yes"><?php echo __("Allow && Continue" , "site-editor"); ?></button>
            </div>

            <div>
                <button type="button" class="sed_user_tracking_allow_action button button-default" data-value="skip"><?php echo __("Skip" , "site-editor"); ?></button>
            </div>

        </div>

        <input type="hidden" name="sed_user_tracking_allow_from_admin" id="sed_user_tracking_allow_from_admin" value="">

    </form>

</div>