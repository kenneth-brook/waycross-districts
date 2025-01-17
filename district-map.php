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
                    <label>
                        <input type="checkbox" id="city-districts" checked>
                        City Districts
                    </label>
                    <label>
                        <input type="checkbox" id="county-districts" checked>
                        County Districts
                    </label>
                    <label>
                        <input type="checkbox" id="county-borders" checked>
                        County Borders
                    </label>
                </div>
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

    document.addEventListener('DOMContentLoaded', () => {
        mapboxgl.accessToken = 'pk.eyJ1IjoiMzY1YWRtaW4iLCJhIjoiY201d3diMXVyMDJteTJxb2xyd2xsdTl3aSJ9._bG3aqw05sfgNZFXKWMxrA';

        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [-82.375, 31.205], // Default center
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

        const cityViewSettings = { center: [-82.375, 31.205], zoom: 12.5 };
        const countyViewSettings = { center: [-82.400, 31.050], zoom: 9 };

        const addDistrictLayers = async (geoJSONFiles, propertyFiles, sourceId, layerIdPrefix, paintStyles, defaultVisibility) => {
            const geoJSONData = {
                type: 'FeatureCollection',
                features: (await Promise.all(geoJSONFiles.map(file => fetch(file).then(res => res.json())))).flatMap(data => data.features)
            };

            const propertiesList = await Promise.all(propertyFiles.map(file => fetch(file).then(res => res.json())));
            const propertiesMap = Object.fromEntries(propertiesList.map(props => [props.district, props]));

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
    });

</script>
</body>
</html>