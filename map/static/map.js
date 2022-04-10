/* Leaflet Map JS */

BASECOORDS = [49.817492,15.472962];

var map = L.map('map').setView(BASECOORDS, 6);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);


var marker =  L.marker([49.817492,15.472962]).addTo(map);

marker.bindPopup('<b>Marker</b><br>Mapový modul pro <a target="_blank" href="http://vufind2-dev.ucl.cas.cz.">VuFind</a>.').openPopup();

