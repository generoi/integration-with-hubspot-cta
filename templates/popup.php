<?php
/**
 * EmbedHubspotCTA popup form.
 *
 * @package EmbedHubspotCTA
 */

if (!current_user_can( 'manage_options')) {
    wp_die(__('You do not have sufficient permissions to manage options for this site.'));
}

?>
<?php add_thickbox(); ?>
<div id="hubspot-cta-popup" style="display:none;">
  <table class="form-table">
      <tr>
        <th scope="row"><label for="hubspot_cta_embed"><?php _e('Hubspot CTA', 'integration-hubspot-cta'); ?></label></th>
      <td>
        <select id="hubspot_cta_embed" name="hubspot_cta_embed" aria-describedby="timezone-description" class="regular-text">
          <option value=""><?php _e('Choose a CTA to embed', 'integration-hubspot-cta'); ?></option>
          <?php foreach ($ctas as $cta) : ?>
            <option value="<?php echo $cta->portal_id . '::' . $cta->placement_guid ?>"><?php echo $cta->name; ?></option>
          <?php endforeach; ?>
        </select>
      </td>
    </tr>
    <tr>
      <th scope="row"></th>
      <td>
        <input type="button" name="insert" id="hubspot_cta_embed_insert" class="button button-primary" value="<?php _e('Insert CTA', 'integration-hubspot-cta'); ?>">
        <input type="button" name="insert" id="hubspot_cta_embed_cancel" class="button" value="<?php _e('Cancel'); ?>">
      </td>
  </table>
</div>
