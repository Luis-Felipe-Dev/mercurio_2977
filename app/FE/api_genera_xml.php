<?php

namespace App\FE;

use DOMDocument;

class api_genera_xml
{
    //Crear la función para generar el XML de una boleta o factura
    public function crea_xml_invoice()
    {
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

    public function crea_xml_ejercicio1($alumno, $cursos)
    {
        $doc = new DOMDocument();
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = true;
        $doc->encoding = 'UTF-8';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <alumno>
                <codigo>' . $alumno['codigo'] . '</codigo>
                <dni>' . $alumno['dni'] . '</dni>
                <nombres>' . $alumno['nombres'] . '</nombres>
                <apellidos>' . $alumno['apellidos'] . '</apellidos>
                <carrera>' . $alumno['carrera'] . '</carrera>';
        foreach ($cursos as $key => $curso) {
            //$xml += '';
            $xml = $xml .
                '<curso>
                        <periodo>' . $curso['periodo'] . '</periodo>
                        <NRC>' . $curso['NRC'] . '</NRC>
                        <nombre>' . $curso['nombre'] . '</nombre>
                        <creditos>' . $curso['creditos'] . '</creditos>
                    </curso>';
        }
        $xml = $xml . '</alumno>';
        $doc->loadXML($xml);
        $doc->save('cpe/xml/ejercicio1.xml'); //asegurarse de crear la carpeta en public
        return 1;
    }

    public function crear_xml_ep1($profesores, $cursos)
    {
        $doc = new DOMDocument();
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = true;
        $doc->encoding = 'UTF-8';

        $xml = '<?xml version="1.0" encoding="UTF-8"?><isil>';
        foreach ($profesores as $key => $profesor) {
            $xml = $xml .
                '<profesor>
                    <codigo>' . $profesor['codigo'] . '</codigo>
                    <nombres>' . $profesor['nombres'] . '</nombres>
                    <apellidos>' . $profesor['apellidos'] . '</apellidos>
                    <dni>' . $profesor['dni'] . '</dni>
                    <especialidad>' . $profesor['especialidad'] . '</especialidad>';
            foreach ($cursos as $key => $curso) {
                if ($curso['codigo_profesor'] == $profesor['codigo']) {
                    $xml = $xml .
                        '<curso>
                        <codigo_profesor>' . $curso['codigo_profesor'] . '</codigo_profesor>
                        <periodo>' . $curso['periodo'] . '</periodo>
                        <NRC>' . $curso['NRC'] . '</NRC>
                        <nombre>' . $curso['nombre'] . '</nombre>
                        <creditos>' . $curso['creditos'] . '</creditos>
                        <horario>' . $curso['horario'] . '</horario>
                        <horas_semana>' . $curso['horas_semana'] . '</horas_semana>
                        <modalidad>' . $curso['modalidad'] . '</modalidad>
                    </curso>';
                }
            }
            $xml = $xml . '</profesor>';
        }
        $xml = $xml . '</isil>';

        $doc->loadXML($xml); //carga y convierte el texto en un XML
        $doc->save('cpe/xml/profesor_cursos.xml'); // guardamos el xml generado
        return 1;
    }
}
