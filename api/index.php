<?php
error_reporting(0);
header("Content-Type: application/json; charset=UTF-8");

function filtrarCpfCnpj($str)
{
    return preg_replace('/\D/', '', $str);
}

function json_response($arr, $status = 200)
{
    http_response_code($status);
    echo json_encode($arr);
    exit;
}

function reqConsultaCNPJ($cnpj)
{
    $url     = 'https://receitaws.com.br/v1/cnpj/' . $cnpj;
    $context = stream_context_create(['http' => ['timeout' => 8]]);
    $result  = @file_get_contents($url, false, $context);
    if ($result === false) {
        json_response(['status' => 'error', 'msg' => 'Falha ao consultar serviço externo.'], 502);
    }
    $data = json_decode($result, true);
    if (! empty($data['nome'])) {
        json_response($data);
    }
    json_response([
        'status' => 'error',
        'msg'    => 'CNPJ não encontrado',
    ], 404);
}

function reqConsultaCPF($cpf)
{
    // Essa API foi extraída de um site falso anunciado no Google.
    $url  = "https://encomendasdobrasil.com/api.php";
    $ch   = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_POSTFIELDS     => "cpf=$cpf",
    ]);
    $result = curl_exec($ch);
    if ($result === false) {
        json_response(['status' => 'error', 'msg' => 'Falha ao consultar serviço externo.'], 502);
    }
    curl_close($ch);
    $resultToJSON = json_decode($result, true);
    if (isset($resultToJSON['status']) && $resultToJSON['status'] == 200) {
        $nasc          = strlen($resultToJSON['dados'][0]['NASC']) > 1 ? $resultToJSON['dados'][0]['NASC'] : 'Não informado';
        $nome          = strlen($resultToJSON['dados'][0]['NOME']) > 1 ? $resultToJSON['dados'][0]['NOME'] : 'Não informado';
        $nomeMae       = strlen($resultToJSON['dados'][0]['NOME_MAE']) > 1 ? $resultToJSON['dados'][0]['NOME_MAE'] : 'Não informado';
        $nomePai       = strlen($resultToJSON['dados'][0]['NOME_PAI']) > 1 ? $resultToJSON['dados'][0]['NOME_PAI'] : 'Não informado';
        $orgaoEmissor  = strlen($resultToJSON['dados'][0]['ORGAO_EMISSOR']) > 1 ? $resultToJSON['dados'][0]['ORGAO_EMISSOR'] : 'Não informado';
        $renda         = strlen($resultToJSON['dados'][0]['RENDA']) > 1 ? $resultToJSON['dados'][0]['RENDA'] : 'Não informado';
        $rg            = strlen($resultToJSON['dados'][0]['RG']) > 1 ? $resultToJSON['dados'][0]['RG'] : 'Não informado';
        $sexo          = strlen($resultToJSON['dados'][0]['SEXO']) ? $resultToJSON['dados'][0]['SEXO'] : 'Não informado';
        $tituloEleitor = strlen($resultToJSON['dados'][0]['TITULO_ELEITOR']) > 1 ? $resultToJSON['dados'][0]['TITULO_ELEITOR'] : 'Não informado';
        $ufEmissao     = strlen($resultToJSON['dados'][0]['UF_EMISSAO']) > 1 ? $resultToJSON['dados'][0]['UF_EMISSAO'] : 'Não informado';
        json_response([
            'status'        => 'success',
            'cpf'           => $cpf,
            'nasc'          => $nasc,
            'nome'          => $nome,
            'nomeMae'       => $nomeMae,
            'nomePai'       => $nomePai,
            'orgaoEmissor'  => $orgaoEmissor,
            'renda'         => $renda,
            'rg'            => $rg,
            'sexo'          => $sexo,
            'tituloEleitor' => $tituloEleitor,
            'ufEmissao'     => $ufEmissao,
        ]);

    } else {
        json_response([
            'status' => 'error',
            'msg'    => 'CPF não encontrado',
        ], 404);
    }
}

$input    = json_decode(file_get_contents('php://input'), true);
$cpf_cnpj = $input['cpf_cnpj'] ?? null;

if (! $cpf_cnpj) {
    json_response([
        'status' => 'error',
        'msg'    => 'CPF/CNPJ não informado',
    ], 400);
}

$cpf_cnpj = filtrarCpfCnpj($cpf_cnpj);

if (! ctype_digit($cpf_cnpj)) {
    json_response([
        'status' => 'error',
        'msg'    => 'Por favor, não aceitamos letras.',
    ], 422);
}

if (strlen($cpf_cnpj) == 14) {
    reqConsultaCNPJ($cpf_cnpj);
} else if (strlen($cpf_cnpj) == 11) {
    reqConsultaCPF($cpf_cnpj);
} else {
    json_response([
        'status' => 'error',
        'msg'    => 'Tamanho inválido para CPF ou CNPJ.',
    ], 422);
}
