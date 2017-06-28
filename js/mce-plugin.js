/**
 * @package WP_HubspotForms
 * Hubspot TinyMCE plugin.
 */

(function() {

  tinymce.create('tinymce.plugins.embedHubspotCta', {

    init: function(ed, url) {
      var t = this;
      ed.on('BeforeSetcontent', function(o) {
        o.content = t._do_render_cta(o.content);
      });
      ed.on('PostProcess', function(o) {
        if (o.get) {
          o.content = t._get_cta(o.content);
        }
      });
    },

    _do_render_cta: function(co) {
      return co.replace(/\[hubspotcta ([^\]]*)\]/g, function(a, b) {
        var baseUrl = (_hspt_preview_base_url !== undefined) ? _hspt_preview_base_url + '/' : '/';
        return '<iframe class="hubspot-cta-embed" title="' + tinymce.DOM.encode(b) + '" style="border:none" width="300" height="100" src="' + baseUrl + 'index.php?hubspot_cta_embed_plugin=' + encodeURIComponent(b) + '" frameborder="0"></iframe>';
      });
    },

    _get_cta : function(co) {
      function getAttr(s, n) {
        n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
        return n ? tinymce.DOM.decode(n[1]) : '';
      };

      return co.replace(/(<iframe[^>]+>)<\/iframe>/g, function(a, iframe) {
        var cls = getAttr(iframe, 'class');

        if (cls.indexOf('hubspot-cta-embed') != -1) {
          return '[hubspotcta ' + tinymce.trim(getAttr(iframe, 'title')) + ']';
        }

        return a;
      });

    },

  });

  // Register plugin
  tinymce.PluginManager.add('embedHubspotCta', tinymce.plugins.embedHubspotCta);

})();
