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
    @$request = file_get_contents('https://receitaws.com.br/v1/cnpj/'.$cpf_cnpj);
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
        @$request = file_get_contents("https://api.consultanacional.org:3000/consulta/$cpf_cnpj");
        $respJson = json_decode($request, true);
        if (@$respJson['cpf']) {
            print_r($request);
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
