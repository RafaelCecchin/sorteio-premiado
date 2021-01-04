<?php
    include '../../../_bd/conexao_bd.php';
        
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['bilhete'])) {

            $bilhete = tratarNumeros($_POST['bilhete']);

            if (validarBilhete($bilhete)) {
                retornaBilhete($bilhete);
            } else {
                retornar("0","NÚMERO INVÁLIDO!", null, null, null, null, null);
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
        
        if (!empty($retorno)) {
            $bilhete = $retorno->id;
            $rodadas = $retorno->rodadas;
            $dh_consulta =  date('d/m/Y H:i:s');
            $premiacoes = retornaPremiacoes($bilhete);
            $cancelado = $retorno->cancelado;

            retornar("1","BILHETE ENCONTRADO!", $bilhete, $rodadas, $dh_consulta, $premiacoes, $cancelado);
        } else {
            retornar("0","BILHETE NÃO ENCONTRADO!", null, null, null, null, null);
        }
    }

    function retornaQuantidade($numeros_apostados) {
        return count(explode(",", $numeros_apostados));
    }
    
    function retornaPremiacoes($numero_bilhete) {
        $conn = getConnection();
        $sql = 'SELECT a.id_sorteio AS numero_sorteio, COALESCE(p.valor, 0) AS lucro, s.fechado FROM apostas AS a LEFT JOIN premiacoes AS p ON a.id = p.id_aposta LEFT JOIN sorteios AS s ON a.id_sorteio = s.id WHERE a.id_bilhete=? ORDER BY a.id_sorteio ASC';
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $numero_bilhete);
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

    function retornar($status, $descricao, $bilhete, $rodadas, $dh_consulta, $premiacoes, $cancelado) {
        $arquivo = file_get_contents('../_json/consultar_bilhetes.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->bilhete=$bilhete;
        $retorno->rodadas=$rodadas;
        $retorno->dh_consulta=$dh_consulta;
        $retorno->premiacoes=$premiacoes;
        $retorno->cancelado=$cancelado;
        echo json_encode($retorno);
    }