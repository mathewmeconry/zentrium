import $ from 'jquery';
import Collection from 'ol/collection';
import Map from 'ol/map';
import View from 'ol/view';
import proj from 'ol/proj';
import FullScreenControl from 'ol/control/fullscreen';
import ZoomControl from 'ol/control/zoom';
import GeoJSON from 'ol/format/geojson';
import Circle from 'ol/style/circle';
import Fill from 'ol/style/fill';
import Stroke from 'ol/style/stroke';
import Style from 'ol/style/style';
import TileLayer from 'ol/layer/tile';
import VectorLayer from 'ol/layer/vector';
import VectorSource from 'ol/source/vector';
import WMTSSource from 'ol/source/wmts';
import { addFlash, post } from 'zentrium';

$(function () {
  $('#map').each(function () {
    const $this = $(this);
    const config = $this.data('config');
    const mapProjection = config.projection || 'EPSG:3857';
    const geoJsonParser = new GeoJSON();

    $this.css('height', Math.max(300, $(document.body).height() - $this.offset().top - 20));

    const defaultStyle = new Style({
      image: new Circle({
        radius: 5,
        fill: new Fill({ color: 'rgba(255, 0, 0, 0.3)' }),
        stroke: new Stroke({color: 'red', width: 2}),
      }),
      fill: new Fill({ color: 'rgba(0, 0, 255, 0.3)' }),
      stroke: new Stroke({color: 'blue', width: 2}),
    });

    const layers = [];
    for(let layer of config.layers) {
      if(layer.type == 'wmts') {
        const capabilities = JSON.parse(layer.capabilities);
        // Temporary fix for https://github.com/openlayers/ol3/issues/4256
        for(let layerContent of capabilities['Contents']['Layer']) {
          if('WGS84BoundingBox' in layerContent) {
            delete layerContent['WGS84BoundingBox'];
          }
        }
        const options = WMTSSource.optionsFromCapabilities(capabilities, {layer: layer.layerId, requestEncoding: 'REST'});
        layers.unshift(new TileLayer({
          opacity: layer.opacity,
          visible: layer.enabled,
          source: new WMTSSource(options)
        }));
      } else if(layer.type == 'feature') {
        const features = layer.features.map(feature => {
          return geoJsonParser.readFeature(feature, {
            dataProjection: 'EPSG:4326',
            featureProjection: mapProjection,
          });
        });
        layers.unshift(new VectorLayer({
          opacity: layer.opacity,
          visible: layer.enabled,
          source: new VectorSource({ features }),
          style: defaultStyle,
        }));
      }
    }

    const controls = new Collection();
    controls.push(new ZoomControl());

    const map = new Map({
      layers: layers,
      controls: controls,
      target: this,
      view: new View({
        center: proj.transform(config.center, 'EPSG:4326', mapProjection),
        projection: mapProjection,
        zoom: config.zoom,
        resolutions: [4000, 3750, 3500, 3250, 3000, 2750, 2500, 2250, 2000, 1750, 1500, 1250, 1000, 750, 650, 500, 250, 100, 50, 20, 10, 5, 2.5, 2, 1.5, 1, 0.5, 0.25],
      }),
    });
    $this.data('ol', map);

    $('#map-fullscreen').click(function () {
      if (FullScreenControl.isFullScreen()) {
        FullScreenControl.exitFullScreen();
      } else {
        const element = map.getTargetElement();
        FullScreenControl.requestFullScreen(element);
      }
    });
  });

  $('#map-set-default').click(function (event) {
    const $this = $(this);
    event.preventDefault();
    post($this.attr('href'), {}).done(function (res) {
      addFlash('success', res.message, true);
      $this.remove();
    });
    return false;
  });

  $('#map-save-viewport').click(function (event) {
    const view = $('#map').data('ol').getView();
    const center = proj.toLonLat(view.getCenter(), view.getProjection());

    post($(this).attr('href'), {
      'map_viewport[centerLongitude]': center[0],
      'map_viewport[centerLatitude]': center[1],
      'map_viewport[zoom]': view.getZoom()
    }).done(function (res) {
      addFlash('success', res.message, true);
    });

    return false;
  });
});
