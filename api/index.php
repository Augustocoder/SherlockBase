<?php
header("Content-Type: application/json; charset=UTF-8");
$GetContent = json_decode(file_get_contents('php://input'), true);
$cpf_cnpj = $GetContent['cpf_cnpj'];

if (isset($cpf_cnpj)) {
    if (is_numeric($cpf_cnpj)) {
        $cpf_cnpj = str_replace(['.', '/', '-'], '', $cpf_cnpj);
        if (strlen($cpf_cnpj) == 14) {
            $req = file_get_contents("https://receitaws.com.br/v1/cnpj/$cpf_cnpj");
            $reqtoJson = json_decode($req, true);
            if ($reqtoJson['nome']) {
                print_r($req);
            } else {
                $msg = [
                    'status' => 'error',
                    'msg' => 'CNPJ não encontrado',
                ];
                echo json_encode($msg);
            }
        } else if (strlen($cpf_cnpj) == 11) {
            $req = file_get_contents("https://api.consultanacional.org:3000/consulta/$cpf_cnpj");
            $reqtoJson = json_decode($req, true);
            if ($reqtoJson['cpf']) {
                print_r($req);
            } else {
                $msg = [
                    'status' => 'error',
                    'msg' => 'CPF não encontrado',
                ];
                echo json_encode($msg);
            }
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
