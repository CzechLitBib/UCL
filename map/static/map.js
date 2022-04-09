/* Leaflet Map JS */

BASECOORDS = [49.817492,15.472962];

var map = L.map('map').setView(BASECOORDS, 6);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);

var layer = L.layerGroup();

fetch('http://vufind2-dev.ucl.cas.cz/map/data')
    .then(res => res.json())
    .then((out) => {
        console.log('Output: ', out);
}).catch(err => console.error(err));
