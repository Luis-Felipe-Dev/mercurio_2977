<?php

namespace App\FE;

use DOMDocument;
use PhpParser\Node\Expr\PostDec;
use ZipArchive;

class api_cpe{

    public function enviar_invoice($emisor, $nombreXML, $ruta_certificado = 'cpe/certificado_digital/', $ruta_archivo_xml = 'cpe/xml/', $ruta_archivo_cdr = 'cpe/cdr/')
    {
        $estado_envio = 0; //iniciamos el envio, no se ha enviado aun nada
        $estado_envio_mensaje = 'iniciamos el envio, no se ha enviado aún nada';

        //01. FIRMAR DIGITALMENTE EL XML
        $objFirma = new Signature();
        $flag_firma = 0; //posicion de la etiqueta del XML donde se firmara
        $ruta = $ruta_archivo_xml . $nombreXML . '.XML';//el archivo XML a firmar
        $certificado = $ruta_certificado . 'certificado_prueba_sunat.pfx';
        $pass_certificado = 'isil';
        $resp_hash = $objFirma->signature_xml($flag_firma, $ruta, $certificado, $pass_certificado);
        $estado_envio = 1;
        $estado_envio_mensaje = 'El xml se firmó digitalmente';
        echo '</br> 0.1: XML FIRMADO DIGITALMENTE hash: ' . $resp_hash['hash_cpe'];

        //02. COMPRIMIR EN FORMATO ZIP EL XML
        $zip = new ZipArchive();
        $nombre_zip = $nombreXML . '.ZIP';
        $ruta_zip = $ruta_archivo_xml . $nombre_zip;

        if ($zip->open($ruta_zip, ZipArchive::CREATE) == true) {
            $zip->addFile($ruta, $nombreXML . '.XML');
            $zip->close();
        }
        $estado_envio = 2;
        $estado_envio_mensaje = 'El xml firmado digitalmente se comprimio en formato .ZIP';
        echo '</br> 0.2: XML CONVERTIDO EN ZIP';


        //03. CODIFICAR EL ZIP EN BASE 64 (OBJETIVO CONVERTIR EN UNA CADENA DE CARACTERES EL ARCHIVO ZIP)
        $contenido_zip_codificado = base64_encode(file_get_contents($ruta_zip));
        $estado_envio = 3;
        $estado_envio_mensaje = 'El archivo XML.ZIP se codifico en base64';
        echo '</br> 0.3: ARCHIVO ZIP CODIFICADO EN BASE64: ' . $contenido_zip_codificado;


        //04. CONSUMIR LOS WEB SERVICE DE SUNAT
        $url_ws = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>' . $emisor['nrodoc'] . $emisor['usuario_secundario'] . '</wsse:Username>
                    <wsse:Password>' . $emisor['clave_usuario_secundario'] . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <ser:sendBill>
                <fileName>' . $nombre_zip . '</fileName>
                <contentFile>' . $contenido_zip_codificado . '</contentFile>
            </ser:sendBill>
        </soapenv:Body>
    </soapenv:Envelope>';

    //1.INICIALIZAR EL CURL
    $ch = curl_init();

    //2.ASIGNAR LOS VALORES A LOS PARAMETROS DEL CURL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    curl_setopt($ch, CURLOPT_URL, $url_ws); //url,
    curl_setopt($ch, CURLOPT_POST, true); //method HTTP POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);//campos

    //3.EJECUTAR CURL
    $respuesta = curl_exec($ch);//xml
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $estado_envio = 4;
    $estado_envio_mensaje = 'Se realizó en consumo del web service de SUNAT SendBill';
    echo '</br> 0.4 CONSUMO DE SERVICIO WEB DE SUNAT SENDBILL';

    //RESPUETA /RECEPCION DEL WS
    $descripcion = '';
    $nota = '';
    $codigo_error = '';
    $mensaje_error = '';

    if ($http_code == 200) { //ok
        $doc = new DOMDocument();
        $doc->loadXML($respuesta);

        if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
            $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
            $estado_envio = 5;
            $estado_envio_mensaje = 'SUNAT aprobó el comprobante, se obtuvo la constancia de recepcion: CDR';

            //decodificar en base64 el XML-CDR
            $cdr = base64_decode($cdr);
            $estado_envio = 6;
            $estado_envio_mensaje = 'Se decodifico en base64 el CDR';

            //copiar en disco el CDR porque esta en memoria el ZIP
            file_put_contents($ruta_archivo_cdr . 'R-' . $nombre_zip, $cdr);
            $estado_envio = 7;
            $estado_envio_mensaje = 'CDR-zip (constancia de recepcion) copiada a disco local';

            //extraer el ZIP
            $zip = new ZipArchive();

            if ($zip->open($ruta_archivo_cdr . 'R-' . $nombre_zip) == TRUE) {
                $zip->extractTo($ruta_archivo_cdr);//obtenemos el XML del CDR
                $zip->close();

                //obtener el archivo XML-CDR
                $xml_cdr = $ruta_archivo_cdr . 'R-' . $nombreXML . '.XML';
                $doc_cdr = new DOMDocument();
                $doc_cdr->loadXML(file_get_contents($xml_cdr));

                if (isset($doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue)) {
                    $descripcion = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                }

                if (isset($doc_cdr->getElementsByTagName('Note')->item(0)->nodeValue)) {
                    $nota = $doc_cdr->getElementsByTagName('Note')->item(0)->nodeValue;
                }

                $estado_envio = 8;
                $estado_envio_mensaje = 'PROCESO TERMINADO';
                echo '</br> PROCESO TERMINADO';
            }
        } else {
            $codigo_error = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
            $mensaje_error = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
            $estado_envio = 9;
            $estado_envio_mensaje = 'ERROR/RECHAZO DE SUNAT';
        }
    } else {
        $doc_error = new DOMDocument();
        $doc_error->load($respuesta);
        $codigo_error = $doc_error->getElementsByTagName('faultcode')->item(0)->nodeValue;
        $mensaje_error = $doc_error->getElementsByTagName('faultstring')->item(0)->nodeValue;

        curl_error($ch);
        $estado_envio = 10;
        $estado_envio_mensaje = 'ERROR DE CONSUMO DE WS/CONEXION';

        $respuesta = 'ERROR EN CONSUMO DE WS/RED/CONEXION ' . $respuesta;
    }

    //4.CERRAMOS EL CURL
    curl_close($ch);

    $resultado_envio = array(
        'estado_envio'              =>  $estado_envio,
        'estado_envio_mensaje'      =>  $estado_envio_mensaje,
        'hash_cpe'                  =>  $resp_hash['hash_cpe'],
        'descripcion'               =>  $descripcion,
        'nota'                      =>  $nota,
        'codigo_error'              =>  str_replace('soap-env:Client.', '', $codigo_error),
        'mensaje_error'             =>  $mensaje_error,
        'http_code'                 =>  $http_code,
        'respuesta'                 =>  $respuesta
    );

    return $resultado_envio;

    }

}