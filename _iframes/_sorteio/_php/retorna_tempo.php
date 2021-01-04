<?php 
    include '../../../_bd/conexao_bd.php';
    
    retornaTempo();

    function retornaTempo() {
        $conn = getConnection();
        $sql = 'SELECT TIMESTAMPDIFF(SECOND, (SELECT max(dh_final) as dh_final FROM sorteios WHERE fechado=1), now()) as `diferenca`';
	    $stmt = $conn -> prepare($sql);
    	$stmt -> execute();
	    $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $diferenca = $retorno ->diferenca;
        
        retornar($diferenca);
    }

    function retornar($segundos) {
        $arquivo = file_get_contents('../_json/retorna_tempo.json');
        $retorno = json_decode($arquivo);
        $retorno->segundos=$segundos;
        echo json_encode($retorno);
    }

