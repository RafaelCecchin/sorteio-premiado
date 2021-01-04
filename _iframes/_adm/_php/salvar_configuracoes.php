<?php
    include '../../../_bd/conexao_bd.php';
    
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['razao_atual']) && isset($_POST['razao_selecionada'])) {
            $razao_atual = tratarFloats($_POST['razao_atual']);
            $razao_selecionada = tratarFloats($_POST['razao_selecionada']);

            if (verificarRazao($razao_atual) && verificarRazao($razao_selecionada)) {
                salvarConfiguracoes($razao_atual, $razao_selecionada);
            } else {
                retornar("0", "DADOS INVÁLIDOS!");
            }
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

    function verificarRazao($razao) {
        if ($razao>=0 && $razao<=1) {
            return true;
        } 

        return false;
    }

    function salvarConfiguracoes($razao_atual, $razao_selecionada) {
        $conn = getConnection();
        $sql = 'UPDATE configuracoes SET razao_atual = ?, razao_selecionada = ?';
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $razao_atual);
        $stmt-> bindValue(2, $razao_selecionada);

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