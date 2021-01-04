<?php
    include '../../../_bd/conexao_bd.php';
    
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['bilhete_app_font']) && isset($_POST['bilhete_print_font']) && isset($_POST['extrato_app_font']) && isset($_POST['extrato_print_font']) && isset($_POST['h4_font']) && isset($_POST['h5_font'])) {

            $bilhete_app_font = tratarFloats($_POST['bilhete_app_font']);
            $bilhete_print_font = tratarFloats($_POST['bilhete_print_font']);
            $extrato_app_font = tratarFloats($_POST['extrato_app_font']);
            $extrato_print_font = tratarFloats($_POST['extrato_print_font']);
            $h4_font = tratarFloats($_POST['h4_font']);
            $h5_font = tratarFloats($_POST['h5_font']);

            salvarConfiguracoes($bilhete_app_font, $bilhete_print_font, $extrato_app_font, $extrato_print_font, $h4_font, $h5_font);
        }
    } else {
        header('Location: ../_login/login.html');
    }

    function tratarFloats($num) {
        try {
            return (float)$num;
        } catch (Exception $e) {
            return null;
        }
    }

    function salvarConfiguracoes($bilhete_app_font, $bilhete_print_font, $extrato_app_font, $extrato_print_font, $h4_font, $h5_font) {
        $conn = getConnection();
        $sql = 'UPDATE usuarios SET bilhete_app_font = ?, bilhete_print_font = ?, extrato_app_font = ?, extrato_print_font = ?, h4_font = ?, h5_font = ? WHERE id = ?';
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $bilhete_app_font);
        $stmt-> bindValue(2, $bilhete_print_font);
        $stmt-> bindValue(3, $extrato_app_font);
        $stmt-> bindValue(4, $extrato_print_font);
        $stmt-> bindValue(5, $h4_font);
        $stmt-> bindValue(6, $h5_font);
        $stmt-> bindValue(7, $_SESSION['id']);

        if ($stmt-> execute()) {
            retornar("1", "SALVO COM SUCESSO!");
        } else {
            retornar("0", "ERRO AO SALVAR!");
        }
    }
    
    function retornar($status, $descricao) {
        $arquivo = file_get_contents('../_json/salvar_configuracoes.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        echo json_encode($retorno);
    }