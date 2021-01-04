<?php
    include '../../../_bd/conexao_bd.php';
        
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['bilhete'])) { 
            $bilhete = tratarNumeros($_POST['bilhete']);

            if ($bilhete>0) {
                cancelarBilhete($bilhete);
            } else {
                retornar("0","NÚMERO INVÁLIDO!");
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

    function cancelarBilhete($numero_bilhete) {
        $conn = getConnection();
        
        $sql = 'SELECT id, cancelado FROM bilhetes WHERE id = ?';
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $numero_bilhete);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        
        if (isset($retorno->id)) {
            if ($retorno->cancelado==0) {
                $sql = 'UPDATE bilhetes SET cancelado = 1 WHERE id = ?';
                $stmt = $conn-> prepare($sql);
                $stmt-> bindValue(1, $numero_bilhete);
    
                if ($stmt-> execute()) {
                    retornar("1","BILHETE CANCELADO COM SUCESSO!");
                } else {
                    retornar("0","HÁ PREMIAÇÕES VINCULADAS AO BILHETE!");
                }
            } else {
                retornar("0","O BILHETE JÁ ESTÁ CANCELADO!");
            }
        } else {
            retornar("0","BILHETE NÃO ENCONTRADO!");
        }
    }
    
    function retornar($status, $descricao) {
        $arquivo = file_get_contents('../_json/cancelar_bilhete.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        echo json_encode($retorno);
    }