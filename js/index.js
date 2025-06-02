let btn = document.getElementById("enviar");

btn.addEventListener("click", (e) => {
  e.preventDefault();

  let cpf_cnpj = document.getElementById("cpf_cnpj").value.trim();
  cpf_cnpj = cpf_cnpj.replace(/\D/g, "");

  let errDiv = document.getElementById("error");
  let resultDiv = document.getElementById("result");

  errDiv.classList.add("hidden");
  resultDiv.innerHTML = "";

  btn.disabled = true;
  btn.innerHTML = `
    <svg class="animate-spin w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
    </svg>`;

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
        const cardBase =
          "glass border border-indigo-600 rounded-xl shadow-lg p-5 transition-all";

        if (cpf_cnpj.length === 14) {
          if (data.status !== "error") {
            let socios = data.qsa?.[0]?.nome || "Não Informado";
            let name = data.nome || "Não Informado";
            let telefone = data.telefone || "Não Informado";
            let porte = data.porte || "Não Informado";
            let capital_social = data.capital_social || "Não Informado";
            let email = data.email || "Não Informado";
            resultDiv.innerHTML = `
              <div class="${cardBase}">
                <p class="font-bold text-indigo-400 mb-2">Razão Social:</p>
                <p class="text-gray-100 mb-1">${name}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">E-mail:</p>
                <p class="text-gray-100 mb-1">${email}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Sócio Principal:</p>
                <p class="text-gray-100 mb-1">${socios}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Telefone:</p>
                <p class="text-gray-100 mb-1">${telefone}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Porte da Empresa:</p>
                <p class="text-gray-100 mb-1">${porte}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Capital Social:</p>
                <p class="text-gray-100 mb-1">R$ ${capital_social}</p>
              </div>
            `;
          } else {
            resultDiv.innerHTML = `
              <div class="${cardBase} border-red-600">
                <p class="font-bold text-red-400">Retorno:</p>
                <p class="text-red-200">${data.msg || "Erro ao consultar."}</p>
              </div>
            `;
          }
        } else if (cpf_cnpj.length === 11) {
          if (data.status !== "error") {
            
            resultDiv.innerHTML = `
              <div class="${cardBase}">
                <p class="font-bold text-indigo-400 mb-2">Nome:</p>
                <p class="text-gray-100 mb-1">${data.nome}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">CPF:</p>
                <p class="text-gray-100 mb-1">${data.cpf}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Mãe:</p>
                <p class="text-gray-100 mb-1">${data.nomeMae}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Nascimento:</p>
                <p class="text-gray-100 mb-1">${data.nasc}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Pai:</p>
                <p class="text-gray-100 mb-1">${data.nomePai}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Orgão Emissor:</p>
                <p class="text-gray-100 mb-1">${data.orgaoEmissor}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Renda:</p>
                <p class="text-gray-100 mb-1">${data.renda}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Sexo:</p>
                <p class="text-gray-100 mb-1">${data.sexo}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">Título de Eleitor:</p>
                <p class="text-gray-100 mb-1">${data.tituloEleitor}</p>
                <p class="font-bold text-indigo-400 mt-3 mb-2">UF Emissão:</p>
                <p class="text-gray-100 mb-1">${data.ufEmissao}</p>


              </div>
            `;
          } else {
            resultDiv.innerHTML = `
              <div class="${cardBase} border-red-600">
                <p class="font-bold text-red-400">Retorno:</p>
                <p class="text-red-200">${data.msg || "Erro ao consultar."}</p>
              </div>
            `;
          }
        }
      })
      .catch((error) => {
        errDiv.classList.remove("hidden");
        errDiv.innerHTML = `<p class="text-red-200 font-mono">Falha na consulta. Reporte ao suporte.</p>`;
        resultDiv.innerHTML = "";
        console.error("Erro - Reporte no GIT:", error);
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>`;
      });
  } else {
    errDiv.classList.remove("hidden");
    errDiv.className =
      "glass border border-red-600 rounded-lg p-3 mt-5 mb-2 text-red-200 text-sm shadow-lg transition-all";
    errDiv.innerHTML = `<p>Preencha corretamente as informações.</p>`;
    btn.disabled = false;
    btn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
      </svg>`;
    resultDiv.innerHTML = "";
  }
});
