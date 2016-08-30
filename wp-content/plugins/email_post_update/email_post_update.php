<?php
/*
Plugin Name: Email  When Post Page Change
Description: Email  When Post Page Change
Plugin URI: postgrid.wdr.tw
Version: 1.0.0
Author: Mike Hsu
Author URI: http://postgrid.wdr.tw
*/





require_once dirname( __FILE__ ) . '/class.email-post-update.php';
add_action( 'init', array( 'Email_Post_update', 'init' ) );








// create custom plugin settings menu
add_action('admin_menu', 'email_post_update_menu_panel');

function email_post_update_menu_panel() {
  //create new top-level menu
  add_menu_page('Email Settings', 'Email Settings', 'administrator', 'emailpostupdate.php', 'email_settings_page','');
  //call register settings function
  add_action( 'admin_init', 'register_email' );
}



function register_email() {
    register_setting( 'pg-option-group', 'pg_option_email' );
}

function email_settings_page() {


    ?>
  <style>
  #app #menu_container {
    background: #fff;
    border-left: 1px solid #d8d8d8;
    padding: 0px 15px;
    max-width: 900px;
  }
  </style>

    <div class="wrap">
        <div id="app" class="container">
            <div class="row">
                <div id="menu_container" class=" col-xs-12 ">
                    <ul>
                        <li>
                            <form method="post" action="options.php">
                                <?php settings_fields( 'pg-option-group' ); ?>
                                <?php do_settings_sections( 'pg-option-group' ); ?>

                                <table class="form-table">

                                    <tr valign="top">
                                        <th scope="row">Email  Post Change</th>
                                        <td>
                                            <input type="text" name="pg_option_email" id="email" value="<?php echo esc_attr( get_option('pg_option_email') ); ?>" />
                                        </td>
                                    </tr>

                                </table>

                                <?php submit_button(); ?>

                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function($){$(document).ready(function(){
            $("input.switch").bootstrapSwitch();
        })})(jQuery);
    </script>


<!--
    <script src="http://localhost:35729/livereload.js"></script>
    -->

<?php } ?>
