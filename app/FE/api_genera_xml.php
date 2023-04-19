<?php

namespace App\FE;
use DOMDocument;

class api_genera_xml{
    //Crear la función para generar el XML de una boleta o factura
    public function crea_xml_invoice(){
        $doc = new DOMDocument();
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = true;
        $doc->encoding = 'UTF-8';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <invoice>
            <cliente>FERNANDO QUIROZ</cliente>
            <fecha>2023-04-19</fecha>
            <total>2000</total>
            <producto>
                <cantidad>1</cantidad>
                <precio>2000</precio>
                <descripcion>LAPTOP GAMER ASUS</descripcion>
            </producto>
        </invoice>';
        
        $doc->loadXML($xml);
        $doc->save('cpe/xml/ejemplo.xml'); //asegurarse de crear la carpeta en public
    }
}


?>