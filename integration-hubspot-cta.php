<?php
/**
 * @package EmbedHubspotCTA
 */
/*
  Plugin Name: Integration with Hubspot CTA
  Description: Embed Hubspot CTA into your content.
  Version: 1.0.0
  Author: Minnur Yunusov
  License: GPLv2 or later
  Author URI: http://www.minnur.com/
  Text Domain: integration-hubspot-cta
*/

define('EmbedHubspotCTA_PATH', __DIR__);
define('EmbedHubspotCTA_PluginName', plugin_basename(__FILE__));

if (!class_exists('EmbedHubspotCTA')) {

    // List classes in their load order.
    $classes = [
        'EmbedHubspotCTA',
        // @todo
        // 'EmbedHubspotCTAWidget',
    ];

    foreach ($classes as $class) {
        require_once sprintf("%s/includes/%s.php", EmbedHubspotCTA_PATH, $class);
    }

    // Instantiate the plugin class.
    $EmbedHubspotCTA = new EmbedHubspotCTA\EmbedHubspotCTA();

    // Install and uninstall hooks.
    register_activation_hook(EmbedHubspotCTA_PluginName, [$EmbedHubspotCTA, 'activate']);
    register_deactivation_hook(EmbedHubspotCTA_PluginName, [$EmbedHubspotCTA, 'deactivate']);
}
