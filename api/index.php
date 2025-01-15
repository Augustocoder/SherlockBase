<?php
error_reporting(0);
header("Content-Type: application/json; charset=UTF-8");
$GetContent = json_decode(file_get_contents('php://input'), true);
$cpf_cnpj = $GetContent['cpf_cnpj'];

function filtrarCpfCnpj($cpf_cnpj)
{
    $cpf_cnpj = str_replace(['.', '/', '-'], '', $cpf_cnpj);
    return $cpf_cnpj;
}
function reqConsultaCNPJ($cpf_cnpj)
{
    @$request = file_get_contents('https://receitaws.com.br/v1/cnpj/' . $cpf_cnpj);
    $respJson = json_decode($request, true);
    if (@$respJson['nome']) {
        print_r($request);
    } else {
        $msg = [
            'status' => 'error',
            'msg' => 'CNPJ não encontrado',
        ];
        echo json_encode($msg);
    }
}
function reqConsultaCPF($cpf_cnpj)
{
    try {
        // Essa API foi encontrada no Search Engine "Censys".
        @$request = file_get_contents("https://api.centralda20.com/consultar/$cpf_cnpj");
        $respJson = json_decode($request, true);
        if (isset($respJson['CPF'])) {
            $formatarJSON = json_encode(
                [
                    "CPF" => $cpf_cnpj,
                    "NOME" => $respJson["NOME"],
                    "SEXO" => $respJson["SEXO"],
                    "NASC" => $respJson["NASC"],
                    "NOME_MAE" => isset($respJson["NOME_MAE"]) ? $respJson["NOME_MAE"] : "Não encontrado",
                    "RG" => isset($respJson["RG"]) ? $respJson["RG"] : "Não encontrado",
                    "CBO" => isset($respJson["CBO"]) ? $respJson["CBO"] : "Não encontrado",
                    "ORGAO_EMISSOR" => isset($respJson["ORGAO_EMISSOR"]) ? $respJson["ORGAO_EMISSOR"] : "Não encontrado",
                    "UF_EMISSAO" => isset($respJson["UF_EMISSAO"]) ? $respJson["UF_EMISSAO"] : "Não encontrado",
                    "CD_MOSAIC" => isset($respJson["CD_MOSAIC"]) ? $respJson["CD_MOSAIC"] : "Não encontrado",
                    "RENDA" => isset($respJson["RENDA"]) ? "R$".$respJson["RENDA"] : "Não encontrado",
                    "TITULO_ELEITOR" => isset($respJson["TITULO_ELEITOR"]) ? $respJson["TITULO_ELEITOR"] : "Não encontrado",
                    "CD_MOSAIC_NOVO" => isset($respJson["CD_MOSAIC_NOVO"]) ? $respJson["CD_MOSAIC_NOVO"] : "Não encontrado",

                ]
            );
            print_r($formatarJSON);
        } else {
            $msg = [
                'status' => 'error',
                'msg' => 'CPF não encontrado',
            ];
            echo json_encode($msg);
        }
    } catch (Exception $e) {
        $msg = [
            'status' => 'error',
            'msg' => $e,
        ];
        echo json_encode($msg);
    }
}

if (isset($cpf_cnpj)) {
    if (is_numeric($cpf_cnpj)) {
        $cpf_cnpj = filtrarCpfCnpj($cpf_cnpj);
        if (strlen($cpf_cnpj) == 14) {
            reqConsultaCNPJ($cpf_cnpj);
        } else if (strlen($cpf_cnpj) == 11) {
            reqConsultaCPF($cpf_cnpj);
        } else {
            $msg = [
                'status' => 'error',
                'msg' => 'Algo de errado, não está certo.',
            ];
            echo json_encode($msg);
        }
    } else {
        $msg = [
            'status' => 'error',
            'msg' => 'Por favor, não aceitamos letras.',
        ];
        echo json_encode($msg);
    }
} else {
    $msg = [
        'status' => 'error',
        'msg' => 'CPF/CNPJ não informado',
    ];
    echo json_encode($msg);
}
