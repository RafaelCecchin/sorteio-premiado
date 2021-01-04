<?php

include '../../../_bd/conexao_bd.php';  
header("Content-type: text/css");

session_start();

retornaTamanhoFontes();

function retornaTamanhoFontes() {
    $conn = getConnection();
    $sql = 'SELECT extrato_app_font, extrato_print_font FROM usuarios WHERE id = ?';
    $stmt = $conn-> prepare($sql);
    $stmt-> bindValue(1, $_SESSION['id']);
    
    if ($stmt-> execute()) {
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $extrato_app_font = $retorno->extrato_app_font;
        $extrato_print_font = $retorno->extrato_print_font;

        echo('
            @charset "utf-8";

            #retorno td {
                font-size: '.($extrato_app_font*12).'px;
            }

            #retorno .separador {
                font-size: '.($extrato_app_font*17.3).'px;
            }

            @media print {
                #retorno td {
                    font-size: '.($extrato_print_font*12).'px;
                }
    
                #retorno .separador {
                    font-size: '.($extrato_print_font*17.3).'px;
                }

                #retorno h4 {
                    font-size: 0px;
                }
            }
        ');
    } 
}



