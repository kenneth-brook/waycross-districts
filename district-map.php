<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0" />
    <title>City of Waycross</title>
    <meta name="format-detection" content="telephone=no" />
    <meta name="Keywords" content="" />
    <meta description="Description"
        content="The Mayor and City Council of Waycross, Georgia set policy and ordinances for Waycross." />
    <?php include "includes/header-code.txt"; ?>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.9.2/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.9.2/mapbox-gl.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf"></script>

    <style>
        #map-container {
    position: relative;
    width: 100%;
    height: 900px;
}

#map {
    width: 100%;
    height: 100%;
}

#layer-controls {
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(255, 255, 255, 0.9);
    padding: 10px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    display: flex;
    gap: 10px;
}

#layer-controls label {
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
}
    </style>
</head>

<body>
    <div id="page" class="site page-mayor-city-commission">
        <?php $header_image = "images/headers/city-hall.jpg"; ?>
        <?php include "includes/header.php"; ?>
        <div class="header-contact d-none cushycms" title="Header address"><div>
            <p>Phone: <strong>912-287-2944</strong></p>
        </div>
    </div>

    <main id="content" class="site-content clearfix" role="main">
        <div class="container">
            <div class="content-header">
                <div class="content-header-right">
                    <?php include "includes/header-buttons.php"; ?>
                </div>
                    <div class="content-header-left cushycms" title="Page header"><h2 class="main-title large mt-xl-2 mb-6">District Map</h2>
                </div>
            </div>
            <div id="map-container">
                <div id="map"></div>
                <div id="layer-controls">
                    <div class="distToggle">
                        <label>
                            <input type="checkbox" id="city-districts" checked>
                            City Districts
                        </label>
                        <label>
                            <input type="checkbox" id="county-districts" checked>
                            County Districts
                        </label>
                    </div>
                    <div id="search-container">
                        <input type="text" id="address-input" placeholder="Enter your address">
                        <button id="search-button" style="padding: 5px;">Find My Districts</button>
                        <p id="search-result" style="margin-top: 10px; font-size: 14px;"></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="results-popup" style="display: none;">
            <div id="popup-content">
                <button id="close-popup" style="float: right;">&times;</button>
                <div id="popup-results"></div>
            </div>
        </div>
    </main>
    <?php include "includes/footer.php"; ?>
    <?php include "includes/footer-code.txt"; ?>

