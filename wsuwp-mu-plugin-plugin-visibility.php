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
    protected static $default_hidden = array(
        "acf-field-for-contact-form-7/acf-field-for-contact-form-7.php",
        "advanced-custom-fields-pro/acf.php",
        "alumni-awards/wsuwp-alumni-awards.php",
        "bu-navigation/bu-navigation.php",
        "carousel-without-jetpack/carousel-without-jetpack.php",
        "contact-form-7/wp-contact-form-7.php",
        "conversation-watson-deactivate/watson.php",
        "custom-posts-per-page/custom-posts-per-page.php",
        "customize-snapshots/customize-snapshots.php",
        "edit-flow/edit_flow.php",
        "editorial-access-manager/editorial-access-manager.php",
        "freemius-fixer/freemius-fixer.php",
        "genericond/genericons.php",
        "gift-wrapper-for-woocommerce/gift-wrapper-for-woocommerce.php",
        "gp-easy-passthrough/gp-easy-passthrough.php",
        "gp-limit-submissions/gp-limit-submissions.php",
        "gp-nested-forms/gp-nested-forms.php",
        "gravityformspolls/polls.php",
        "gravityformssurvey/survey.php",
        "gravityformstrello/trello.php",
        "gravityformszapier/zapier.php",
        "gravityperks/gravityperks.php",
        "gutenberg/gutenberg.php",
        "gwlimitchoices/gwlimitchoices.php",
        "gwwordcount/gwwordcount.php",
        "image-shortcake/image-shortcake.php",
        "msm-sitemap/msm-sitemap.php",
        "multiple-post-passwords/multiple-post-passwords.php",
        "o2/o2.php",
        "openid-connect-generic/openid-connect-generic.php",
        "photoshelter-importer/photoshelter-importer.php",
        "powerpress/powerpress.php",
        "publish-feed-items/publish-feed-items.php",
        "query-monitor/query-monitor.php",
        "rewrite-rules-inspector/rewrite-rules-inspector.php",
        "shortcake-bakery/shortcake-bakery.php",
        "shortcode-ui/shortcode-ui.php",
        "syntaxhighlighter/syntaxhighlighter.php",
        "the-events-calendar-community-events/tribe-community-events.php",
        "tinymce-advanced/tinymce-advanced.php",
        "university-center/wsuwp-university-center.php",
        "vals-program-roles/wsuwp-vals-custom-roles.php",
        "woo-discount-rules-pro/woo-discount-rules-pro.php",
        "woo-discount-rules/woo-discount-rules.php",
        "woocommerce-gravityforms-product-addons/gravityforms-product-addons.php",
        "woocommerce-order-status-control/woocommerce-order-status-control.php",
        "woocommerce/woocommerce.php",
        "wookitty/wookitty.php",
        "wordpress-seo/wp-seo.php",
        "wp-by-email/wp-by-email.php",
        "wp-crontrol/wp-crontrol.php",
        "wp-timelines-wsu/timeline.php",
        "wsu-color-palette/wsuwp-color-palette.php",
        "wsu-home-headlines/wsuwp-home-headlines.php",
        "wsu-people-directory/wsu-people-directory.php",
        "wsu-scholarships/plugin.php",
        "wsu-search/wsuwp-search.php",
        "wsu-show-and-hide/wsu-show-and-hide.php",
        "wsuwp-extended-woocommerce/wsuwp-extended-woocommerce.php",
        "wsuwp-html-component-embed/plugin.php",
        "wsuwp-plugin-auto-tagging/wsuwp-plugin-auto-tagging.php",
        "wsuwp-plugin-az-index/wsuwp-plugin-az-index.php",
        "wsuwp-plugin-blocks/wsuwp-plugin-blocks.php",
        "wsuwp-plugin-campus-updates/wsuwp-plugin-campus-updates.php",
        "wsuwp-plugin-covid-email/wsuwp-plugin-covid-email.php",
        "wsuwp-plugin-exhibits/wsuwp-plugin-exhibits.php",
        "wsuwp-plugin-explore/wsuwp-plugin-explore.php",
        "wsuwp-plugin-fields-of-study/wsuwp-plugin-fields-of-study.php",
        "wsuwp-plugin-issue-builder/plugin.php",
        "wsuwp-plugin-legacy-templates/wsuwp-plugin-legacy-templates.php",
        "wsuwp-plugin-logo-generator/wsuwp-plugin-logo-generator.php",
        "wsuwp-plugin-news/wsuwp-plugin-news.php",
        "wsuwp-plugin-newsletter/wsuwp-plugin-newsletter.php",
        "wsuwp-plugin-people-api/wsuwp-plugin-people-api.php",
        "wsuwp-plugin-people-datastore/wsuwp-plugin-people-datastore.php",
        "wsuwp-plugin-pods/init.php",
        "wsuwp-plugin-public-pending/wsuwp-plugin-public-pending.php",
        "wsuwp-plugin-woocommerce-wa-tax-data/wsuwp-plugin-woocommerce-wa-tax-data.php",
        "wsuwp-raduis/wsuwp-radius.php",
        "wsuwp-spine-section-ids/wsuwp-spine-section-ids.php",
        "wsuwp-toc-generator/wsuwp-toc-generator.php",
        "wsuwp-video-backgrounds/wsu-video-backgrounds.php",
        "wsuwp-youtube-embed/wsuwp-youtube-embed.php",
        "wsuws-woocommerce-payment-gateway/wsuws-woocommerce-payment-gateway.php",
    );


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

        if(is_super_admin()) {
            return $plugins;
        }
        
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
