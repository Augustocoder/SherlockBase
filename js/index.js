let btn = document.getElementById("enviar");
btn.addEventListener("click", () => {
    let cpf_cnpj = document.getElementById("cpf_cnpj").value;
    cpf_cnpj = cpf_cnpj.replaceAll(".", "");
    cpf_cnpj = cpf_cnpj.replaceAll("-", "");
    cpf_cnpj = cpf_cnpj.replaceAll("/", "");
    let errDiv = document.getElementById("error");
    if (cpf_cnpj.length == 14 || cpf_cnpj.length == 11) {
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
                let resultDiv = document.getElementById("result");
                if (cpf_cnpj.length == 14) {
                    errDiv.style.display = "none";
                    let nomeDefine = data.nome;
                    if (nomeDefine) {
                        let socios = data.qsa && data.qsa.length > 0 ? data.qsa[0].nome : "Não Informado";
                        let name = data.nome ? data.nome : "Não Informado";
                        let telefone = data.telefone ? data.telefone : "Não Informado";
                        let porte = data.porte ? data.porte : "Não Informado";
                        let capital_social = data.capital_social ? data.capital_social : "Não Informado";
                        let email = data.email ? data.email : "Não Informado";

                        resultDiv.innerHTML = `<div class="bg-gray-100 rounded-lg p-4">
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
                } else if (cpf_cnpj.length == 11) {
                    errDiv.style.display = "none";
                    let dataNascFormat = new Date(data.nascimento);
                    dataNascFormat = dataNascFormat.toLocaleDateString("pt-BR");
                    resultDiv.innerHTML = ` <div class="bg-gray-100 rounded-lg p-4">
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
                    resultDiv.innerHTML = `<div class="bg-gray-100 rounded-lg p-4">
                  <p class="text-indigo-700 font-bold">Não encontrei</p>
                  `;
                }
            })
            .catch((error) => {
                console.error("Erro - Reporte no GIT:", error);
            });
    } else {
        errDiv.style.display = "block";
        errDiv.innerHTML = `<p class="text-red-700 text-sm">Preencha corretamente as informações.</p>`;
    }
});
