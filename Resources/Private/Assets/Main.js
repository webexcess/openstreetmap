window.initOpenStreetMap = function() {

    var maps = [];

    var mapOptions = {
        imagePath: '_Resources/Static/Packages/WebExcess.OpenStreetMap/Assets/',
        // dragging: false,
        touchZoom: false,
        // zoomControl: false,
        scrollWheelZoom: false
    };

    if (!Array.from) {
        Array.from = (function () {
            var toStr = Object.prototype.toString;
            var isCallable = function (fn) {
                return typeof fn === 'function' || toStr.call(fn) === '[object Function]';
            };
            var toInteger = function (value) {
                var number = Number(value);
                if (isNaN(number)) { return 0; }
                if (number === 0 || !isFinite(number)) { return number; }
                return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
            };
            var maxSafeInteger = Math.pow(2, 53) - 1;
            var toLength = function (value) {
                var len = toInteger(value);
                return Math.min(Math.max(len, 0), maxSafeInteger);
            };

            // The length property of the from method is 1.
            return function from(arrayLike/*, mapFn, thisArg */) {
                // 1. Let C be the this value.
                var C = this;

                // 2. Let items be ToObject(arrayLike).
                var items = Object(arrayLike);

                // 3. ReturnIfAbrupt(items).
                if (arrayLike == null) {
                    throw new TypeError("Array.from requires an array-like object - not null or undefined");
                }

                // 4. If mapfn is undefined, then let mapping be false.
                var mapFn = arguments.length > 1 ? arguments[1] : void undefined;
                var T;
                if (typeof mapFn !== 'undefined') {
                    // 5. else
                    // 5. a If IsCallable(mapfn) is false, throw a TypeError exception.
                    if (!isCallable(mapFn)) {
                        throw new TypeError('Array.from: when provided, the second argument must be a function');
                    }

                    // 5. b. If thisArg was supplied, let T be thisArg; else let T be undefined.
                    if (arguments.length > 2) {
                        T = arguments[2];
                    }
                }

                // 10. Let lenValue be Get(items, "length").
                // 11. Let len be ToLength(lenValue).
                var len = toLength(items.length);

                // 13. If IsConstructor(C) is true, then
                // 13. a. Let A be the result of calling the [[Construct]] internal method of C with an argument list containing the single item len.
                // 14. a. Else, Let A be ArrayCreate(len).
                var A = isCallable(C) ? Object(new C(len)) : new Array(len);

                // 16. Let k be 0.
                var k = 0;
                // 17. Repeat, while k < len… (also steps a - h)
                var kValue;
                while (k < len) {
                    kValue = items[k];
                    if (mapFn) {
                        A[k] = typeof T === 'undefined' ? mapFn(kValue, k) : mapFn.call(T, kValue, k);
                    } else {
                        A[k] = kValue;
                    }
                    k += 1;
                }
                // 18. Let putStatus be Put(A, "length", len, true).
                A.length = len;
                // 20. Return A.
                return A;
            };
        }());
    }

    try {
        Array.from(document.querySelectorAll('.webexcess-openstreetmap__map')).forEach(function (mapContainer) {
            var mapId = mapContainer.getAttribute('id');
            initMap(mapContainer, mapId);
        });
    } catch (e) {
        console.log(e);
    }

    function initMap(mapContainer, mapId) {
        var mapSpecificOptions = mapContainer.getAttribute('data-map-options');
        if (mapSpecificOptions) {
            mapSpecificOptions = JSON.parse(mapSpecificOptions);
        } else {
            mapSpecificOptions = {};
        }
        mapSpecificOptions = Object.assign({}, mapOptions, mapSpecificOptions);

        maps[mapId] = new L.Map(mapId, mapSpecificOptions);

        var tilesUrl = mapContainer.getAttribute('data-tiles-url');
        var tiles = new L.TileLayer(tilesUrl, {
            minZoom: mapContainer.getAttribute('data-min-zoom'),
            maxZoom: mapContainer.getAttribute('data-max-zoom'),
            attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
        });
        maps[mapId].addLayer(tiles);

        var lat = mapContainer.getAttribute('data-lat');
        var lng = mapContainer.getAttribute('data-lon');
        var popup = mapContainer.getAttribute('data-popup');
        var tooltip = mapContainer.getAttribute('data-tooltip');
        if (lat && lng) {
            maps[mapId].setView(L.latLng(lat, lng), 15);
            var marker = L.marker([lat, lng]);
            if (popup) {
                marker.bindPopup(popup);
            }
            if (tooltip) {
                marker.bindTooltip(tooltip);
            }
            marker.addTo(maps[mapId]);
        }

        var geoJson = mapContainer.getAttribute('data-json');
        if (geoJson) {
            try {
                var geoJsonObject = {};
                if (geoJson.indexOf('http') === 0 || geoJson.substr(0, 1) === '/') {

                    var request = new XMLHttpRequest();
                    request.open('GET', geoJson, true);
                    request.onload = function () {
                        if (request.status >= 200 && request.status < 400) {
                            geoJsonObject = JSON.parse(request.responseText.trim());
                            window.addOpenStreetMapGeoJSON(maps[mapId], geoJsonObject);
                        }
                    };
                    request.send();

                } else {
                    geoJsonObject = JSON.parse(geoJson);
                    window.addOpenStreetMapGeoJSON(maps[mapId], geoJsonObject);
                }
            } catch (e) {
                console.log(e);
            }
        }

        setInterval(function () {
            document.getElementById(mapId).classList.remove('hide-leaflet-pane');
        }, 500);
    }
};

window.addOpenStreetMapGeoJSON = function(mapElement, geoJsonObject) {
    var geojsonLayer = L.geoJSON(geoJsonObject, {
        pointToLayer: function(geoJsonPoint, latlng) {
            var marker = L.marker(latlng);
            if (geoJsonPoint.properties.popup) {
                marker.bindPopup(geoJsonPoint.properties.popup);
            }
            if (geoJsonPoint.properties.tooltip) {
                marker.bindTooltip(geoJsonPoint.properties.tooltip);
            }
            return marker;
        }
    }).addTo(mapElement);
    mapElement.fitBounds(geojsonLayer.getBounds(), {
        padding: [100, 100]
    });
};

window.addEventListener('load', initOpenStreetMap);
