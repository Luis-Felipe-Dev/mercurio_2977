<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FE\api_genera_xml;

class EP1Controller extends Controller
{
    public function index()
    {
        $profesores = array(
            array(
                'codigo'            => 'COD001',
                'nombres'           => 'FERNANDO JACOBO',
                'apellidos'         => 'QUIROZ CABANILLAS',
                'dni'               => '12345678',
                'especialidad'      => 'SOFTWARE'
            ),
            array(
                'codigo'            => 'COD002',
                'nombres'           => 'MARÍA FERNANDA',
                'apellidos'         => 'CORNEJO SOTO',
                'dni'               => '12345678',
                'especialidad'      => 'IDIOMAS'
            )
        );
        

        $cursos = array(
            array(
                'codigo_profesor'   =>  'COD001',
                'periodo'       =>  '202310',
                'NRC'           =>  2977,
                'nombre'        =>  'INTEGRACION DE APLICACIONES',
                'creditos'      =>  3,
                'horario'       =>  '07:00:00',
                'horas_semana'  =>  18,
                'modalidad'     =>  'REMOTO'
            ),
            array(
                'codigo_profesor'   =>  'COD001',
                'periodo'       =>  '202310',
                'NRC'           =>  2578,
                'nombre'        =>  'ADMINISTRACIÓN DE BASE DE DATOS',
                'creditos'      =>  4,
                'horario'       =>  '19:00:00',
                'horas_semana'  =>  20,
                'modalidad'     =>  'PRESENCIAL - SAN ISIDRO'
            ),
            array(
                'codigo_profesor'   =>  'COD001',
                'periodo'       =>  '202310',
                'NRC'           =>  2496,
                'nombre'        =>  'INTRODUCCION A LA PROGRAMACION',
                'creditos'      =>  3,
                'horario'       =>  '13:00:00',
                'horas_semana'  =>  15,
                'modalidad'     =>  'PRESENCIAL - MIRAFLORES'
            ),
            array(
                'codigo_profesor'   =>  'COD002',
                'periodo'       =>  '202310',
                'NRC'           =>  2632,
                'nombre'        =>  'IDIOMA EXTRANJERO I',
                'creditos'      =>  2,
                'horario'       =>  '07:00:00',
                'horas_semana'  =>  12,
                'modalidad'     =>  'VIRTUAL'
            ),
            array(
                'codigo_profesor'   =>  'COD002',
                'periodo'       =>  '202310',
                'NRC'           =>  2547,
                'nombre'        =>  'IDIOMA EXTRANJERO II',
                'creditos'      =>  2,
                'horario'       =>  '19:00:00',
                'horas_semana'  =>  12,
                'modalidad'     =>  'VIRTUAL'
            ),
            array(
                'codigo_profesor'   =>  'COD002',
                'periodo'       =>  '202310',
                'NRC'           =>  2489,
                'nombre'        =>  'IDIOMA EXTRANJERO III',
                'creditos'      =>  2,
                'horario'       =>  '13:00:00',
                'horas_semana'  =>  12,
                'modalidad'     =>  'VIRTUAL'
            )
        );

        $objCrearXML = new api_genera_xml();
        $objCrearXML->crear_xml_ep1($profesores, $cursos); 

        echo 'Se creo el xml de EP1';
    }
}
