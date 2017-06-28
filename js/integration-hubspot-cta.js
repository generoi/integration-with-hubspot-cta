/**
 * @package WP_HubspotForms
 * Admin JS.
 */

jQuery(function ($) {

  $('#hubspot_cta_embed_insert').click(insert_hubspot_cta);

  // Insert hubspot cta data from a popup.
  function insert_hubspot_cta() {
    var _cta_id = $('#hubspot_cta_embed');
    if ( _cta_id.val() !== '' ) {
      var components = _cta_id.val().split('::');
      var params = '';
      window.send_to_editor('[hubspotcta portal_id="' + components[0] + '" cta_id="' + components[1] + '"' + params + ']');
      $("#TB_closeWindowButton").trigger('click');
      _cta_id.val('');
    }
    else {
      alert(objectL10n.no_cta_selected);
    }
  }

  // Close Hubspot Embed form.
  $('#hubspot_cta_embed_cancel').click(function(e) {
    $("#TB_closeWindowButton").trigger('click');
  });

});
