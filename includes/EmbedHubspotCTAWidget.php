<?php
/**
 * @package EmbedHubspotCTA
 */

namespace EmbedHubspotCTA;

class EmbedHubspotCTAWidget extends \WP_Widget
{

    private $EmbedHubspotCTA;

    /**
    * Implements __construct().
    * Register widget with WordPress.
    */
    public function __construct()
    {
        parent::__construct(
            'WP_IntegrationHubspotCTAWidget',
            __('Embed Hubspot CTA', 'integration-hubspot-cta'),
            ['description' => __('Display Hubspot CTA', 'integration-hubspot-cta')]
        );

        // Call the main plugin. We will need this to access some of the methods
        // in this widget class, but we don't want double initialize and load
        // files/hooks.
        $this->EmbedHubspotCTA = new \EmbedHubspotCTA\EmbedHubspotCTA(false);
    }

    /**
    * Implements widget().
    * Display widget.
    */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        if (!empty($instance['hubspot_cta_embed'])) {
            list ($portal_id, $cta_id) = explode( '::', $instance['hubspot_cta_embed'] );
            $atts = [];
            $params = $this->EmbedHubspotCTA->hubspotcta_parameters($atts);
            echo $args['before_title'] . $this->EmbedHubspotCTA->hubspot_embed_code($portal_id, $cta_id, $params) . $args['after_title'];
        }
        echo $args['after_widget'];
    }

    /**
    * Implements form().
    * Widget configuration form.
    */
    public function form($instance)
    {
        $ctas = $this->EmbedHubspotCTA->get_hubspot_cta();
        $hubspot_current_cta  = !empty($instance[ 'hubspot_cta_embed']) ? $instance['hubspot_cta_embed'] : '';
        // Render the settings template
        include sprintf("%s/templates/widget-form.php", EmbedHubspotCTA_PATH);
    }

    /**
    * Implements update().
    * Save widget configuration options.
    */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['hubspot_cta_embed'] = (!empty($new_instance['hubspot_cta_embed'])) ? strip_tags($new_instance['hubspot_cta_embed']) : '';
        return $instance;
    }

}

// Register Hubspot CTA widget.
add_action('widgets_init', function() {
    register_widget('EmbedHubspotCTA\EmbedHubspotCTAWidget');
});
