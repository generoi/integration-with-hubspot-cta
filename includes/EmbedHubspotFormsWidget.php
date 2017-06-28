<?php
/**
 * @package EmbedHubspotForms
 */

namespace EmbedHubspotForms;

class EmbedHubspotFormsWidget extends \WP_Widget {

  private $EmbedHubspotForms;

  /**
   * Implements __construct().
   * Register widget with WordPress.
   */
  public function __construct() {
    parent::__construct(
      'WP_IntegrationHubspotFormsWidget',
      __('Embed Hubspot Form', 'integration-hubspot-forms'),
      array( 'description' => __( 'Display Hubspot Form', 'integration-hubspot-forms' ), )
    );
    // Call the main plugin. We will need this to access some of the methods
    // in this widget class, but we don't want double initialize and load
    // files/hooks.
    $this->EmbedHubspotForms = new \EmbedHubspotForms\EmbedHubspotForms( false );
  }

  /**
   * Implements widget().
   * Display widget.
   */
  public function widget( $args, $instance ) {
    echo $args['before_widget'];
    if ( !empty( $instance['hubspot_forms_embed'] ) ) {
      list ($portal_id, $form_id) = explode( '::', $instance['hubspot_forms_embed'] );
      $atts = array(
        'sfdccampaignid' => !empty($instance['sfdcCampaignId']) ? $instance['sfdcCampaignId'] : '',
        'css' => ( isset($instance[ 'css' ] ) && $instance[ 'css' ] == 'on' ) ? '' : 'hide',
      );
      $params = $this->EmbedHubspotForms->hubspotform_parameters( $atts );
      echo $args['before_title'] . $this->EmbedHubspotForms->hubspot_embed_code( $portal_id, $form_id, $params ) . $args['after_title'];
    }
    echo $args['after_widget'];
  }

  /**
   * Implements form().
   * Widget configuration form.
   */
  public function form( $instance ) {
    $forms = $this->EmbedHubspotForms->get_hubspot_forms();
    $hubspot_current_form  = !empty( $instance[ 'hubspot_forms_embed' ] ) ? $instance[ 'hubspot_forms_embed' ] : '';
    $sfdcCampaignId  = !empty( $instance[ 'sfdcCampaignId' ] ) ? $instance[ 'sfdcCampaignId' ] : '';
    $css = !empty($instance[ 'css' ]) ? strip_tags( $instance[ 'css' ] ) : '';
    // Render the settings template
    include ( sprintf( "%s/templates/widget-form.php", EmbedHubspotForms_PATH ) );
  }

  /**
   * Implements update().
   * Save widget configuration options.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['hubspot_forms_embed'] = ( ! empty( $new_instance['hubspot_forms_embed'] ) ) ? strip_tags( $new_instance['hubspot_forms_embed'] ) : '';
    $instance['sfdcCampaignId'] = ( ! empty( $new_instance['sfdcCampaignId'] ) ) ? strip_tags( $new_instance['sfdcCampaignId'] ) : '';
    $instance['css'] = strip_tags( $new_instance[ 'css' ] );
    return $instance;
  }

}

// Register Hubspot Forms widget.
add_action( 'widgets_init', function(){
  register_widget( 'EmbedHubspotForms\EmbedHubspotFormsWidget' );
});
