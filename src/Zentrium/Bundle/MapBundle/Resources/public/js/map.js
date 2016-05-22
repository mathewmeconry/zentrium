$(function () {
  $('#map').each(function () {
    var $this = $(this);
    var config = $this.data('config');
    var mapProjection = config.projection || 'EPSG:3857';

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
        var options = ol.source.WMTS.optionsFromCapabilities(JSON.parse(layer.capabilities), {layer: layer.layerId, requestEncoding: 'REST'});
        layers.push(new ol.layer.Tile({
          opacity: layer.opacity,
          visible: layer.enabled,
          source: new ol.source.WMTS(options)
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
});
