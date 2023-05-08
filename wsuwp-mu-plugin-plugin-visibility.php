<?php namespace WSUWP\Plugin\Plugin_Visibility;
/**
 * Plugin Name: WSUWP MU Plugin |  Plugin Visibility
 * Description: Enables network administrators to control the visibility of plugins at the site level.
 * Version: 1.0.0
 * Requires PHP: 7.3
 * Author: Washington State University, Dan White
 * Author URI: https://web.wsu.edu/
 * Text Domain: wsuwp-mu-plugin-plugin-visibility
 */

class Plugin_Visibility
{
    protected static $option_name = 'wsu_plugin_visibility_settings';
    protected static $default_hidden = array();


    public static function add_settings_page()
    {
        
        add_plugins_page(
            'Plugin Visibility',
            'Plugin Visibility',
            'manage_network',
            'plugin-visibility',
            __CLASS__ . '::plugin_visibility_content'
        );
        
    }


    private static function update_plugin_visibility($visibility_settings)
    {
        
        if (is_network_admin()) {
            if (get_network_option(null, self::$option_name, null) === null) {               
                add_network_option(null, self::$option_name, $visibility_settings);
            } else {
                update_network_option(null, self::$option_name, $visibility_settings);
            }
        } else {
            $visibility_settings = array_filter(
                $visibility_settings, function ($value) {
                    return !empty($value);
                }
            );
            if (get_option(self::$option_name, null) === null) {
                add_option(self::$option_name, $visibility_settings);
            } else {
                update_option(self::$option_name, $visibility_settings);
            }
        }

    }


    private static function get_default_visibility_settings($plugins)
    {

        $default_settings = array();
        foreach ($plugins as $key => $value) {
            $default_settings[$key] = in_array($key, self::$default_hidden) ? 'hidden'  : 'visible';
        }

        return $default_settings;
        
    }

    private static function get_visibility_settings($plugins)
    {

        $network_visibility_settings = get_network_option(null, self::$option_name, null);
        $site_visibility_settings = get_option(self::$option_name, null);

        // resolve settings for the network level
        if (is_network_admin()) {
            if ($network_visibility_settings !== null) {
                return $network_visibility_settings;
            }

            $default_settings = self::get_default_visibility_settings($plugins);

            return $default_settings;
        }

        // resolve settings at the the site level
        $default_settings = array();
        foreach ($plugins as $key => $value) {
            $default_settings[$key] = '';
        }

        if ($site_visibility_settings !== null) {
            return array_merge($default_settings, $site_visibility_settings);
        }        
        
        return $default_settings;

    }

    
    private static function get_visible_plugins($all_plugins)
    {

        $visibility_settings = array();
        $default_settings = self::get_default_visibility_settings($all_plugins);
        $network_visibility_settings = get_network_option(null, self::$option_name, null);
        $site_visibility_settings = get_option(self::$option_name, null);

        $visibility_settings = $network_visibility_settings !== null ? array_merge($default_settings, $network_visibility_settings) : $default_settings;
        $visibility_settings = !is_network_admin() && $site_visibility_settings !== null ? array_merge($visibility_settings, $site_visibility_settings) : $visibility_settings;

        return array_keys(
            array_filter(
                $visibility_settings, function ($visibility) {
                    return $visibility === 'visible';
                }
            )
        );

    }


    public static function plugin_visibility_content()
    {

        if (isset($_POST['save_changes']) && check_admin_referer('plugin_visibility_nonce') ) {
            self::update_plugin_visibility($_POST['visibility_settings']);
            echo '<div class="notice notice-success"><p>Changes Saved</p></div>';
        }
        
        $plugins = get_plugins();
        $visibility_settings = self::get_visibility_settings($plugins);

        echo '<div class="wrap"><h2>Manage Plugin Visibility</h2>';
        echo '<form action="plugins.php?page=plugin-visibility" method="post">';
        wp_nonce_field('plugin_visibility_nonce');
        echo '<input type="hidden" value="true" name="save_changes" />';
        echo '<table class="wp-list-table widefat plugins" style="margin-top: 30px; max-width: 550px;">';
        echo '<thead><tr>';
        echo '<th>Plugin</th>';
        echo '<th>Visibility</th>';
        echo '</tr></thead>';
        echo '<body>';
        foreach ($plugins as $key => $plugin){
            echo '<tr class="inactive">';
            echo "<td>${plugin['Name']}</td>";
            echo '<td>';
            echo '<select name=visibility_settings[' . $key . ']" style="width: 100%">';
            echo !is_network_admin() ? '<option value=""' . ($visibility_settings[$key] === '' ? ' selected' : '') . '>Network Default</option>' : '';
            echo '<option value="visible"' . ($visibility_settings[$key] === 'visible' ? ' selected' : '') . '>Visible</option>';
            echo '<option value="hidden"' . ($visibility_settings[$key] === 'hidden' ? ' selected' : '') . '>Hidden</option>';
            echo '</select>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</body>';
        echo '</table>';

            submit_button('Save Changes');
        echo '</form>';

        echo '</div>';        

    }


    public static function filter_plugins($plugins)
    {        
        
        $plugins = array_filter(
            $plugins, function ($k) use ($plugins) {                
                return in_array($k, self::get_visible_plugins($plugins));
            }, ARRAY_FILTER_USE_KEY
        );
        
        $plugins = apply_filters('wsu_plugin_visibility_plugins', $plugins);

        return $plugins;
        
    }


    public static function init()
    {

        add_action('admin_menu', __CLASS__ . '::add_settings_page');
        add_action('network_admin_menu', __CLASS__ . '::add_settings_page');

        add_filter('all_plugins', array( __CLASS__, 'filter_plugins' ));

    }

}

Plugin_Visibility::init();
