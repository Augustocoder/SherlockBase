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
    $url  = "https://api.dataget.site/api/v1/cpf/$cpf";
    $ch   = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_TIMEOUT       => 10,
        CURLOPT_HTTPHEADER     => [
            "User-Agent: Mozilla/5.0 (Windows NT 12.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0",
            // USE ESSA KEY À VONTADE!
            "Authorization: Bearer 2e1228a7a34fb74cb5d91cfae27594ef07b0f03f92abe4611c94bc3fa4583765",
        ],
    ]);
    $result = curl_exec($ch);
    if ($result === false) {
        json_response(['status' => 'error', 'msg' => 'Falha ao consultar serviço externo.'], 502);
    }
    curl_close($ch);
    $resultToJSON = json_decode($result, true);
    if (isset($resultToJSON['CPF']) && !empty($resultToJSON['CPF'])) {
        $nome = $resultToJSON['NOME'] ?? 'Não informado';
        $nasc = $resultToJSON['NASC'] ?? 'Não informado';
        $nomeMae = $resultToJSON['NOME_MAE'] ?? 'Não informado';
        $nomePai = (strlen($resultToJSON['NOME_PAI']) > 2) ? $resultToJSON['NOME_PAI'] : 'Não informado';
        $sexo = $resultToJSON['SEXO'] ?? 'Não informado';
        json_response([
            'status'        => 'success',
            'cpf'           => $cpf,
            'nome'          => $nome,
            'nasc'    => $nasc,
            'nomeMae'      => $nomeMae,
            'nomePai'      => $nomePai,
            'sexo'          => $sexo,
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
