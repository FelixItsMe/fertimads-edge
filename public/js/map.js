const fillPosition = (latitude, longitude, idLatitude, idLongitude) => {
    document.getElementById(idLatitude).value = latitude
    document.getElementById(idLongitude).value = longitude
}

const fillPolygon = (polygon, idPolygon) => {
    document.getElementById(idPolygon).value = polygon
}

const initPolygon = (layerMap, arrayPolygon, setting = {}) => {
    return L.polygon(arrayPolygon, setting)
        .addTo(layerMap)
}

const initMarker = (layerMap, latitude, longitude) => {
    return L.marker([latitude, longitude], {
            draggable: false
        })
        .addTo(layerMap)
}
