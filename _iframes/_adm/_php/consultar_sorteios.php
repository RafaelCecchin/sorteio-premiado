<?php
    include '../../../_bd/conexao_bd.php';
        
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['sorteio']) && isset($_POST['pagina'])) { 
            $sorteio = tratarNumeros($_POST['sorteio']);
            $pagina = tratarNumeros($_POST['pagina']);
            
            if (validarSorteio($sorteio) && validarPagina($pagina)) {
                consultarSorteio($sorteio, $pagina);
            } else {
                retornar("0","DADOS INVÁLIDOS!", null, null);
            }
        }
    } else {
        header('Location: ../_login/login.html');
    }

    function tratarNumeros($num) {
        try {
            return (int)$num;
        } catch (Exception $e) {
            return 0;
        }
    }

    function validarSorteio($num) {
        if ($num>=0) {
            return true;
        }

        return false;
    }

    function validarPagina($num) {
        if ($num>0) {
            return true;
        }

        return false;
    }

    function consultarSorteio($numero_sorteio, $pagina) {
        $conn = getConnection();
        if ($numero_sorteio=='0') {
            $sql = "SELECT s.id, s.numeros_sorteados, s.numero_bonus, s.fechado, s.dh_inicio, s.dh_final, COALESCE(ROUND(SUM(p.valor),2),'0') AS premiacoes FROM premiacoes AS p RIGHT JOIN apostas AS a ON p.id_aposta = a.id RIGHT JOIN sorteios AS s ON a.id_sorteio = s.id WHERE s.fechado=1 GROUP BY s.id ORDER BY s.id DESC LIMIT ?, 25";
            $stmt = $conn-> prepare($sql);
            $stmt-> bindValue(1, (($pagina-1)*25), PDO::PARAM_INT);
        } else {
            $sql = "SELECT s.id, s.numeros_sorteados, s.numero_bonus, s.fechado, s.dh_inicio, s.dh_final, COALESCE(ROUND(SUM(p.valor),2),'0') AS premiacoes FROM premiacoes AS p RIGHT JOIN apostas AS a ON p.id_aposta = a.id RIGHT JOIN sorteios AS s ON a.id_sorteio = s.id WHERE s.fechado=1 AND s.id=? GROUP BY s.id ORDER BY s.id DESC LIMIT ?, 25";
            $stmt = $conn-> prepare($sql);
            $stmt-> bindValue(1, $numero_sorteio);
            $stmt-> bindValue(2, (($pagina-1)*25), PDO::PARAM_INT);    
        }

        $stmt-> execute();
        $retorno = $stmt -> fetchAll(PDO::FETCH_OBJ);

        if (!empty($retorno)) {
            $quantidade = count($retorno);
            for ($i = 0; $i<$quantidade; $i++) {
                $retorno[$i]->dh_final = date('d/m/Y H:i:s', strtotime($retorno[$i]->dh_final));
            }

            retornar("1", "REGISTROS ENCONTRADOS COM SUCESSO!", $quantidade, $retorno);
        } else {
            retornar("0", "NENHUM REGISTRO ENCONTRADO!", null, null);
        }
    }
    
    function retornar($status, $descricao, $quantidade, $sorteios) {
        $arquivo = file_get_contents('../_json/consultar_sorteios.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->quantidade=$quantidade;
        $retorno->sorteios=$sorteios;
        echo json_encode($retorno);
    }