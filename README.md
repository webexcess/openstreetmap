# WebExcess.OpenStreetMap for Neos CMS
[![Logo](Documentation/logo-20.png)](Documentation/logo-512.png)
[![Latest Stable Version](https://poser.pugx.org/webexcess/openstreetmap/v/stable)](https://packagist.org/packages/webexcess/openstreetmap)
[![License](https://poser.pugx.org/webexcess/openstreetmap/license)](https://packagist.org/packages/webexcess/openstreetmap)

Easy and flexible [OpenStreetMap](https://www.openstreetmap.org/) Implementation as NodeType or Fusion Component.

## Installation
```
composer require webexcess/openstreetmap
```

Built for [Neos](https://www.neos.io/)


## Implemented Styles

| Original | Grayscale | Dark |
| -------- | --------- | ---- |
| ![Original Map Style](Documentation/map-style-original.png?raw=true "Original Map Style") | ![Original Map Style](Documentation/map-style-grayscale.png?raw=true "Original Map Style") | ![Original Map Style](Documentation/map-style-dark.png?raw=true "Original Map Style") |

**Default JS & CSS**

By default, this plugin loads a JS and CSS file.

It's best practice to include them in your custom builds and remove the default assets:

    prototype(Neos.Neos:Page) {
      head.stylesheets.openStreetMap >
      body.javascripts.openStreetMap >
    }



## Editor Settings

|          |          |
| -------- | -------- |
| <pre># default</pre> | ![Editor Default](Documentation/editor-default.png?raw=true "Editor Default") |
| <pre>'WebExcess.OpenStreetMap:Map':<br>  superTypes:<br>    'WebExcess.OpenStreetMap:Map.LatLon.Editable': true</pre> | ![Editor LatLon](Documentation/editor-latlon.png?raw=true "Editor LatLon") |
| <pre>'WebExcess.OpenStreetMap:Map':<br>  superTypes:<br>    'WebExcess.OpenStreetMap:Map.Style': true</pre> | ![Editor Style](Documentation/editor-style.png?raw=true "Editor Style") |
| <pre>'WebExcess.OpenStreetMap:Map':<br>  superTypes:<br>    'WebExcess.OpenStreetMap:Map.MaxZoom': true</pre> | ![Editor Maximum Zoom](Documentation/editor-maxzoom.png?raw=true "Editor Maximum Zoom") |


## Default Settings

	WebExcess:
	  OpenStreetMap:
	    tilesUrl: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
	    minZoom: 6
	    maxZoom: 18
	    style: ~ # ~, grayscale or dark
	    ratio: '3:2'
	    address: ~ # Talisker Distillery, Carbost, Scotland
	    lat: ~ # 57.302387
	    lon: ~ # -6.356159
        paddingTopLeft: [100, 100]
        paddingBottomRight: [100, 100]
	    mapOptions: []


## Fusion only Implementation

**Disable NodeType**

	'WebExcess.OpenStreetMap:Map':
	  abstract: true

**Simple**

	map = WebExcess.OpenStreetMap:Map.Component {
	  address = 'Talisker Distillery, Carbost, Scotland'
	  tooltip = 'Talisker Distillery'
	  popup = 'Also have a look at <a href=\\"https:\/\/unsplash.com\/search\/photos\/talisker-bay\\" target=\\"_blank\\">Talisker Bay<\/a>.'
	}

**Advanced**

	map = WebExcess.OpenStreetMap:Map.Component {
	  lat = 57.302387
	  lon = -6.356159
	  style = 'dark'
	  ratio = '4:1'
	  renderer.content.customOverlay = Neos.Fusion:Tag {
	      @position = 'after map'
	      content = 'A Special Information..'
	  }
	}

**GeoJSON**

inline with multiple markers..

	map = WebExcess.OpenStreetMap:Map.Component {
	  json = '[{"type":"Feature","properties":{"tooltip":"Talisker Distillery"},"geometry":{"type":"Point","coordinates":[-6.356159,57.302387]}},{"type":"Feature","properties":{"popup":"Talisker Bay<br \/>&raquo; <a href=\\"https:\/\/unsplash.com\/search\/photos\/talisker-bay\\" target=\\"_blank\\">Photos<\/a>"},"geometry":{"type":"Point","coordinates":[-6.456646,57.283313]}}]'
	}

or with an external source..

	map = WebExcess.OpenStreetMap:Map.Component {
	  json = '/talisker-geo.json'
	}

**EEL Helper**

	Geocode.latLonFromAddress('Talisker Distillery, Carbost, Scotland')


## Interacting with JavaScript

**Methods**

    mapIds = window.openStreetMap.getMapIds();
    > Array [ "map-d8aaafcf-b2fa-4240-8a28-ed48b6e6143c", "map-b9ffb901-e91e-4261-a127-ec3246bc6350", .. ]

    map = window.openStreetMap.getMap('map-d8aaafcf-b2fa-4240-8a28-ed48b6e6143c');
    > { MapObject }

    markers = window.openStreetMap.getMarkers('map-d8aaafcf-b2fa-4240-8a28-ed48b6e6143c');
    > Array [ { MarkerObject }, { MarkerObject }, ... ]

**Events**

    document.addEventListener('initializedOpenStreetMap', e => {
        console.log(e);
    });
    > { details: { map: { MapObject }, mapId: 'map-123..' }, ...DefaultEventProperties }

    document.addEventListener('addedOpenStreetMapMarkers', e => {
        console.log(e);
    });
    > { details: { map: { MapObject }, mapId: 'map-123..', geoJson: { GeoJSON } }, ...DefaultEventProperties }


## MarkerCluster Example

Load the Leaflet plugin..

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.1.0/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.1.0/dist/MarkerCluster.Default.css" />
    <script type="text/javascript" src="https://unpkg.com/leaflet.markercluster@1.1.0/dist/leaflet.markercluster.js"></script>

Register a JS hook..

    prototype(WebExcess.OpenStreetMap:Map.Component) {
      mapHooks.addMarkersLayerHook = 'myAddMarkersLayerHook'
    }

Load the plugin with your hook..

    window.myAddMarkersLayerHook = (layer) => {
      const clusterMarkers = L.markerClusterGroup()
      return clusterMarkers.addLayer(layer)
    }


## Leaflet Map Options

See [leafletjs.com](https://leafletjs.com/reference-1.3.4.html#map-option)

**Via default settings**

	WebExcess:
	  OpenStreetMap:
	    mapOptions:
	      scrollWheelZoom: true

**Via Fusion**

	prototype(WebExcess.OpenStreetMap:Map.Component) {
	  mapOptions {
	    scrollWheelZoom = true
	  }
	}

**Inspector Editor**

Feel free to add custom Editors to enhance your Editors experience as he need's it.


## Acknowledgements

Thanks to [OpenStreetMap](https://www.openstreetmap.org/) for providing free and open map data. And thanks to [leafletjs.com](https://leafletjs.com/) for providing an open-source JS library for interactive maps.


------------------------------------------

developed by [webexcess GmbH](https://webexcess.ch/)
