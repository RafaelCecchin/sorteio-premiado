<?php

    include '../../../_bd/conexao_bd.php';  
    
    session_start();

    if (isset($_SESSION['logado'])) {       
        if (isset($_POST['bilhete'])) {
            $bilhete = tratarNumeros($_POST['bilhete']);
            if (validarBilhete($bilhete)) {
                retornarAposta($bilhete);
            } else {
                retornar("0","NÚMERO INVÁLIDO!", null);
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

    function validarBilhete($num) {
        if ($num>=0) {
            return true;
        }

        return false;
    }

    function retornarAposta($bilhete) {
        $conn = getConnection();

        if ($bilhete!='0') {
            $sql = 'SELECT id, numeros_apostados FROM bilhetes WHERE id = ?';
            $stmt = $conn-> prepare($sql);
            $stmt-> bindValue(1, $bilhete);
        } else {
            $sql = 'SELECT id, numeros_apostados FROM bilhetes WHERE id = (SELECT MAX(id) FROM bilhetes)';
            $stmt = $conn-> prepare($sql);
        }
        
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        
        if (isset($retorno->id)) {
            $numeros = $retorno->numeros_apostados;
            
            retornar("1","NÚMEROS COPIADOS COM SUCESSO!", $numeros);
        } else {
            retornar("0","BILHETE NÃO ENCONTRADO!", null);
        }
    }

    function retornar($status, $descricao, $numeros) {
        $arquivo = file_get_contents('../_json/repetir_aposta.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->numeros=$numeros;
        echo json_encode($retorno);
    }