<script>
    const openBtns = document.querySelectorAll('.openBtn');
    const popups = document.querySelectorAll('.popup');
    const closeBtns = document.querySelectorAll('.closeBtn');

    openBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = btn.getAttribute('data-target');
            document.getElementById(target).style.display = 'block';
            overlay.style.display = 'block';
        });
    });

    closeBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = btn.getAttribute('data-target');
            document.getElementById(target).style.display = 'none';
            overlay.style.display = 'none';
        });
    });

    const isCityView = true; // Set to `true` for city website, `false` for county website
    const defaultCityVisibility = isCityView ? 'visible' : 'none';
    const defaultCountyVisibility = isCityView ? 'none' : 'visible';
    let cityGeoJSON = null;  // Global variable for city GeoJSON
    let countyGeoJSON = null; // Global variable for county GeoJSON
    let cityProperties = {};
    let countyProperties = {};

    document.addEventListener('DOMContentLoaded', () => {
        mapboxgl.accessToken = 'pk.eyJ1IjoiMzY1YWRtaW4iLCJhIjoiY201d3diMXVyMDJteTJxb2xyd2xsdTl3aSJ9._bG3aqw05sfgNZFXKWMxrA';

        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [-82.355, 31.205], // Default center
            zoom: 12.5 // Default zoom
        });

        const cityCoordFiles = [
            './coord-data/district1.geojson',
            './coord-data/district2.geojson',
            './coord-data/district3.geojson',
            './coord-data/district4.geojson',
            './coord-data/district5.geojson'
        ];

        const countyCoordFiles = [
            './coord-data/county/county1.geojson',
            './coord-data/county/county2.geojson',
            './coord-data/county/county3.geojson',
            './coord-data/county/county4.geojson'
        ];

        const cityPropertyFiles = [
            './popup-data/district1.json',
            './popup-data/district2.json',
            './popup-data/district3.json',
            './popup-data/district4.json',
            './popup-data/district5.json'
        ];

        const countyPropertyFiles = [
            './popup-data/county/county1.json',
            './popup-data/county/county2.json',
            './popup-data/county/county3.json',
            './popup-data/county/county4.json'
        ];

        const cityViewSettings = { center: [-82.355, 31.205], zoom: 12.5 };
        const countyViewSettings = { center: [-82.400, 31.050], zoom: 9 };

        const addDistrictLayers = async (geoJSONFiles, propertyFiles, sourceId, layerIdPrefix, paintStyles, defaultVisibility) => {
            const geoJSONData = {
                type: 'FeatureCollection',
                features: (await Promise.all(geoJSONFiles.map(file => fetch(file).then(res => res.json())))).flatMap(data => data.features)
            };

            if (sourceId === 'city-districts') {
                cityGeoJSON = geoJSONData; // Store city GeoJSON globally
            } else if (sourceId === 'county-districts') {
                countyGeoJSON = geoJSONData; // Store county GeoJSON globally
            }

            const propertiesList = await Promise.all(propertyFiles.map(file => fetch(file).then(res => res.json())));
            const propertiesMap = Object.fromEntries(propertiesList.map(props => [props.district, props]));

            if (sourceId === 'city-districts') {
                cityProperties = propertiesMap;
            } else if (sourceId === 'county-districts') {
                countyProperties = propertiesMap;
            }

            map.addSource(sourceId, { type: 'geojson', data: geoJSONData });

            map.addLayer({
                id: `${layerIdPrefix}-layer`,
                type: 'fill',
                source: sourceId,
                paint: paintStyles.fill,
                layout: { visibility: defaultVisibility }
            });

            map.addLayer({
                id: `${layerIdPrefix}-outline`,
                type: 'line',
                source: sourceId,
                paint: paintStyles.outline,
                layout: { visibility: defaultVisibility }
            });

            map.addLayer({
                id: `${layerIdPrefix}-labels`,
                type: 'symbol',
                source: sourceId,
                layout: {
                    'text-field': ['get', 'district'],
                    'text-size': 16,
                    'text-offset': [0, 0.5],
                    'text-anchor': 'top',
                    'visibility': defaultVisibility
                },
                paint: {
                    'text-color': '#000',
                    'text-halo-color': '#FFF',
                    'text-halo-width': 1
                }
            });

            map.addLayer({
                id: `${layerIdPrefix}-hover`,
                type: 'fill',
                source: sourceId,
                paint: {
                    'fill-color': '#FFD700', // Highlight color
                    'fill-opacity': 0.8
                },
                filter: ['==', 'district', ''] // No district highlighted initially
            });

            // Hover effects
            map.on('mousemove', `${layerIdPrefix}-layer`, (e) => {
                const districtName = e.features[0].properties.district;
                map.setFilter(`${layerIdPrefix}-hover`, ['==', 'district', districtName]);
                map.getCanvas().style.cursor = 'pointer';
            });

            map.on('mouseleave', `${layerIdPrefix}-layer`, () => {
                map.setFilter(`${layerIdPrefix}-hover`, ['==', 'district', '']);
                map.getCanvas().style.cursor = '';
            });

            // Popups
            map.on('click', `${layerIdPrefix}-layer`, (e) => {
                const districtName = e.features[0].properties.district;
                const districtProps = propertiesMap[districtName];

                if (districtProps) {
                    const pictureHtml = districtProps.Picture
                        ? `<img src="${districtProps.Picture}" alt="${districtProps.representative}" style="width: 150px; height: auto; border-radius: 8px; margin-top: 8px;">`
                        : `<p style="font-style: italic; color: gray;">No picture available</p>`;

                    const phoneHtml = districtProps.phone
                        ? `<p>Phone: ${districtProps.phone}</p>`
                        : '';

                    const emailHtml = districtProps.email
                        ? `<p>Email: <a href="mailto:${districtProps.email}" style="color: blue;">${districtProps.email}</a></p>`
                        : '';

                    const popupContent = `
                        <div style="text-align: center;">
                            <strong>${districtProps.tag}</strong><br>
                            <p>Population: ${districtProps.population}</p>
                            ${pictureHtml}
                            <p>Commissioner: ${districtProps.representative}</p>
                            ${phoneHtml}
                            ${emailHtml}
                        </div>
                    `;
                    new mapboxgl.Popup()
                        .setLngLat(e.lngLat)
                        .setHTML(popupContent)
                        .addTo(map);
                } else {
                    console.warn(`No properties found for district: ${districtName}`);
                }
            });
            return geoJSONData;
        };

        map.on('load', async () => {
            const cityGeoJSONData = await addDistrictLayers(
                cityCoordFiles,
                cityPropertyFiles,
                'city-districts',
                'city-districts',
                {
                    fill: {
                        'fill-color': [
                            'match',
                            ['get', 'district'],
                            'District 1', '#9C9C9C',
                            'District 2', '#38A800',
                            'District 3', '#005CE6',
                            'District 4', '#FF0000',
                            'District 5', '#FFCA00',
                            '#FFFFFF'
                        ],
                        'fill-opacity': 0.5
                    },
                    outline: { 'line-color': '#333', 'line-width': 1 }
                },
                defaultCityVisibility
            );

            const countyGeoJSONData = await addDistrictLayers(
                countyCoordFiles,
                countyPropertyFiles,
                'county-districts',
                'county-districts',
                {
                    fill: {
                        'fill-color': [
                            'match',
                            ['get', 'district'],
                            'County 1', '#FF5733',
                            'County 2', '#33FF57',
                            'County 3', '#3357FF',
                            'County 4', '#FF33A1',
                            '#FFFFFF'
                        ],
                        'fill-opacity': 0.5
                    },
                    outline: { 'line-color': '#000', 'line-width': 2 }
                },
                defaultCountyVisibility
            );

            // Initialize checkbox states
            document.getElementById('city-districts').checked = isCityView;
            document.getElementById('county-districts').checked = !isCityView;

            const toggleLayerVisibility = (toggleId, layerIdsToShow, layerIdsToHide, centerZoomSettings) => {
                document.getElementById(toggleId).addEventListener('change', (e) => {
                    const visibility = e.target.checked ? 'visible' : 'none';

                    layerIdsToShow.forEach(layerId => map.setLayoutProperty(layerId, 'visibility', visibility));
                    layerIdsToHide.forEach(layerId => map.setLayoutProperty(layerId, 'visibility', 'none'));

                    if (e.target.checked) {
                        map.flyTo(centerZoomSettings);
                    }

                    // Synchronize checkboxes
                    document.getElementById(
                        toggleId === 'city-districts' ? 'county-districts' : 'city-districts'
                    ).checked = !e.target.checked;
                });
            };

            toggleLayerVisibility(
                'city-districts',
                ['city-districts-layer', 'city-districts-outline', 'city-districts-labels'],
                ['county-districts-layer', 'county-districts-outline', 'county-districts-labels'],
                cityViewSettings
            );

            toggleLayerVisibility(
                'county-districts',
                ['county-districts-layer', 'county-districts-outline', 'county-districts-labels'],
                ['city-districts-layer', 'city-districts-outline', 'city-districts-labels'],
                countyViewSettings
            );
        });

        const geocodeAddress = async (address) => {
            const geocodeUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(address)}.json?access_token=${mapboxgl.accessToken}`;
            try {
                const response = await fetch(geocodeUrl);
                const data = await response.json();
                if (data.features && data.features.length > 0) {
                    const [lng, lat] = data.features[0].geometry.coordinates;
                    return { lng, lat };
                } else {
                    throw new Error('No results found for the entered address.');
                }
            } catch (error) {
                console.error('Geocoding error:', error);
                return null;
            }
        };

        const findDistrict = (point, geoJSON) => {
            const userPoint = turf.point([point.lng, point.lat]);
            for (const feature of geoJSON.features) {
                if (turf.booleanPointInPolygon(userPoint, feature)) {
                    return feature.properties.district;
                }
            }
            return null; // No district found
        };

        document.getElementById('search-button').addEventListener('click', async () => {
            const address = document.getElementById('address-input').value;
            const popupElement = document.getElementById('results-popup');
            const popupResults = document.getElementById('popup-results');

            if (!address) {
                alert('Please enter an address.');
                return;
            }

            const coordinates = await geocodeAddress(address);
            if (!coordinates) {
                alert('Unable to find the address. Please try again.');
                return;
            }

            // Function to find the district from GeoJSON
            const findDistricts = (coords, geoJSON, districtType) => {
                const districts = [];
                for (const feature of geoJSON.features) {
                    if (turf.booleanPointInPolygon(coords, feature)) {
                        districts.push({ district: feature.properties.district, type: districtType });
                    }
                }
                return districts;
            };

            const point = turf.point([coordinates.lng, coordinates.lat]);
            const cityResults = findDistricts(point, cityGeoJSON, 'City');
            const countyResults = findDistricts(point, countyGeoJSON, 'County');

            const cityMayor = {
                name: "Dr. Michael-Angelo James",
                phone: "(912) 722-1366",
                email: "majames@waycrossga.gov",
                picture: "https://www.waycrossga.gov/uploads/mayor-city-commission_2_3186562038.jpg"
            };

            // Chairperson details for the county
            const countyChairperson = {
                name: "Elmer Thrift",
                phone: "(912) 548-7253",
                email: "",
                picture: ""
            };

            const mayorPictureHtml = cityMayor.picture
                ? `<img src="${cityMayor.picture}" alt="${cityMayor.representative}">`
                : `<p style="font-style: italic; color: gray;">No picture available</p>`;

            const mayorPhoneHtml = cityMayor.phone
                ? `<p>Phone: ${cityMayor.phone}</p>`
                : '';

            const mayorEmailHtml = cityMayor.email
                ? `<p>Email: <a href="mailto:${cityMayor.email}" style="color: blue;">${cityMayor.email}</a></p>`
                : '';

            const chairPictureHtml = countyChairperson.picture
                ? `<img src="${countyChairperson.picture}" alt="${countyChairperson.representative}">`
                : `<p style="font-style: italic; color: gray;">No picture available</p>`;

            const chairPhoneHtml = countyChairperson.phone
                ? `<p>Phone: ${countyChairperson.phone}</p>`
                : '';

            const chairEmailHtml = countyChairperson.email
                ? `<p>Email: <a href="mailto:${countyChairperson.email}" style="color: blue;">${countyChairperson.email}</a></p>`
                : '';

            if (cityResults.length > 0 || countyResults.length > 0) {
                popupResults.innerHTML = ''; // Clear previous results

                // Append city results
                cityResults.forEach(result => {
                    const districtProps = cityProperties[result.district];
                    const pictureHtml = districtProps.Picture
                        ? `<img src="${districtProps.Picture}" alt="${districtProps.representative}">`
                        : `<p style="font-style: italic; color: gray;">No picture available</p>`;

                    const phoneHtml = districtProps.phone
                        ? `<p>Phone: ${districtProps.phone}</p>`
                        : '';

                    const emailHtml = districtProps.email
                        ? `<p>Email: <a href="mailto:${districtProps.email}" style="color: blue;">${districtProps.email}</a></p>`
                        : '';

                    popupResults.innerHTML += `
                        <h3 style="font-size: 28px;">City District: ${result.district}</h3>
                        <div style="display: flex; align-items: flex-end;">
                        <div style="padding: 10px;">
                        ${mayorPictureHtml}
                        <p>Mayor: ${cityMayor.name}</p>
                        ${mayorPhoneHtml}
                        ${mayorEmailHtml}
                        </div>
                        <div style="padding: 10px;">
                        ${pictureHtml}
                        <p>Commissioner: ${districtProps.representative}</p>
                        ${phoneHtml}
                        ${emailHtml}
                        </div>
                        </div>
                        <hr>
                    `;
                });

                // Append county results
                countyResults.forEach(result => {
                    const districtProps = countyProperties[result.district];
                    const pictureHtml = districtProps.Picture
                        ? `<img src="${districtProps.Picture}" alt="${districtProps.representative}">`
                        : `<p style="font-style: italic; color: gray;">No picture available</p>`;

                    const phoneHtml = districtProps.phone
                        ? `<p>Phone: ${districtProps.phone}</p>`
                        : '';

                    const emailHtml = districtProps.email
                        ? `<p>Email: <a href="mailto:${districtProps.email}" style="color: blue;">${districtProps.email}</a></p>`
                        : '';

                    popupResults.innerHTML += `
                        <h3 style="font-size: 28px;">County District: ${result.district}</h3>
                        <div style="display: flex; align-items: flex-end;">
                        <div style="padding: 10px;">
                        ${chairPictureHtml}
                        <p>Commission Chair: ${countyChairperson.name}</p>
                        ${chairPhoneHtml}
                        ${chairEmailHtml}
                        </div>
                        <div style="padding: 10px;">
                        ${pictureHtml}
                        <p>Commissioner: ${districtProps.representative}</p>
                        ${phoneHtml}
                        ${emailHtml}
                        </div>
                        </div>
                        <hr>
                    `;
                });

                popupElement.style.display = 'block';

                // Center map on the searched location
                map.flyTo({ center: [coordinates.lng, coordinates.lat], zoom: 14 });

                // Add a marker for the searched location
                new mapboxgl.Marker()
                    .setLngLat([coordinates.lng, coordinates.lat])
                    .addTo(map);
            } else {
                popupResults.innerHTML = 'Address is not within any district.';
                popupElement.style.display = 'block';
            }
        });

        // Close the popup
        document.getElementById('close-popup').addEventListener('click', () => {
            document.getElementById('results-popup').style.display = 'none';
        });

    });

</script>
</body>
</html>