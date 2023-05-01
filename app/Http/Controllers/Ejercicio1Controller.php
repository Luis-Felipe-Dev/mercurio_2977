<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FE\api_genera_xml;

class Ejercicio1Controller extends Controller
{
    public function index()
    {
        $alumno = array(
            'codigo'    =>  'COD001',
            'dni'       =>  '12345678',
            'nombres'   =>  'GANDALF',
            'apellidos' =>  'RINGS',
            'carrera'   =>  'CINE'
        );

        $cursos = array(
            array(
                'periodo'   =>  '202310',
                'NRC'       =>  2977,
                'nombre'    =>  'INTEGRACION DE APLICACIONES',
                'creditos'  =>  3
            ),
            array(
                'periodo'   =>  '202310',
                'NRC'       =>  1001,
                'nombre'    =>  'ADMINISTRACION DE BASE DE DATOS',
                'creditos'  =>  3
            )
        );
        
        $objCreaXML = new api_genera_xml();
        $objCreaXML->crea_xml_ejercicio1($alumno, $cursos);        

        echo 'Se creo el xml de ejercicio 1';
    }
}
