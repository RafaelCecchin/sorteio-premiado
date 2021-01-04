<?php

    include '../../../_bd/conexao_bd.php';  
    
    session_start();

    if (isset($_SESSION['logado'])) {
        retornaLucro();
    } else {
        header('Location: ../_login/login.html');
    }

    function retornaLucro() {
        $conn = getConnection();
        $sql = 'SELECT COALESCE(razao_selecionada, 0) AS razao_selecionada, COALESCE(razao_atual, 0) AS razao_atual FROM configuracoes WHERE id = 1';
        $stmt = $conn-> prepare($sql);
        
        if ($stmt-> execute()) {
            $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
            $razao_selecionada = $retorno->razao_selecionada;
            $razao_atual = $retorno->razao_atual;

            retornar("1", "CONSULTA REALIZADA COM SUCESSO!", $razao_selecionada, $razao_atual);
        } else {
            retornar("0", "ERRO AO REALIZAR CONSULTA!", null, null);
        }
    }

    function tratarNumeros($num) {
        try {
            return (int)$num;
        } catch (Exception $e) {
            return null;
        }
    }

    function retornar($status, $descricao, $razao_selecionada, $razao_atual) {
        $arquivo = file_get_contents('../_json/consultar_configuracoes.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->razao_selecionada=$razao_selecionada;
        $retorno->razao_atual=$razao_atual;
        echo json_encode($retorno);
    }
