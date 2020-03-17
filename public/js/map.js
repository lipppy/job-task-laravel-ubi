var map = new ol.Map({
    target: 'map',
    layers: [
        new ol.layer.Tile({
            source: new ol.source.OSM()
        }),
        new VectorLayer({
            source: new VectorSource({
              format: new GeoJSON(),
              url: './region/1'
            })
        })
    ],
    view: new ol.View({
        center: ol.proj.fromLonLat([23.41, 45.82]),
        zoom: 5
    })
});