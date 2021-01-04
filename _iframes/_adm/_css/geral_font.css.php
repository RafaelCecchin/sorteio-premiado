<?php

include '../../../_bd/conexao_bd.php';  
header("Content-type: text/css");

session_start();

retornaTamanhoFontes();

function retornaTamanhoFontes() {
    $conn = getConnection();
    $sql = 'SELECT h4_font, h5_font FROM usuarios WHERE id = ?';
    $stmt = $conn-> prepare($sql);
    $stmt-> bindValue(1, $_SESSION['id']);
    
    if ($stmt-> execute()) {
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $h4_font = $retorno->h4_font;
        $h5_font = $retorno->h5_font;

        echo('
            @charset "utf-8";

            h4 {
                font-size: '.($h4_font*12).'px;
            }

            h5 {
                font-size: '.($h5_font*17.3).'px;
            }
        ');
    } 
}



