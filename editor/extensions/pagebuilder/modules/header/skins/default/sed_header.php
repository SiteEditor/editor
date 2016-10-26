<header <?php echo $sed_attrs; ?> sed_role="site-header" class="s-tb-sm module module-header header-default  <?php echo $class;?>">
      <?php echo $content ?>
    <?php if( $sticky ){ ?>
    <div class="init-sticky-header"></div>
    <header class="sticky-header" id="header-sticky">
        <div class="sticky-header-inner" >
            <div class="sticky-header-row sed-row-boxed">
                <div class="logo" id="sticky-logo">

                </div>
                <nav class="nav-holder" id="sticky-nav">

                </nav>
            </div>
        </div>
    </header>
    <?php } ?>
</header>