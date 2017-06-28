<?php
/**
 * @package EmbedHubspotCTA
 */

namespace EmbedHubspotCTA;

class EmbedHubspotCTA
{

  /**
   * Implements __construct().
   * Construct the plugin object.
   */
    public function __construct($add_menu = true)
    {
        if ($add_menu) {
            add_action('init', [&$this, 'init']);
            add_action('admin_head' , [&$this, 'admin_head']);
            add_action('template_redirect', [&$this, 'page_rewrite_redirect']);
            add_action('admin_init', [&$this, 'admin_init']);
            add_action('media_buttons', [&$this, 'media_buttons']);
            add_action('admin_notices', [&$this, 'admin_notices']);
            add_shortcode('hubspotcta', [&$this, 'hubspotcta_output']);
            // Add TinyMCE plugin
            add_filter( 'mce_external_plugins', [$this, 'embed_hubspot_mce_plugin']);
        }
    }

  /**
   * Implements 'init'.
   */
    public function init()
    {
        // Plugin redirect callback paths.
        add_rewrite_tag('%hubspot_cta_embed_plugin%', '([^&]+)' );
        add_rewrite_rule('^hubspotcta\-preview/?(.*)?', 'index.php?hubspot_cta_embed_plugin=$matches[1]', 'top');
    }

    /**
    * TinyMCE plugin.
    */
    public function embed_hubspot_mce_plugin($plugins)
    {
        $plugins['embedHubspotCta'] = plugins_url('/js/mce-plugin.js', EmbedHubspotCTA_PluginName);
        return $plugins;
    }

    /**
    * Implements 'template_redirect'.
    * Load Hubspot cta iframe preview.
    */
    public function page_rewrite_redirect()
    {
        global $wp;
        $template = $wp->query_vars;
        if (array_key_exists('hubspot_cta_embed_plugin', $template) && !empty($template['hubspot_cta_embed_plugin'])) {
            $data = $template['hubspot_cta_embed_plugin'];
            $params = str_replace('\"', '"', urldecode($data));
            $output = '<!DOCTYPE html><html><head><title>' . __('Preview') . '</title></head><body>';
            $output .= do_shortcode('[hubspotcta ' . $params . ']');
            $output .= '</body></html>';
            echo $output;
            exit();
        }
    }

    /**
    * Implements 'admin_init'.
    */
    public function admin_init()
    {
        // Add admin assets.
        wp_register_style('embed_hubspot_cta_admin_css', plugins_url('/css/admin.css', EmbedHubspotCTA_PluginName));
        wp_register_script('embed_hubspot_cta_admin_js', plugins_url('/js/integration-hubspot-cta.js', EmbedHubspotCTA_PluginName), ['jquery']);
        wp_localize_script('embed_hubspot_cta_admin_js', 'objectL10n', [
            'no_cta_selected' => __('Please choose a Hubspot CTA to insert', 'integration-hubspot-cta'),
        ]);
        wp_enqueue_style('embed_hubspot_cta_admin_css');
        wp_enqueue_script('embed_hubspot_cta_admin_js');
    }

    /**
    * Implements 'admin_head'.
    */
    public function admin_head()
    {
        echo '<script>var _hspt_preview_base_url = \''  . get_site_url() . '\';</script>';
    }

    /**
    * Build hubspot embed cta parameters.
    */
    public function hubspotcta_parameters($atts = [])
    {
        $params = [];
        // Add Tracking whenever it is possible by checking cookies.
        // This is a Hubspot supported variable.
        if (isset($_COOKIE['hubspotutk']) && !empty($_COOKIE['hubspotutk'])) {
            $params['hutk'] = $_COOKIE['hubspotutk'];
        }
        return $params;
    }

    /**
    * Hubspot cta shortcode.
    */
    public function hubspotcta_output($atts)
    {
        $atts = shortcode_atts([
            'portal_id' => '',
            'cta_id' => '',
        ], $atts);
        $portal_id = $atts['portal_id'];
        $cta_id = $atts['cta_id'];
        $params = $this->hubspotcta_parameters($atts);
        return $this->hubspot_embed_code($portal_id, $cta_id, $params);
    }

    /**
    * Embed code.
    */
    public function hubspot_embed_code($portal_id, $cta_id, $params = [])
    {
        return '
            <span class="hs-cta-wrapper" id="hs-cta-wrapper-' . $cta_id . '">
              <span class="hs-cta-node hs-cta-' . $cta_id . '" id="hs-cta-' . $cta_id . '">
                <a href="https://cta-redirect.hubspot.com/cta/redirect/' . $portal_id . '/' . $cta_id . '" >
                  <img class="hs-cta-img" id="hs-cta-img-' . $cta_id . '" style="border-width:0px;" src="https://no-cache.hubspot.com/cta/default/' . $portal_id . '/' . $cta_id . '.png" />
                </a>
              </span>
              <script src="https://js.hscta.net/cta/current.js"></script>
              <script>hbspt.cta.load(' . $portal_id . ', \'' . $cta_id. '\', {});</script>
            </span>
        ';
    }

    /**
    * Implements 'media_buttons'.
    * Add "Add Hubspot CTA" button next to Add Media button.
    */
    public function media_buttons()
    {
        echo '<a href="#TB_inline?width=600&height=350&inlineId=hubspot-cta-popup" title="'
        . __('Embed Hubspot CTA', 'integration-hubspot-cta')
        . '" class="button thickbox">'
        . __('Embed Hubspot CTA', 'integration-hubspot-cta')
        . '</a>';
    }

    /**
    * Implements 'admin_notices'.
    * Add popup html to head.
    */
    public function admin_notices()
    {
        // Render the settings template
        $ctas = $this->get_hubspot_cta();
        $screen = get_current_screen();
        if ($screen->parent_base == 'edit') {
            require_once sprintf("%s/templates/popup.php", EmbedHubspotCTA_PATH);
        }
    }

    /**
    * Get Hubspot CTAs.
    */
    public function get_hubspot_cta()
    {
        $ctas = wp_cache_get('embed_hubspot_cta');
        if ($ctas === false) {
            $limit = 100;
            $offset = 0;
            $ctas = [];

            do {
                $url = 'https://api.hubapi.com/ctas/v2/ctas?hapikey=' . get_option('embed_hubspot_api_key') . '&limit=' . $limit . '&offset='. $offset;
                $response = wp_remote_get($url);
                $response = json_decode($response['body']);
                if (!isset($response->meta)) {
                    break;
                }
                $has_more = $response->meta->total_count > ($response->meta->offset + $response->meta->limit);
                if ($has_more) {
                    $offset = $offset + $response->meta->limit;
                }
                $ctas = array_merge($ctas, $response->objects);
            } while ($has_more);

            wp_cache_set('embed_hubspot_cta', $ctas);
        }
        return $ctas;
    }

    /**
    * Activate the plugin.
    */
    public static function activate()
    {
        global $wp_rewrite;
        // Refresh path rules.
        $wp_rewrite->flush_rules();
    }

    /**
    * Deactivate the plugin.
    */
    public static function deactivate()
    {
    }

}
