<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FE\api_genera_xml;

class EP2Controller extends Controller
{
    public function index()
    {
        $promedio = 0;

        $alumnos = array(
            array(
                'codigo_alumno'         => 'ALUM001',
                'nombres_apellidos'   => 'VICENTE TEJEDA',
                'dni'                   => '87654321',
                'carrera'               => 'DESARROLLO DE SOFTWARE'
            ),
            array(
                'codigo_alumno'         => 'ALUM002',
                'nombres_apellidos'   => 'LUIS HUARANCA',
                'dni'                   => '62345678',
                'carrera'               => 'DESARROLLO DE SOFTWARE'
            ),
            array(
                'codigo_alumno'         => 'ALUM003',
                'nombres_apellidos'   => 'OMAR VILLARREAL',
                'dni'                   => '24516524',
                'carrera'               => 'ING. COMPUTACION E INFORMATICA'
            )
        );

        $periodos = array(
            array(
                'perido'                => '202220',
                'codigo_alumno'         => 'ALUM001',
                'promedio_ponderado'    => 0
            ),
            array(
                'perido'                => '202310',
                'codigo_alumno'         => 'ALUM002',
                'promedio_ponderado'    =>  0
            ),
            array(
                'perido'                => '202210',
                'codigo_alumno'         => 'ALUM003',
                'promedio_ponderado'    =>  0
            )
        );

        $cursos = array(
            array(
                'nrc'               => '5245',
                'nombre'            => 'ANALISIS DEL ENTORNO DE NEGOCIOS',
                'periodo'           => '202310',
                'codigo_alumno'     => 'ALUM002',
                'nota'              => '10'
            ),
            array(
                'nrc'               => '8542',
                'nombre'            => 'ANALISIS Y DISEÑO DE SISTEMAS II',
                'periodo'           => '202310',
                'codigo_alumno'     => 'ALUM002',
                'nota'              => '5'
            ),
            array(
                'nrc'               => '1645',
                'nombre'            => 'INTEGRACION DE APLICACIONES',
                'periodo'           => '202310',
                'codigo_alumno'     => 'ALUM002',
                'nota'              => '20'
            ),
            array(
                'nrc'               => '4562',
                'nombre'            => 'ADMINISTRACIÓN BASE DE DATOS',
                'periodo'           => '202210',
                'codigo_alumno'     => 'ALUM003',
                'nota'              => '16'
            ),
            array(
                'nrc'               => '2145',
                'nombre'            => 'COMUNICACIÓN ESCRITA',
                'periodo'           => '202210',
                'codigo_alumno'     => 'ALUM003',
                'nota'              => '17'
            ),
            array(
                'nrc'               => '3254',
                'nombre'            => 'DESARROLLO DE APLICACIONES EMPRESARIALES II',
                'periodo'           => '202220',
                'codigo_alumno'     => 'ALUM001',
                'nota'              => '16'
            ),
            array(
                'nrc'               => '4542',
                'nombre'            => 'DESARROLLO DE SOLUCIONES CLOUD',
                'periodo'           => '202220',
                'codigo_alumno'     => 'ALUM001',
                'nota'              => '15'
            ),
            array(
                'nrc'               => '2452',
                'nombre'            => 'DESARROLLO DE APLICACIONES EMPRESARIALES I',
                'periodo'           => '202220',
                'codigo_alumno'     => 'ALUM001',
                'nota'              => '19'
            )
        );
        
        $objCrearXML = new api_genera_xml();
        $objCrearXML->crear_xml_ep2($alumnos, $periodos, $cursos);

        echo 'Se creo el xml de EP2';
    }
}
