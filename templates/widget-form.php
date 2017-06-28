<?php
/**
 * EmbedHubspotCTA widget form.
 *
 * @package EmbedHubspotCTA
 */
?>
<p>
  <label for="<?php echo $this->get_field_id('hubspot_cta_embed'); ?>"><?php _e('Choose Hubspot CTA to embed', 'integration-hubspot-cta'); ?>:</label>
  <select id="<?php echo $this->get_field_id('hubspot_cta_embed'); ?>" name="<?php echo $this->get_field_name('hubspot_cta_embed'); ?>" style="max-width: 100%" class="postform">
    <option value=""><?php _e('Choose a CTA to embed', 'integration-hubspot-cta'); ?></option>
    <?php foreach ($ctas as $cta) : ?>
      <?php $cta_id = $cta->portalId . '::' . $cta->guid; ?>
      <option value="<?php echo $cta_id ?>"<?php if ($hubspot_current_cta == $cta_id) : ?> selected<?php endif; ?>><?php echo $cta->name; ?></option>
    <?php endforeach; ?>
  </select>
</p>
