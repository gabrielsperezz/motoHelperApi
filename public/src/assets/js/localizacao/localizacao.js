(function ($) {

    var mapa = L.map(document.getElementById('localizacao_map'));

    L.Icon.Default.imagePath = 'assets/img/theme/vendor/leaflet/dist/images';

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(mapa);

    var markersGroup = L.layerGroup().addTo(mapa);

    mapa.setView([-22.215403, -49.654070], 15);
    var marker = L.marker([-22.215403, -49.654070], {
        icon: L.AwesomeMarkers.icon({
            icon: '',
            markerColor: 'blue',
            prefix: 'fa'
        }),
        draggable: true
    }).addTo(markersGroup);

    marker.on('dragend', function(evento) {

        $.ajax({
            url : '/api/mobile/login/' + $("#usuario").val() + '/ultimaposicao',
            dataType : 'json',
            type : 'put',
            data : {latitude : evento.target.getLatLng().lat,longitude : evento.target.getLatLng().lng},
            error : function (error) {
                e(error);
            },
            success : function (data) {
                s(data);
            }
        });
    });

})(jQuery);


