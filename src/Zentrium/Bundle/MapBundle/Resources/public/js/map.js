$(function () {
  $('#map').each(function () {
    var $this = $(this);
    var config = $this.data('config');
    var mapProjection = config.projection || 'EPSG:3857';
    var geoJsonParser = new ol.format.GeoJSON();

    var defaultStyle = new ol.style.Style({
      image: new ol.style.Circle({
        radius: 5,
        fill: new ol.style.Fill({ color: 'rgba(255, 0, 0, 0.3)' }),
        stroke: new ol.style.Stroke({color: 'red', width: 2}),
      }),
      fill: new ol.style.Fill({ color: 'rgba(0, 0, 255, 0.3)' }),
      stroke: new ol.style.Stroke({color: 'blue', width: 2}),
    });

    var layers = [];
    for(var i in config.layers) {
      var layer = config.layers[i];
      if(layer.type == 'wmts') {
        var capabilities = JSON.parse(layer.capabilities);
        // Temporary fix for https://github.com/openlayers/ol3/issues/4256
        for(var j in capabilities['Contents']['Layer']) {
          if('WGS84BoundingBox' in capabilities['Contents']['Layer'][j]) {
            delete capabilities['Contents']['Layer'][j]['WGS84BoundingBox'];
          }
        }
        var options = ol.source.WMTS.optionsFromCapabilities(capabilities, {layer: layer.layerId, requestEncoding: 'REST'});
        layers.unshift(new ol.layer.Tile({
          opacity: layer.opacity,
          visible: layer.enabled,
          source: new ol.source.WMTS(options)
        }));
      } else if(layer.type == 'feature') {
        var features = [];
        for(var j in layer.features) {
          features.push(geoJsonParser.readFeature(layer.features[j], {
            dataProjection: 'EPSG:4326',
            featureProjection: mapProjection,
          }));
        }
        var source = new ol.source.Vector({
          features: features
        });
        layers.unshift(new ol.layer.Vector({
          opacity: layer.opacity,
          visible: layer.enabled,
          source: source,
          style: defaultStyle,
        }));
      }
    }

    var controls = new ol.Collection();
    controls.push(new ol.control.Zoom());

    $this.data('ol', new ol.Map({
      layers: layers,
      controls: controls,
      target: this,
      view: new ol.View({
        center: ol.proj.transform(config.center, 'EPSG:4326', mapProjection),
        projection: mapProjection,
        zoom: config.zoom,
      }),
    }));
  });

  $('#map-set-default').click(function (event) {
    var $this = $(this);
    event.preventDefault();
    Zentrium.post($this.attr('href'), {}).done(function (res) {
      Zentrium.addFlash('success', res.message, true);
      $this.remove();
    });
    return false;
  });

  $('#map-save-viewport').click(function (event) {
    var view = $('#map').data('ol').getView();
    var center = ol.proj.toLonLat(view.getCenter(), view.getProjection());

    Zentrium.post($(this).attr('href'), {
      'map_viewport[centerLongitude]': center[0],
      'map_viewport[centerLatitude]': center[1],
      'map_viewport[zoom]': view.getZoom()
    }).done(function (res) {
      Zentrium.addFlash('success', res.message, true);
    });

    return false;
  });
});
