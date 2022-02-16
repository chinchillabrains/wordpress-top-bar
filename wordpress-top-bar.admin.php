<?php

if ( ! defined('ABSPATH') ) {
    die( 'ABSPATH is not defined! "Script didn\' run on Wordpress."' );
}
if ( !is_admin() ) {
    die('Not enough privileges');
}

$bar_content = get_option( WPTB_PREFIX . '-content', '');
?>
<form method="post" action="options.php">
    <?php 
        settings_fields( WPTB_TEXTDOMAIN . '_settings' );
        $settings = array( 'textarea_name' => WPTB_PREFIX . '-content' );
        wp_editor( $bar_content, WPTB_PREFIX . '-content', $settings );
        submit_button();
    ?>
</form>