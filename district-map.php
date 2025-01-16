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

    

document.addEventListener('DOMContentLoaded', () => {
    mapboxgl.accessToken = 'pk.eyJ1IjoiMzY1YWRtaW4iLCJhIjoiY201d3diMXVyMDJteTJxb2xyd2xsdTl3aSJ9._bG3aqw05sfgNZFXKWMxrA';

    // Set default visibility for layers
    const isCityView = true; // Set this to `true` for the city website and `false` for the county website

    // Default visibility states
    const defaultCityVisibility = isCityView ? 'visible' : 'none';
    const defaultCountyVisibility = isCityView ? 'none' : 'visible';

    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [-82.375, 31.205], // Adjust for your area
        zoom: 12.7
    });

    // Define your GeoJSON files
    const coordFiles = [
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

    const countyPropertyFiles = [
        './popup-data/county/county1.json',
        './popup-data/county/county2.json',
        './popup-data/county/county3.json',
        './popup-data/county/county4.json'
    ];

    // Load GeoJSON data and add to map
    Promise.all(coordFiles.map(file => fetch(file).then(res => res.json())))
        .then(geojsonList => {
            // Combine all districts into one GeoJSON source
            const combinedGeoJSON = {
                type: 'FeatureCollection',
                features: geojsonList.flatMap(data => data.features)
            };

            map.on('load', () => {
                // Add the districts source
                map.addSource('districts', {
                    type: 'geojson',
                    data: combinedGeoJSON
                });

                // Add districts fill layer
                map.addLayer({
                    id: 'districts-layer',
                    type: 'fill',
                    source: 'districts',
                    paint: {
                        'fill-color': [
                            'match',
                            ['get', 'district'],
                            'District 1', '#9C9C9C',
                            'District 2', '#38A800',
                            'District 3', '#005CE6',
                            'District 4', '#FF0000',
                            'District 5', '#FFCA00',
                            '#FFFFFF' // Default color
                        ],
                        'fill-opacity': 0.5
                    }
                });

                map.addLayer({
                    id: 'districts-outline',
                    type: 'line',
                    source: 'districts', // Use the same source as the fill layer
                    paint: {
                        'line-color': '#333', // Outline color
                        'line-width': 1 // Outline thickness
                    }
                });

                // Add district labels
                map.addLayer({
                    id: 'district-labels',
                    type: 'symbol',
                    source: 'districts',
                    layout: {
                        'text-field': ['get', 'district'],
                        'text-size': 16,
                        'text-offset': [0, 0.5],
                        'text-anchor': 'top'
                    },
                    paint: {
                        'text-color': '#000',
                        'text-halo-color': '#FFF',
                        'text-halo-width': 1
                    }
                });

                // Hover effect layer
                map.addLayer({
                    id: 'districts-hover',
                    type: 'fill',
                    source: 'districts',
                    paint: {
                        'fill-color': '#FFD700', // Highlight color
                        'fill-opacity': 0.8
                    },
                    filter: ['==', 'district', ''] // No district highlighted initially
                });

                // Handle mouse hover
                map.on('mousemove', 'districts-layer', (e) => {
                    const districtName = e.features[0].properties.district;
                    map.setFilter('districts-hover', ['==', 'district', districtName]);
                    map.getCanvas().style.cursor = 'pointer';
                });

                map.on('mouseleave', 'districts-layer', () => {
                    map.setFilter('districts-hover', ['==', 'district', '']);
                    map.getCanvas().style.cursor = '';
                });

                // Handle popups with additional data
                const propertyFiles = [
                    './popup-data/district1.json',
                    './popup-data/district2.json',
                    './popup-data/district3.json',
                    './popup-data/district4.json',
                    './popup-data/district5.json'
                ];

                Promise.all(propertyFiles.map(file => fetch(file).then(res => res.json())))
                    .then(propertiesList => {
                        const propertiesMap = Object.fromEntries(
                            propertiesList.map(props => [props.district, props])
                        );

                        map.on('click', 'districts-layer', (e) => {
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
                                    <strong>${districtName}</strong><br>
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
                            }
                        });
                    })
                    .catch(error => console.error('Error loading popup data:', error));

                const cityDistrictsLayer = map.getLayer('districts-layer');
                if (cityDistrictsLayer) {
                    document.getElementById('city-districts').addEventListener('change', (e) => {
                        const visibility = e.target.checked ? 'visible' : 'none';
                        console.log(`Setting city districts visibility to: ${visibility}`);

                        map.setLayoutProperty('districts-layer', 'visibility', visibility);
                        map.setLayoutProperty('districts-outline', 'visibility', visibility);
                        map.setLayoutProperty('district-labels', 'visibility', visibility);
                    });
                } else {
                    console.error('City districts layer not found.');
                }
            });
        })
        .catch(error => console.error('Error loading GeoJSON files:', error));



        let combinedCountyGeoJSON;
        let propertiesMap;

        // Load GeoJSON and property files
        const loadCountyData = async () => {
            try {
                const geojsonList = await Promise.all(
                    countyCoordFiles.map(async (file) => {
                        const res = await fetch(file);
                        if (!res.ok) {
                            throw new Error(`Failed to fetch ${file}: ${res.statusText}`);
                        }
                        const data = await res.json();
                        console.log(`Fetched ${file} successfully:`, data);
                        return data;
                    })
                );
                combinedCountyGeoJSON = {
                    type: 'FeatureCollection',
                    features: geojsonList.flatMap(data => data.features)
                };

                console.log('County GeoJSON source added:', combinedCountyGeoJSON);

                const propertiesList = await Promise.all(
                    countyPropertyFiles.map(file => fetch(file).then(res => res.json()))
                );
                propertiesMap = Object.fromEntries(propertiesList.map(props => [props.district, props]));
            } catch (error) {
                console.error('Error loading county GeoJSON or property files:', error);
            }
        };

        // Call loadCountyData before adding layers
        (async () => {
            await loadCountyData();

            map.on('load', () => {
                console.log('Adding county districts layer...');
                console.log('County GeoJSON:', JSON.stringify(combinedCountyGeoJSON, null, 2));

                map.addSource('county-districts', {
                    type: 'geojson',
                    data: combinedCountyGeoJSON
                });

                map.addLayer({
                    id: 'county-districts-layer',
                    type: 'fill',
                    source: 'county-districts',
                    paint: {
                        'fill-color': [
                            'match',
                            ['get', 'district'],
                            'County 1', '#FF5733', // Example color for County 1
                            'County 2', '#33FF57', // Example color for County 2
                            'County 3', '#3357FF', // Example color for County 3
                            'County 4', '#FF33A1', // Example color for County 4
                            '#FFFFFF' // Default color
                        ],
                        'fill-opacity': 0.5
                    }
                });

                map.addLayer({
                    id: 'county-districts-outline',
                    type: 'line',
                    source: 'county-districts',
                    paint: {
                        'line-color': '#000',
                        'line-width': 2
                    }
                });

                map.addLayer({
                    id: 'county-districts-labels',
                    type: 'symbol',
                    source: 'county-districts',
                    layout: {
                        'text-field': ['get', 'district'],
                        'text-size': 16,
                        'text-offset': [0, 0.5],
                        'text-anchor': 'top',
                        visibility: 'visible'
                    },
                    paint: {
                        'text-color': '#000',
                        'text-halo-color': '#FFF',
                        'text-halo-width': 1
                    }
                });

                map.addLayer({
                    id: 'county-districts-hover',
                    type: 'fill',
                    source: 'county-districts',
                    paint: {
                        'fill-color': '#FFD700', // Highlight color
                        'fill-opacity': 0.8
                    },
                    filter: ['==', 'district', ''] // No district highlighted initially
                });

                map.addLayer({
                    id: 'county-districts-hover-outline',
                    type: 'line',
                    source: 'county-districts',
                    paint: {
                        'line-color': '#FFD700',
                        'line-width': 4
                    },
                    filter: ['==', 'district', ''] // No district highlighted initially
                });

                map.on('click', 'county-districts-layer', (e) => {
                    const districtName = e.features[0].properties.district;
                    const districtProps = propertiesMap[districtName];

                    if (districtProps) {
                        const phoneHtml = districtProps.phone
                            ? `<p>Phone: ${districtProps.phone}</p>`
                            : '';

                        const emailHtml = districtProps.email
                            ? `<p>Email: <a href="mailto:${districtProps.email}" style="color: blue;">${districtProps.email}</a></p>`
                            : '';

                        const popupContent = `
                            <div style="text-align: center;">
                                <strong>${districtName}</strong><br>
                                <p>Population: ${districtProps.population}</p>
                                <p>Commissioner: ${districtProps.representative}</p>
                                ${phoneHtml}
                                ${emailHtml}
                            </div>
                        `;
                        new mapboxgl.Popup()
                            .setLngLat(e.lngLat)
                            .setHTML(popupContent)
                            .addTo(map);
                    }
                });

                // Hover and popup logic
                map.on('mousemove', 'county-districts-layer', (e) => {
                    if (!e.features || !e.features[0] || !e.features[0].properties) return;
                    const districtName = e.features[0].properties.district;
                    map.setFilter('county-districts-hover', ['==', 'district', districtName]);
                    map.setFilter('county-districts-hover-outline', ['==', 'district', districtName]);
                    map.getCanvas().style.cursor = 'pointer';
                });

                map.on('mouseleave', 'county-districts-layer', () => {
                    map.setFilter('county-districts-hover', ['==', 'district', '']);
                    map.setFilter('county-districts-hover-outline', ['==', 'district', '']);
                    map.getCanvas().style.cursor = '';
                });

                // Add toggle visibility
                document.getElementById('county-districts').addEventListener('change', (e) => {
                    const visibility = e.target.checked ? 'visible' : 'none';
                    console.log(`Setting county districts visibility to: ${visibility}`);
                    map.setLayoutProperty('county-districts-layer', 'visibility', visibility);
                    map.setLayoutProperty('county-districts-outline', 'visibility', visibility);
                    map.setLayoutProperty('county-districts-labels', 'visibility', visibility);
                });
            });
        })();
})
</script>
</body>
</html>