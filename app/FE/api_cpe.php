<?php

namespace App\FE;

use ZipArchive;

class api_cpe{

    public function enviar_invoice($emisor, $nombreXML, $ruta_certificado = 'cpe/certificado_digital/', $ruta_archivo_xml = 'cpe/xml/', $ruta_archivo_cdr = 'cpe/cdr/')
    {
        //01. FIRMAR DIGITALMENTE EL XML
        $objFirma = new Signature();
        $flag_firma = 0; //posicion de la etiqueta del XML donde se firmara
        $ruta = $ruta_archivo_xml . $nombreXML . '.XML';//el archivo XML a firmar
        $certificado = $ruta_certificado . 'certificado_prueba_sunat.pfx';
        $pass_certificado = 'isil';
        $resp_hash = $objFirma->signature_xml($flag_firma, $ruta, $certificado, $pass_certificado);
        echo '</br> 0.1: XML FIRMADO DIGITALMENTE hash: ' . $resp_hash['hash_cpe'];


        //02. COMPRIMIR EN FORMATO ZIP EL XML
        $zip = new ZipArchive();
        $nombre_zip = $nombreXML . '.ZIP';
        $ruta_zip = $ruta_archivo_xml . $nombre_zip;

        if ($zip->open($ruta_zip, ZipArchive::CREATE) == true) {
            $zip->addFile($ruta, $nombreXML . '.XML');
            $zip->close();
        }

        echo '</br> 0.2: XML CONVERTIDO EN ZIP';

        //03. CODIFICAR EL ZIP EN BASE 64 (OBJETIVO CONVERTIR EN UNA CADENA DE CARACTERES EL ARCHIVO ZIP)
        $contenido_zip_codificado = base64_encode(file_get_contents($ruta_zip));

        echo '</br> 0.3: ARCHIVO ZIP CODIFICADO EN BASE64: ' . $contenido_zip_codificado;
    }

}

?>
