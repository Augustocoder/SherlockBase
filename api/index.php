<?php
header("Content-Type: application/json; charset=UTF-8");
$GetContent = json_decode(file_get_contents('php://input'), true);
$cpf_cnpj = $GetContent['cpf_cnpj'];

if(isset($cpf_cnpj)){
    $cpf_cnpj = str_replace(['.', '/', '-'], '', $cpf_cnpj);
    if(strlen($cpf_cnpj) == 14){
    $req = file_get_contents("https://brasilapi.com.br/api/cnpj/v1/$cpf_cnpj");
    $reqtoJson = json_decode($req, true);
    if($reqtoJson['razao_social']){
        print_r($req);
    }
    else{
        $msg = [
           'status' => 'error',
           'msg' => 'CNPJ não encontrado',
        ];
        echo json_encode($msg);
    }
}
}
else{
    $msg = [
        'status' => 'error',
        'msg' => 'CPF/CNPJ não informado',
    ];
    echo json_encode($msg);
}
