<?php

namespace App\Http\Controllers;

use App\FE\api_cpe;
use App\FE\api_genera_xml;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FacturaController extends Controller
{
    public function index()
    {
        //Estructura de datos Emisor: Es la empresa que brinda el servicio o el bien
        //las variables empiezan con el simbolo dolar: $
        $emisor = array(
            'tipodoc'                   =>  '6', //DNI:1, RUC: 6
            'nrodoc'                    =>  '20123456789',
            'razon_social'              =>  'ISIL SA',
            'nombre_comercial'          =>  'ISIL',
            'direccion'                 =>  'LIMA',
            'ubigeo'                    =>  '010101',
            'departamento'              =>  'LIMA',
            'provincia'                 =>  'LIMA',
            'distrito'                  =>  'LIMA',
            'pais'                      =>  'PE',
            'usuario_secundario'        =>  'MODDATOS', //usuario para autentircase con los servicios web de SUNAT
            'clave_usuario_secundario'  =>  'MODDATOS'
        );

        $cliente = array(
            'tipodoc'                   =>  '6', //DNI:1, RUC: 6
            'nrodoc'                    =>  '20987456321',
            'razon_social'              =>  'BRUCE WAYNE',
            'direccion'                 =>  'GOTIKA',
            'pais'                      =>  'PE',
        );


        $comprobante = array(
            'tipodoc'                   =>  '01', //boleta: 03, factura: 01
            'serie'                     =>  'F001',
            'correlativo'               =>  '123',
            'fecha_emision'             =>  date('Y-m-d'),
            'hora'                      =>  '00:00:20',
            'fecha_vencimiento'         =>  date('Y-m-d'),
            'moneda'                    =>  'PEN', //SOLES: PEN, DOLARES: USD
            'total_opgravadas'          =>  0.00,
            'total_opexoneradas'        =>  0.00,
            'total_opinafectos'         =>  0.00,
            'total_impbolsas'           =>  0.00,
            'total_opgratuitas_1'       =>  0.00,
            'total_opgratuitas_2'       =>  0.00,
            'igv'                       =>  0.00,
            'total'                     =>  0.00,
            'total_texto'               =>  '',
            'forma_pago'                => 'Credito',  //Contado, Credito
            'monto_pendiente'           =>  500.00
        );

        $cuotas = array(
            array(
                'cuota'                 =>  'Cuota001',
                'monto'                 =>  500.00,
                'fecha'                 =>  '2023-05-30'
            )
        );

        $detalle = array(
            array(
                'item'                  =>  1,
                'codigo'                =>  'PROD001',
                'descripcion'           =>  'LAPTOP ACER',
                'cantidad'              =>  1,
                'precio_unitario'       =>  2360.00, //ya contiene el IGV
                'valor_unitario'        =>  2000.00, //no contiene IGV
                'igv'                   =>  360.00,
                'tipo_precio'           =>  '01', //CODIGO PARA EL PRECIO
                'porcentaje_igv'        =>  18,
                'importe_total'         =>  2360.00, //cantidad por el precio unitario
                'valor_total'           =>  2000.00, //cantidad por el valor unitario
                'unidad'                =>  'NIU', //codigo de unidad de medida
                'bolsa_plastica'        =>  'NO',
                'total_impuesto_bolsas' =>  0.00,

                'tipo_afectacion_igv'   =>  '10', //Gravado:10, Exonerado: 20, Inafecto: 30
                'codigo_tipo_tributo'   =>  '1000',
                'tipo_tributo'          =>  'VAT',
                'nombre_tributo'        =>  'IGV'
            )
        );

        //Inicializar los totales
        $total_opgravadas = 0.00;
        $total_opexoneradas = 0.00;
        $total_opinafectas = 0.00;
        $total_impbolsas = 0.00;
        $igv = 0.00;
        $total = 0.00;
        $op_gratuitas1 = 0.00;
        $op_gratuitas2 = 0.00;

        foreach ($detalle as $key => $value) {
            if ($value['tipo_afectacion_igv'] == 10) { //OP GRAVADAS
                $total_opgravadas += $value['valor_total'];
            }

            if ($value['tipo_afectacion_igv'] == 20) { //OP EXONERADAS
                $total_opexoneradas += $value['valor_total'];
            }

            if ($value['tipo_afectacion_igv'] == 30) { //OP INAFECTAS
                $total_opinafectas += $value['valor_total'];
            }

            $igv += $value['igv'];
            $total_impbolsas += $value['total_impuesto_bolsas'];
            $total += $value['importe_total'] + $total_impbolsas;
        }

        $comprobante['total_opgravadas'] = $total_opgravadas;
        $comprobante['total_opexoneradas'] = $total_opexoneradas;
        $comprobante['total_opinafectas'] = $total_opinafectas;
        $comprobante['total_impbolsas'] = $total_impbolsas;
        $comprobante['total_opgratuitas_1'] = $op_gratuitas1;
        $comprobante['total_opgratuitas_2'] = $op_gratuitas2;
        $comprobante['igv'] = $igv;
        $comprobante['total'] = $total;

        //print_r($emisor);
        //echo '<h1>Bienvenido a Integración de Aplicaciones ISIL 202310</h1>';

        //PARTE I: CREAR EL XML
        $objCreaXML = new api_genera_xml();

        $nombreXML = $emisor['nrodoc'] . '-' . $comprobante['tipodoc'] . '-' . $comprobante['serie'] . '-' . $comprobante['correlativo']; //nombre del XML a guardar
        $rutaXML = 'cpe/xml/'; //ruta donde se guardar los archivos XML

        $resultado = $objCreaXML->crea_xml_invoice($rutaXML . $nombreXML, $emisor, $cliente, $comprobante, $detalle, $cuotas);

        if ($resultado == 1) {
            echo 'PARTE I: SE CREO EL XML CON EXITO';
        } else {
            echo 'PARTE I: ERROR EN CREAR EL XML';
        }

        //PARTE II - CONSUMO DE WEB SERVICE SUNAT Y RESULTADO
        $objCPE = new api_cpe(); //api para el consumo de WS-SUNAT
        echo '</br> PARTE II - CONSUMO WEB SERVICE SUNAT Y RESULTADO';
        $resultado_sunat = $objCPE->enviar_invoice($emisor, $nombreXML);

        echo '</br> Estado de envío: ' . $resultado_sunat['estado_envio'];
        echo '</br> Estado de envío mensaje: ' . $resultado_sunat['estado_envio_mensaje'];
        echo '</br> HASH-CPE: ' . $resultado_sunat['hash_cpe'];
        echo '</br> Descripción: ' . $resultado_sunat['descripcion'];
        echo '</br> Nota: ' . $resultado_sunat['nota'];
        echo '</br> Código de error: ' . $resultado_sunat['codigo_error'];
        echo '</br> Mensaje de error: ' . $resultado_sunat['mensaje_error'];
        echo '</br> HTTP Code: ' . $resultado_sunat['http_code'];
        echo '</br> Respuesta: ' . $resultado_sunat['respuesta'];

    }
}
