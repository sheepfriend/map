<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head lang="<?php echo $str_language; ?>" xml:lang="<?php echo $str_language; ?>">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>MAMP PRO</title>
<style type="text/css">
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: .9em;
        color: #000000;
        background-color: #FFFFFF;
        margin: 0;
        padding: 10px 20px 20px 20px;
    }

    samp {
        font-size: 1.3em;
    }

    a {
        color: #000000;
        background-color: #FFFFFF;
    }

    sup a {
        text-decoration: none;
    }

    hr {
        margin-left: 90px;
        height: 1px;
        color: #000000;
        background-color: #000000;
        border: none;
    }

    #logo {
        margin-bottom: 10px;
        margin-left: 28px;
    }

    .text {
        width: 80%;
        margin-left: 90px;
        line-height: 140%;
    }
    #mapid { height: 180px; }
</style>
</head>

<body>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
  integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
  crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"
  integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
  crossorigin=""></script>
  <script src="json_source/bicycle_thefts_Part1.geojson.txt" ></script>

   <div id="mapid"></div>
    

  <script type="text/javascript">
    var tmp;
    var mymap = L.map('mapid').setView([39.924273321000044,-75.16977545399999], 13).on({
                'click':function(){
                    //clear data?
                    a.clearLayers();
                    a.addData(newdata);
                    console.log(123)
                },
                'zoomanim':function(){
                    //request data
                    //reshape current data
                    console.log(this.getBounds());tmp=this;

                }
            });

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1Ijoic2hlZXBmcmllbmQiLCJhIjoiY2pqa2x1Z3k3NjA3ZzNwcjVuNW5sMmpmcSJ9.cMg7_umE-NlH4A767Y8KfQ'
    }).addTo(mymap);



    function onEachFeature(feature, layer) {
    // does this feature have a property named popupContent?
    layer.bindPopup(feature.properties.THEFT_DATE);
        layer.on({
            baselayerchange: function(){console.log(123);}
         })
    }

    var geojsonMarkerOptions = {
    radius: 8,
    fillColor: "#ff7800",
    color: "#000",
    weight: 1,
    opacity: 1,
    fillOpacity: 0.8
    };


    var a=L.geoJSON(geoFeatures,{
        onEachFeature: onEachFeature,
        pointToLayer: function (feature, latlng) {
        console.log(latlng);
        return L.circleMarker(latlng, geojsonMarkerOptions);
    }
    }).addTo(mymap);


  </script>
<?php echo 123;?>
</body>
</html>