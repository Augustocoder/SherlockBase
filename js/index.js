$(document).ready(function() {
    $("#buscar").submit(function(event) {
        event.preventDefault();
        var cpf = $("input[name='cpf_cnpj']").val();
        $("#carregando").show();
        $.ajax({
            type: "GET",
            url: "consultar.php?cpf_cnpj=" + cpf,
            dataType: "text",
        })
        .done(function(msg) {
            $("#carregando").hide();
            $("#result").html('<h2>Resultado:</h2>');
            $("#result").html(msg);
        })
        .fail(function(jqXHR, textStatus, msg) {
            $("#carregando").hide();
            alert(msg);
        });
    });
  });