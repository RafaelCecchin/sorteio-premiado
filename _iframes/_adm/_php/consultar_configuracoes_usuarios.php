<?php

    include '../../../_bd/conexao_bd.php';  
    
    session_start();

    if (isset($_SESSION['logado'])) {
        retornaConfiguracoesUsuarios();
    } else {
        header('Location: ../_login/login.html');
    }

    function retornaConfiguracoesUsuarios() {
        $conn = getConnection();
        $sql = 'SELECT bilhete_app_font, bilhete_print_font, extrato_app_font, extrato_print_font, h4_font, h5_font FROM usuarios WHERE id = ?';
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $_SESSION['id']);
        
        if ($stmt-> execute()) {
            $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
            $bilhete_app_font = $retorno->bilhete_app_font;
            $bilhete_print_font = $retorno->bilhete_print_font;
            $extrato_app_font = $retorno->extrato_app_font;
            $extrato_print_font = $retorno->extrato_print_font;
            $h4_font = $retorno->h4_font;
            $h5_font = $retorno->h5_font;

            retornar("1", "CONSULTA REALIZADA COM SUCESSO!", $bilhete_app_font, $bilhete_print_font, $extrato_app_font, $extrato_print_font, $h4_font, $h5_font);
        } else {
            retornar("0", "ERRO AO REALIZAR CONSULTA!", null, null, null, null, null, null);
        }
    }

    function retornar($status, $descricao, $bilhete_app_font, $bilhete_print_font, $extrato_app_font, $extrato_print_font, $h4_font, $h5_font) {
        $arquivo = file_get_contents('../_json/consultar_configuracoes_usuarios.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->bilhete_app_font=$bilhete_app_font;
        $retorno->bilhete_print_font=$bilhete_print_font;
        $retorno->extrato_app_font=$extrato_app_font;
        $retorno->extrato_print_font=$extrato_print_font;
        $retorno->h4_font=$h4_font;
        $retorno->h5_font=$h5_font;
        echo json_encode($retorno);
    }
