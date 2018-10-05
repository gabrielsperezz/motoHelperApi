(function ($) {
    
    $(document).ready(function(){
        
      var table =  $('#table-eventos').DataTable( {
            processing:true,
            paging: false,
            lengthChange: false,
            searching: false,
            ordering: false,
            info: false,
            responsive: true,
            autoWidth: false,
            serverSide: true,
            ajax: "/api/v1/home/eventos",
            scrollY:'30vh',
            scrollCollapse: true,
            language:language_datatable
        } );
        $("#reload_last_event").on("click",function(){
           table.ajax.reload();
        });
        $(".paineis_desconectados").click(function () {
            window.location = "/painel/desconectado";
        });
        $(".paineis_todos").click(function () {
            window.location = "/painel/form/busca";
        });
    });
})(jQuery);







