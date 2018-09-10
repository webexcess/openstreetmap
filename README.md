# WebExcess.OpenStreetMap for Neos CMS
[![Logo](Documentation/logo-20.png)](Documentation/logo-512.png)
[![Latest Stable Version](https://poser.pugx.org/webexcess/openstreetmap/v/stable)](https://packagist.org/packages/webexcess/openstreetmap)
[![License](https://poser.pugx.org/webexcess/openstreetmap/license)](https://packagist.org/packages/webexcess/openstreetmap)

Easy and flexible [OpenStreetMap](https://www.openstreetmap.org/) Implementation as NodeType or Fusion Component.

## Installation
```
composer require webexcess/openstreetmap
```

Supported for Neos 3.x, 4.x and will be maintained for upcoming versions.


## Implemented Styles

| Original | Grayscale | Dark |
| -------- | --------- | ---- |
| ![Original Map Style](Documentation/map-style-original.png?raw=true "Original Map Style") | ![Original Map Style](Documentation/map-style-grayscale.png?raw=true "Original Map Style") | ![Original Map Style](Documentation/map-style-dark.png?raw=true "Original Map Style") |


## Editor Settings

|          |          |
| -------- | -------- |
| <pre># default</pre> | ![Editor Default](Documentation/editor-default.png?raw=true "Editor Default") |
| <pre>'WebExcess.OpenStreetMap:Map':<br>  superTypes:<br>    'WebExcess.OpenStreetMap:Map.LatLon.Editable': true</pre> | ![Editor LatLon](Documentation/editor-latlon.png?raw=true "Editor LatLon") |
| <pre>'WebExcess.OpenStreetMap:Map':<br>  superTypes:<br>    'WebExcess.OpenStreetMap:Map.Style': true</pre> | ![Editor Style](Documentation/editor-style.png?raw=true "Editor Style") |


## Default Settings

	WebExcess:
	  OpenStreetMap:
	    tilesUrl: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
	    minZoom: 6
	    maxZoom: 20
	    style: ~ # ~, grayscale or dark
	    ratio: '3:2'
	    address: ~ # Talisker Distillery, Carbost, Scotland
	    lat: ~ # 57.302387
	    lon: ~ # -6.356159
	    mapOptions: []


## Fusion only Implementation

**Disable NodeType**

	'WebExcess.OpenStreetMap:Map':
	  abstract: true

**Simple**

	map = WebExcess.OpenStreetMap:Map.Component {
	  address = ${'Talisker Distillery, Carbost, Scotland'}
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

**EEL Helper**

	Geocode.latLonFromAddress('Talisker Distillery, Carbost, Scotland')


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
