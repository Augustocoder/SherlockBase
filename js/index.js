  let btn = document.getElementById("enviar");
  btn.addEventListener("click", () => {
    let cpf_cnpj = document.getElementById("cpf_cnpj").value;
    fetch('api/', {
      'method': 'POST',
      headers: {
        'Accept' : 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({'cpf_cnpj': cpf_cnpj}),
    })
    .then(response => response.json())
    .then(data => {
       console.log(data);
     })
    
  });
