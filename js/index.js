let btn = document.getElementById("enviar");
btn.addEventListener("click", () => {
  let cpf_cnpj = document.getElementById("cpf_cnpj").value;
  cpf_cnpj = cpf_cnpj
    .replaceAll(".", "")
    .replaceAll("-", "")
    .replaceAll("/", "");
  let errDiv = document.getElementById("error");
  let resultDiv = document.getElementById("result");

  btn.disabled = true;
  btn.innerHTML = '<img  class="w-6 h-6" src="/images/loading.gif">';

  if (cpf_cnpj.length === 14 || cpf_cnpj.length === 11) {
    fetch("api/", {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ cpf_cnpj: cpf_cnpj }),
    })
      .then((response) => response.json())
      .then((data) => {
        errDiv.style.display = "none";
        if (cpf_cnpj.length === 14) {
          let nomeDefine = data.nome;
          if (nomeDefine) {
            let socios =
              data.qsa && data.qsa.length > 0
                ? data.qsa[0].nome
                : "Não Informado";
            let name = data.nome || "Não Informado";
            let telefone = data.telefone || "Não Informado";
            let porte = data.porte || "Não Informado";
            let capital_social = data.capital_social || "Não Informado";
            let email = data.email || "Não Informado";
            if (data.status != "error") {
              resultDiv.innerHTML = `
                        <div class="bg-gray-100 rounded-lg p-4">
                            <p class="text-indigo-700 font-bold">Razão Social:</p>
                            <p class="text-gray-800">${name}</p>
                            <p class="text-indigo-700 font-bold">E-mail:</p>
                            <p class="text-gray-800">${email}</p>
                            <p class="text-indigo-700 font-bold">Nome do Sócio-Principal:</p>
                            <p class="text-gray-800">${socios}</p>
                            <p class="text-indigo-700 font-bold">Telefone:</p>
                            <p class="text-gray-800">${telefone}</p>
                            <p class="text-indigo-700 font-bold">Porte da Empresa:</p>
                            <p class="text-gray-800">${porte}</p>
                            <p class="text-indigo-700 font-bold">Capital Social:</p>
                            <p class="text-gray-800">R$ ${capital_social}</p>
                        </div>`;
            }
          } else {
            resultDiv.innerHTML = `<div class="bg-gray-100 rounded-lg p-4"><p class="text-indigo-700 font-bold">Retorno: <span class="text-red-700 font-bold">${data.msg}</span></p></div>`;
          }
        } else if (cpf_cnpj.length === 11) {
          let dataNascFormat = new Date(data.nascimento).toLocaleDateString(
            "pt-BR"
          );
          if (data.status != "error") {
            resultDiv.innerHTML = `
                        <div class="bg-gray-100 rounded-lg p-4">
                            <p class="text-indigo-700 font-bold">Nome:</p>
                            <p class="text-stone-800">${data.nome}</p>
                            <p class="text-indigo-700 font-bold">CPF:</p>
                            <p class="text-stone-800">${data.cpf}</p>
                            <p class="text-indigo-700 font-bold">Mãe:</p>
                            <p class="text-stone-800">${data.mae}</p>
                            <p class="text-indigo-700 font-bold">Data de Nascimento:</p>
                            <p class="text-stone-800">${dataNascFormat}</p>
                        </div>`;
          } else {
            resultDiv.innerHTML = `<div class="bg-gray-100 rounded-lg p-4"><p class="text-indigo-700 font-bold">Retorno: <span class="text-red-700 font-bold">${data.msg}</span></p></div>`;
          }
        } else {
          resultDiv.innerHTML = `<div class="bg-gray-100 rounded-lg p-4"><p class="text-indigo-700 font-bold">Não encontrei</p></div>`;
        }
      })
      .catch((error) => {
        console.error("Erro - Reporte no GIT:", error);
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>`;
      });
  } else {
    errDiv.style.display = "block";
    errDiv.innerHTML = `<p class="text-red-700 text-sm">Preencha corretamente as informações.</p>`;
    btn.disabled = false;
    btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>`;
  }
});
