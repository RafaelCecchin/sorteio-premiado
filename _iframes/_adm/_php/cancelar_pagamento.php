<?php
    include '../../../_bd/conexao_bd.php';
        
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['premiacao'])) { 
            $premiacao = tratarNumeros($_POST['premiacao']);
            if (validarPremiacao($premiacao)) {
                retornaCancelarPagamento($premiacao);
            } else {
                retornar("0", "DADOS INVÁLIDOS!");
            }
        }
    } else {
        header('Location: ../_login/login.html');
    }

    function tratarNumeros($num) {
        try {
            return (int)$num;
        } catch (Exception $e) {
            return null;
        }
    }

    function validarPremiacao($num) {
        if ($num>0) {
            return true;
        }

        return false;
    }

    function retornaCancelarPagamento($premiacao) {
        $conn = getConnection();
        
        $sql = 'SELECT id, paga, pagamento_liberado FROM premiacoes_pagamentos WHERE id=?';
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $premiacao);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        
        if (isset($retorno->id)) {
            if ($retorno->pagamento_liberado==1) {
                if ($retorno->paga==1) {
                    $sql = "UPDATE premiacoes_pagamentos SET paga = 0 WHERE paga = 1 AND id = ?";
                    $stmt = $conn-> prepare($sql);
                    $stmt-> bindValue(1, $premiacao);
        
                    if ($stmt-> execute()) {
                        retornar("1","CANCELAMENTO REALIZADO COM SUCESSO!");
                    } else {
                        retornar("0","ERRO AO CANCELAR PAGAMENTO!");
                    }
                } else {
                    retornar("0","O PRÊMIO AINDA NÃO FOI PAGO!");
                }
            } else {
                retornar("0","AINDA HÁ SORTEIOS PENDENTES!", null);
            }
        } else {
            retornar("0","PREMIAÇÃO NÃO ENCONTRADA!");
        }

        
        
    }

    function retornar($status, $descricao) {
        $arquivo = file_get_contents('../_json/cancelar_pagamento.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        echo json_encode($retorno);
    }