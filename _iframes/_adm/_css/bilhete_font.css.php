<?php

include '../../../_bd/conexao_bd.php';  
header("Content-type: text/css");

session_start();

retornaTamanhoFontes();

function retornaTamanhoFontes() {
    $conn = getConnection();
    $sql = 'SELECT bilhete_app_font, bilhete_print_font FROM usuarios WHERE id = ?';
    $stmt = $conn-> prepare($sql);
    $stmt-> bindValue(1, $_SESSION['id']);
    
    if ($stmt-> execute()) {
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $bilhete_app_font = $retorno->bilhete_app_font;
        $bilhete_print_font = $retorno->bilhete_print_font;

        echo('
            @charset "utf-8";

            #retorno h5 {
                font-size: '.($bilhete_app_font*15).'px;
            }

            #retorno b {
                font-size: '.($bilhete_app_font*26).'px;
            }

            @media print {
                #retorno h5 {
                    font-size: '.($bilhete_print_font*15).'px;
                }

                #retorno b {
                    font-size: '.($bilhete_print_font*26).'px;
                }

                #retorno h4 {
                    font-size: 0px !important;
                }
            }
        ');
    } 
}



