<?php
add_action('admin_init', 'ratchet_admin_init');
add_action('admin_menu', 'ratchet_admin_menu');
add_filter('plugin_action_links', 'ratchet_plugin_action_links', 10, 2);

ratchet_admin_warnings();

function ratchet_admin_init() {
    register_setting('ratchet-settings-group', 'ratchet_access_token');
}

// Display a Settings link on the main Plugins page
function ratchet_plugin_action_links($links, $file) {
    if ($file == plugin_basename(dirname(__FILE__) . '/ratchet.php')) {
        $ratchet_links = '<a href="' . get_admin_url() . 'options-general.php?page=ratchet/admin.php">' . __('Settings') . '</a>';
        array_unshift($links, $ratchet_links);
    }
    return $links;
}

function ratchet_admin_warnings() {
    if (!get_option('ratchet_access_token') && !isset($_POST['submit'])) {
        function ratchet_warning() {
            echo "
            <div id='ratchet-warning' class='updated fade'><p><strong>".__('Ratchet is almost ready.')."</strong> ".sprintf(__('You must <a href="%1$s">enter your Ratchet.io Access Token</a> for it to work.'), "admin.php?page=ratchet-token-config")."</p></div>
            ";
        }
        add_action('admin_notices', 'ratchet_warning');
    }
}

function ratchet_admin_menu() {
    if (class_exists('Jetpack')) {
        add_action('jetpack_admin_menu', 'ratchet_load_menu');
    } else {
        ratchet_load_menu();
    }
}

function ratchet_load_menu() {
    if (class_exists('Jetpack')) {
        add_submenu_page('jetpack', __('Ratchet Configuration'), __('Ratchet Configuration'), 'manage_options', 'ratchet-token-config', 'ratchet_conf');
    } else {
        add_submenu_page('plugins.php', __('Ratchet Configuration'), __('Ratchet Configuration'), 'manage_options', 'ratchet-token-config', 'ratchet_conf');
    }
}

function ratchet_nonce_field($action = -1) { 
    return wp_nonce_field($action); 
}
$ratchet_nonce = 'ratchet-update-token';

function ratchet_conf() {
    global $ratchet_nonce;
    echo "in ratchet_conf";

    if (isset($_POST['submit'])) {
        echo "in ratchet_conf -> submit";
        if (function_exists('current_user_can') && !current_user_can('manage_options')) {
            die(__('Cheatin&#8217; uh?'));
        }

        check_admin_referer($ratchet_nonce);
        $token = preg_replace('/[^a-h0-9]/i', '', $_POST['access_token']);
        // TODO verify that the token is valid
        echo "updating token";
        update_option('ratchet_access_token', $token);
    }
?>
<?php if (!empty($_POST['submit'])) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('Ratchet Configuration'); ?></h2>
<form method="post">
    <?php ratchet_nonce_field($ratchet_nonce); ?>
    <?php settings_fields('ratchet-settings-group'); ?>
    <?php do_settings_fields('ratchet-settings-group'); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Access token</th>
        <td><input size="32" type="text" name="access_token" value="<?php echo get_option('ratchet_access_token'); ?>"/></td>
        </tr>
    </table>

    <p class="submit">
    <input name="submit" type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
    </p>
</form>
</div>
<?php
}
    

    /*
    // create new top-level menu
    add_menu_page('Ratchet Plugin Settings', 'Ratchet Settings', 'administrator', __FILE__, 'ratchet_settings_page');

    add_action('admin_init', 'register_ratchet_settings');
    */

/*function register_ratchet_settings() {
    register_setting('ratchet-settings-group', 'new_option_name');
    register_setting('ratchet-settings-group', 'some_other_option');
}

function ratchet_settings_page() {
?>
<h2>Ratchet</h2>
<?php
}*/


?>
