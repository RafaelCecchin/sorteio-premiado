<?php
    include '../../../_bd/conexao_bd.php';
        
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['bilhete'])) {

            $bilhete = tratarNumeros($_POST['bilhete']);

            if (validarBilhete($bilhete)) {
                retornaBilhete($bilhete);
            } else {
                retornar("0","NÚMERO INVÁLIDO!", null, null, null, null, null, null, null, null);
            }
        }
    } else {
        header('Location: ../_login/login.html');
    }

    function retornaBilhete($numero_bilhete) {
        $conn = getConnection();

        if ($numero_bilhete == 0) {
            $sql = 'SELECT * FROM bilhetes WHERE id = (SELECT MAX(id) FROM bilhetes)';
            $stmt = $conn-> prepare($sql);
        } else {
            $sql = 'SELECT * FROM bilhetes WHERE id = ?';
            $stmt = $conn-> prepare($sql);
            $stmt-> bindValue(1, $numero_bilhete);
        }
        
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        
        if (isset($retorno->id)) {
            $bilhete = $retorno->id;
            $numeros_apostados = str_replace(",", ", ",$retorno->numeros_apostados);
            $numero_bonus = $retorno->numero_bonus;
            $rodadas = $retorno->rodadas;
            $valor = $retorno->valor;
            $dh_insert =  date('d/m/Y H:i:s', strtotime($retorno->dh_insert));
            $quantidade_apostada = retornaQuantidade($numeros_apostados);
            $premiacao = retornaPremiacao($quantidade_apostada);

            retornar("1","BILHETE ENCONTRADO!", $bilhete, $numeros_apostados, $numero_bonus, $rodadas, $valor, $dh_insert, $quantidade_apostada, $premiacao);
        } else {
            retornar("0","BILHETE NÃO ENCONTRADO!", null, null, null, null, null, null, null, null);
        }
    }

    function retornaQuantidade($numeros_apostados) {
        return count(explode(",", $numeros_apostados));
    }

    function retornaPremiacao($quantidade) {
        $conn = getConnection();
        $sql = "SELECT id, quantidade_apostada, quantidade_acertos, multiplicador FROM premiacao WHERE quantidade_apostada = ?";
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $quantidade);
        $stmt-> execute();
        $retorno = $stmt -> fetchAll(PDO::FETCH_OBJ);

        return $retorno;
    }

    function tratarNumeros($num) {
        try {
            return (int)$num;
        } catch (Exception $e) {
            return 0;
        }
    }

    function validarBilhete($num) {
        if ($num>=0) {
            return true;
        }

        return false;
    }

    function retornar($status, $descricao, $bilhete, $numeros_apostados, $numero_bonus, $rodadas, $valor, $dh_insert, $quantidade_apostada, $premiacao) {
        $arquivo = file_get_contents('../_json/consultar_bilhetes.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->bilhete=$bilhete;
        $retorno->numeros_apostados=$numeros_apostados;
        $retorno->numero_bonus=$numero_bonus;
        $retorno->rodadas=$rodadas;
        $retorno->valor=$valor;
        $retorno->dh_insert=$dh_insert;
        $retorno->quantidade_apostada=$quantidade_apostada;
        $retorno->premiacao=$premiacao;
        echo json_encode($retorno);
    }