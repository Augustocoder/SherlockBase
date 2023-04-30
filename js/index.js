$(document).ready(function () {
  $("#buscar").submit(function (event) {
    event.preventDefault();
    var cpf = $("input[name='cpf_cnpj']").val();
    $("#carregando").show();
    $("#icon").hide();

    $.ajax({
      type: "GET",
      url: "consultar.php?cpf_cnpj=" + cpf,
      dataType: "text",
    })
      .done(function (msg) {
        $("#carregando").hide();
        $("#icon").show();
        $("#info").hide();
        $("#result").html(msg);
      })
      .fail(function (jqXHR, textStatus, msg) {
        $("#carregando").hide();
        alert(msg);
      });
  });
});
