<script type="text/javascript" src="https://www.google.com/jsapi?key=AIzaSyDzu7YD4AQdJJDMLG_rFzLkpCybg6_vLRM"></script>
 <div id="yourinfo"></div>
 <script type="text/javascript">
        if(google.loader.ClientLocation)
        {
            visitor_lat = google.loader.ClientLocation.latitude;
            visitor_lon = google.loader.ClientLocation.longitude;
            visitor_city = google.loader.ClientLocation.address.city;
            visitor_region = google.loader.ClientLocation.address.region;
            visitor_country = google.loader.ClientLocation.address.country;
            visitor_countrycode = google.loader.ClientLocation.address.country_code;
            document.getElementById('yourinfo').innerHTML = '<p>Lat/Lon: ' + visitor_lat + ' / ' + visitor_lon + '</p><p>Location: ' + visitor_city + ', ' + visitor_region + ', ' + visitor_country + ' (' + visitor_countrycode + ')</p>';
        }
        else
        {
            document.getElementById('yourinfo').innerHTML = '<p>Whoops!</p>';
        }
    </script>
