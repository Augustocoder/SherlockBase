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
    $url = 'https://receitaws.com.br/v1/cnpj/' . $cnpj;
    $context = stream_context_create(['http' => ['timeout' => 8]]);
    $result = @file_get_contents($url, false, $context);
    if ($result === FALSE) {
        json_response(['status' => 'error', 'msg' => 'Falha ao consultar serviço externo.'], 502);
    }
    $data = json_decode($result, true);
    if (!empty($data['nome'])) {
        json_response($data);
    }
    json_response(['status' => 'error', 'msg' => 'CNPJ não encontrado'], 404);
}

function reqConsultaCPF($cpf)
{
    // EM BREVE IREI COLOCAR NO AR NOVAMENTE, PEÇO DESCULPAS PELO INCÔMODO!
}

$input = json_decode(file_get_contents('php://input'), true);
$cpf_cnpj = $input['cpf_cnpj'] ?? null;

if (!$cpf_cnpj) {
    json_response(['status' => 'error', 'msg' => 'CPF/CNPJ não informado'], 400);
}

$cpf_cnpj = filtrarCpfCnpj($cpf_cnpj);

if (!ctype_digit($cpf_cnpj)) {
    json_response(['status' => 'error', 'msg' => 'Por favor, não aceitamos letras.'], 422);
}

if (strlen($cpf_cnpj) == 14) {
    reqConsultaCNPJ($cpf_cnpj);
} elseif (strlen($cpf_cnpj) == 11) {
    reqConsultaCPF($cpf_cnpj);
} else {
    json_response(['status' => 'error', 'msg' => 'Tamanho inválido para CPF ou CNPJ.'], 422);
}
