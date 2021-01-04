<?php
    include '../../../_bd/conexao_bd.php';
        
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['premiacao'])) { 
            $premiacao = tratarNumeros($_POST['premiacao']);
            if (validarNumeros($premiacao)) {
                retornaPagamento($premiacao);
            } else {
                retornar("0", "DADOS INVÁLIDOS!", null);
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

    function validarNumeros($num) {
        if ($num>0) {
            return true;
        }

        return false;
    }

    function retornaPagamento($premiacao) {
        $conn = getConnection();
        
        $sql = 'SELECT id, paga, pagamento_liberado FROM premiacoes_pagamentos WHERE id=?';
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $premiacao);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        
        if (!empty($retorno)) {
            if ($retorno->pagamento_liberado==1) {
                if ($retorno->paga==0) {
                    $sql = "UPDATE premiacoes_pagamentos SET paga = 1 WHERE paga = 0 AND id = ?";
                    $stmt = $conn-> prepare($sql);
                    $stmt-> bindValue(1, $premiacao);
        
                    if ($stmt-> execute()) {
                        retornar("1","PAGAMENTO REALIZADO COM SUCESSO!", date('d/m/Y H:i:s'));
                    } else {
                        retornar("0","ERRO AO REALIZAR PAGAMENTO!", null);
                    }
                } else {
                    retornar("0","O PRÊMIO JÁ FOI PAGO!", null);
                }
            } else {
                retornar("0","AINDA HÁ SORTEIOS PENDENTES!", null);
            }

            
        } else {
            retornar("0","PREMIAÇÃO NÃO ENCONTRADA!", null);
        }

        
        
    }

    function retornar($status, $descricao, $dh_pagamento) {
        $arquivo = file_get_contents('../_json/realizar_pagamento.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->dh_pagamento=$dh_pagamento;
        echo json_encode($retorno);
    }