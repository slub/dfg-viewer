/*!

    Custom OpenLayers styling for DFG viewer
    ----------------------------------------
    Mainly new styles for hover and select
    interactions with the canvas object.


!*/
/*global ol, dlfViewerOLStyles*/
/**
 * @return {ol.style.Style}
 */
dlfViewerOLStyles.hoverStyle = function () {

    return new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: "rgba(255,255,255,.3)",
            width: 2,
        }),
        fill: new ol.style.Fill({
            color: "rgba(104, 135, 163,.3)",
        })
    });

};

/**
 * @return {ol.style.Style}
 */
dlfViewerOLStyles.selectStyle = function () {

    return new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: "rgba(255,255,255,1)",
            width: 2
        }),
        fill: new ol.style.Fill({
            color: "rgba(255,154,35,.3)",
        })
    });

};
