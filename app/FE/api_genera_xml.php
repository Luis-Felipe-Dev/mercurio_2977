<?php

namespace App\FE;

use DOMDocument;

class api_genera_xml
{

    //crear los XML de factura y boleta [EXCLUSIVO LAS FACTURAS SON ENVIADAS POR ESTE METODO]
    public function crea_xml_invoice($nombreXML, $emisor, $cliente, $comprobante, $detalle, $cuotas = null)
    {
        $doc = new DOMDocument(); //clase que me permite gestionar XML
        $doc->preserveWhiteSpace = true;
        $doc->encoding = 'utf-8';
        $doc->formatOutput = false;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
            <ext:UBLExtensions>
                <ext:UBLExtension>
                    <ext:ExtensionContent />
                </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
            <cbc:CustomizationID>2.0</cbc:CustomizationID>
            <cbc:ID>' . $comprobante['serie'] . '-' . $comprobante['correlativo'] . '</cbc:ID>
            <cbc:IssueDate>' . $comprobante['fecha_emision'] . '</cbc:IssueDate>
            <cbc:IssueTime>00:00:00</cbc:IssueTime>
            <cbc:DueDate>' . $comprobante['fecha_vencimiento'] . '</cbc:DueDate>
            <cbc:InvoiceTypeCode listID="0101">' . $comprobante['tipodoc'] . '</cbc:InvoiceTypeCode>
            <cbc:Note languageLocaleID="1000"><![CDATA[' . $comprobante['total_texto'] . ']]></cbc:Note>
            <cbc:DocumentCurrencyCode>' . $comprobante['moneda'] . '</cbc:DocumentCurrencyCode>
            <cac:Signature>
                <cbc:ID>' . $emisor['nrodoc'] . '</cbc:ID>
                <cbc:Note><![CDATA[' . $emisor['nombre_comercial'] . ']]></cbc:Note>
                <cac:SignatoryParty>
                    <cac:PartyIdentification>
                    <cbc:ID>' . $emisor['nrodoc'] . '</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                    <cbc:Name><![CDATA[' . $emisor['razon_social'] . ']]></cbc:Name>
                    </cac:PartyName>
                </cac:SignatoryParty>
                <cac:DigitalSignatureAttachment>
                    <cac:ExternalReference>
                    <cbc:URI>#SIGN-EMPRESA</cbc:URI>
                    </cac:ExternalReference>
                </cac:DigitalSignatureAttachment>
            </cac:Signature>
            <cac:AccountingSupplierParty>
                <cac:Party>
                    <cac:PartyIdentification>
                    <cbc:ID schemeID="' . $emisor['tipodoc'] . '">' . $emisor['nrodoc'] . '</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                    <cbc:Name><![CDATA[' . $emisor['nombre_comercial'] . ']]></cbc:Name>
                    </cac:PartyName>
                    <cac:PartyLegalEntity>
                    <cbc:RegistrationName><![CDATA[' . $emisor['razon_social'] . ']]></cbc:RegistrationName>
                    <cac:RegistrationAddress>
                        <cbc:ID>' . $emisor['ubigeo'] . '</cbc:ID>
                        <cbc:AddressTypeCode>0000</cbc:AddressTypeCode>
                        <cbc:CitySubdivisionName>NONE</cbc:CitySubdivisionName>
                        <cbc:CityName>' . $emisor['provincia'] . '</cbc:CityName>
                        <cbc:CountrySubentity>' . $emisor['departamento'] . '</cbc:CountrySubentity>
                        <cbc:District>' . $emisor['distrito'] . '</cbc:District>
                        <cac:AddressLine>
                            <cbc:Line><![CDATA[' . $emisor['direccion'] . ']]></cbc:Line>
                        </cac:AddressLine>
                        <cac:Country>
                            <cbc:IdentificationCode>' . $emisor['pais'] . '</cbc:IdentificationCode>
                        </cac:Country>
                    </cac:RegistrationAddress>
                    </cac:PartyLegalEntity>
                </cac:Party>
            </cac:AccountingSupplierParty>
            <cac:AccountingCustomerParty>
                <cac:Party>
                    <cac:PartyIdentification>
                    <cbc:ID schemeID="' . $cliente['tipodoc'] . '">' . $cliente['nrodoc'] . '</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyLegalEntity>
                    <cbc:RegistrationName><![CDATA[' . $cliente['razon_social'] . ']]></cbc:RegistrationName>
                    <cac:RegistrationAddress>
                        <cac:AddressLine>
                            <cbc:Line><![CDATA[' . $cliente['direccion'] . ']]></cbc:Line>
                        </cac:AddressLine>
                        <cac:Country>
                            <cbc:IdentificationCode>' . $cliente['pais'] . '</cbc:IdentificationCode>
                        </cac:Country>
                    </cac:RegistrationAddress>
                    </cac:PartyLegalEntity>
                </cac:Party>
            </cac:AccountingCustomerParty>';

        if ($comprobante['tipodoc'] == '01') {
            if ($comprobante['forma_pago'] == 'Contado') {
                $xml = $xml . '<cac:PaymentTerms>
                                        <cbc:ID>FormaPago</cbc:ID>
                                        <cbc:PaymentMeansID>' . $comprobante['forma_pago'] . '</cbc:PaymentMeansID>
                                </cac:PaymentTerms>';
            }

            if ($comprobante['forma_pago'] == 'Credito') {
                $xml = $xml . '<cac:PaymentTerms>
                                        <cbc:ID>FormaPago</cbc:ID>
                                        <cbc:PaymentMeansID>' . $comprobante['forma_pago'] . '</cbc:PaymentMeansID>
                                        <cbc:Amount currencyID="PEN">' . $comprobante['monto_pendiente'] . '</cbc:Amount>
                                </cac:PaymentTerms>';

                foreach ($cuotas as $key => $value) {
                    $xml = $xml .
                        '<cac:PaymentTerms>
                                <cbc:ID>FormaPago</cbc:ID>
                                <cbc:PaymentMeansID>' . $value['cuota'] . '</cbc:PaymentMeansID>
                                <cbc:Amount currencyID="PEN">' . $value['monto'] . '</cbc:Amount>
                                <cbc:PaymentDueDate>' . $value['fecha'] . '</cbc:PaymentDueDate>
                            </cac:PaymentTerms>';
                }
            }
        }

        $xml = $xml . '<cac:TaxTotal>
                <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">' . ($comprobante['igv'] + $comprobante['total_impbolsas']) . '</cbc:TaxAmount>';

        if ($comprobante['total_opgravadas'] > 0) {
            $xml .= '<cac:TaxSubtotal>
                        <cbc:TaxableAmount currencyID="' . $comprobante['moneda'] . '">' . $comprobante['total_opgravadas'] . '</cbc:TaxableAmount>
                        <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">' . $comprobante['igv'] . '</cbc:TaxAmount>
                        <cac:TaxCategory>
                        <cac:TaxScheme>
                            <cbc:ID>1000</cbc:ID>
                            <cbc:Name>IGV</cbc:Name>
                            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                        </cac:TaxCategory>
                    </cac:TaxSubtotal>';
        }

        if ($comprobante['total_opexoneradas'] > 0) {
            $xml .= '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $comprobante['moneda'] . '">' . $comprobante['total_opexoneradas'] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">0.00</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                        <cac:TaxScheme>
                            <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                            <cbc:Name>EXO</cbc:Name>
                            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                    </cac:TaxSubtotal>';
        }

        if ($comprobante['total_opinafectas'] > 0) {
            $xml .= '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $comprobante['moneda'] . '">' . $comprobante['total_opinafectas'] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">0.00</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                        <cac:TaxScheme>
                            <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                            <cbc:Name>INA</cbc:Name>
                            <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                    </cac:TaxSubtotal>';
        }

        if ($comprobante['total_opgratuitas_1'] > 0) {
            $xml .= '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $comprobante['moneda'] . '">' . $comprobante['total_opgratuitas_1'] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">' . $comprobante['total_opgratuitas_2'] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                        <cac:TaxScheme>
                            <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
                            <cbc:Name>GRA</cbc:Name>
                            <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                    </cac:TaxSubtotal>';
        }

        if ($comprobante['total_impbolsas'] > 0) {

            $xml .= '<cac:TaxSubtotal>
                    <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">' . $comprobante['total_impbolsas'] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cac:TaxScheme>
                            <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">7152</cbc:ID>
                            <cbc:Name>ICBPER</cbc:Name>
                            <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>';
        }

        $total_antes_de_impuestos = $comprobante['total_opgravadas'] + $comprobante['total_opexoneradas'] + $comprobante['total_opinafectas'];

        $xml .= '</cac:TaxTotal>
            <cac:LegalMonetaryTotal>
                <cbc:LineExtensionAmount currencyID="' . $comprobante['moneda'] . '">' . $total_antes_de_impuestos . '</cbc:LineExtensionAmount>
                <cbc:TaxInclusiveAmount currencyID="' . $comprobante['moneda'] . '">' . $comprobante['total'] . '</cbc:TaxInclusiveAmount>
                <cbc:PayableAmount currencyID="' . $comprobante['moneda'] . '">' . $comprobante['total'] . '</cbc:PayableAmount>
            </cac:LegalMonetaryTotal>';

        foreach ($detalle as $k => $v) {


            if ($v['bolsa_plastica'] == 'SI') {

                $xml .= '<cac:InvoiceLine>
                    <cbc:ID>' . $v['item'] . '</cbc:ID>
                    <cbc:InvoicedQuantity unitCode="' . $v['unidad'] . '">' . $v['cantidad'] . '</cbc:InvoicedQuantity>
                    <cbc:LineExtensionAmount currencyID="' . $comprobante['moneda'] . '">' . $v['valor_total'] . '</cbc:LineExtensionAmount>
                    <cac:PricingReference>
                        <cac:AlternativeConditionPrice>
                        <cbc:PriceAmount currencyID="' . $comprobante['moneda'] . '">' . $v['precio_unitario'] . '</cbc:PriceAmount>
                        <cbc:PriceTypeCode>' . $v['tipo_precio'] . '</cbc:PriceTypeCode>
                        </cac:AlternativeConditionPrice>
                    </cac:PricingReference>';

                $xml .= '<cac:TaxTotal>
                            <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">' . ($v['cantidad'] * 0.40 + $v['igv']) . '</cbc:TaxAmount>
                            <cac:TaxSubtotal>
                                    <cbc:TaxableAmount currencyID="' . $comprobante['moneda'] . '">' . $v['valor_total'] . '</cbc:TaxableAmount>
                                    <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">' . $v['igv'] . '</cbc:TaxAmount>
                                    <cac:TaxCategory>
                                    <cbc:Percent>' . $v['porcentaje_igv'] . '</cbc:Percent>
                                    <cbc:TaxExemptionReasonCode>' . $v['tipo_afectacion_igv'] . '</cbc:TaxExemptionReasonCode>
                                    <cac:TaxScheme>
                                        <cbc:ID>' . $v['codigo_tipo_tributo'] . '</cbc:ID>
                                        <cbc:Name>' . $v['nombre_tributo'] . '</cbc:Name>
                                        <cbc:TaxTypeCode>' . $v['tipo_tributo'] . '</cbc:TaxTypeCode>
                                    </cac:TaxScheme>
                                    </cac:TaxCategory>
                            </cac:TaxSubtotal>
                            <cac:TaxSubtotal>
                                <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">' . ($v['cantidad'] * 0.40) . '</cbc:TaxAmount>
                                <cbc:BaseUnitMeasure unitCode="' . $v['unidad'] . '">' . $v['cantidad'] . '</cbc:BaseUnitMeasure>
                                <cac:TaxCategory>
                                    <cbc:PerUnitAmount currencyID="PEN">0.40</cbc:PerUnitAmount>
                                    <cac:TaxScheme>
                                        <cbc:ID schemeName="Codigo de tributos" schemeAgencyName="PE:SUNAT" >7152</cbc:ID>
                                        <cbc:Name>ICBPER</cbc:Name>
                                        <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                                    </cac:TaxScheme>
                                </cac:TaxCategory>
                            </cac:TaxSubtotal>';
            } else {
                $xml .= '<cac:InvoiceLine>
                    <cbc:ID>' . $v['item'] . '</cbc:ID>
                    <cbc:InvoicedQuantity unitCode="' . $v['unidad'] . '">' . $v['cantidad'] . '</cbc:InvoicedQuantity>
                    <cbc:LineExtensionAmount currencyID="' . $comprobante['moneda'] . '">' . $v['valor_total'] . '</cbc:LineExtensionAmount>
                    <cac:PricingReference>
                        <cac:AlternativeConditionPrice>
                        <cbc:PriceAmount currencyID="' . $comprobante['moneda'] . '">' . $v['precio_unitario'] . '</cbc:PriceAmount>
                        <cbc:PriceTypeCode>' . $v['tipo_precio'] . '</cbc:PriceTypeCode>
                        </cac:AlternativeConditionPrice>
                    </cac:PricingReference>';

                $xml .= '<cac:TaxTotal>
                    <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">' . $v['igv'] . '</cbc:TaxAmount>
                    <cac:TaxSubtotal>
                        <cbc:TaxableAmount currencyID="' . $comprobante['moneda'] . '">' . $v['valor_total'] . '</cbc:TaxableAmount>
                        <cbc:TaxAmount currencyID="' . $comprobante['moneda'] . '">' . $v['igv'] . '</cbc:TaxAmount>
                        <cac:TaxCategory>
                            <cbc:Percent>' . $v['porcentaje_igv'] . '</cbc:Percent>
                            <cbc:TaxExemptionReasonCode>' . $v['tipo_afectacion_igv'] . '</cbc:TaxExemptionReasonCode>
                            <cac:TaxScheme>
                                <cbc:ID>' . $v['codigo_tipo_tributo'] . '</cbc:ID>
                                <cbc:Name>' . $v['nombre_tributo'] . '</cbc:Name>
                                <cbc:TaxTypeCode>' . $v['tipo_tributo'] . '</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                        </cac:TaxCategory>
                    </cac:TaxSubtotal>';
            }


            $xml .= '</cac:TaxTotal>
                    <cac:Item>
                        <cbc:Description><![CDATA[' . $v['descripcion'] . ']]></cbc:Description>
                        <cac:SellersItemIdentification>
                        <cbc:ID>' . $v['codigo'] . '</cbc:ID>
                        </cac:SellersItemIdentification>
                    </cac:Item>
                    <cac:Price>
                        <cbc:PriceAmount currencyID="' . $comprobante['moneda'] . '">' . $v['valor_unitario'] . '</cbc:PriceAmount>
                    </cac:Price>
                </cac:InvoiceLine>';
        }

        $xml .= "</Invoice>";

        $doc->loadXML($xml); //cargo o convierto el texto a XML
        $doc->save($nombreXML . '.XML'); //guardando el XML en disco
        return 1;
    }

    public function crea_xml_alumno_cursos($alumno, $cursos)
    {
        $doc = new DOMDocument(); //clase que me permite gestionar XML
        $doc->preserveWhiteSpace = true;
        $doc->encoding = 'utf-8';
        $doc->formatOutput = false;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <alumno>
                    <codigo>' . $alumno['codigo'] . '</codigo>
                    <dni>' . $alumno['dni'] . '</dni>
                    <nombres>' . $alumno['nombres'] . '</nombres>
                    <apellidos>' . $alumno['apellidos'] . '</apellidos>
                    <carrera>' . $alumno['carrera'] . '</carrera>';

        foreach ($cursos as $key => $curso) {
            $xml = $xml . '
                    <curso>
                        <periodo>' . $curso['periodo'] . '</periodo>
                        <NRC>' . $curso['NRC'] . '</NRC>
                        <nombre>' . $curso['nombre'] . '</nombre>
                        <creditos>' . $curso['creditos'] . '</creditos>
                    </curso>';
        }

        $xml = $xml . '
        </alumno>';

        $doc->loadXML($xml); //cargo o convierto el texto a XML
        $doc->save('cpe/xml/alumno_cursos.XML'); //guardando el XML en disco
        return 1;
    }

    public function crear_xml_ep1($profesores, $cursos)
    {
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;
        $doc->encoding = 'utf-8';
        $doc->formatOutput = false;

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
        $doc->save('cpe/xml/ep1.xml'); // guardamos el xml generado
        return 1;
    }
    
    public function promedio_ponderado($cursos, $codigo_alumno)
    {
        $notas = array();
        $promedio_ponderado = 0;
        foreach ($cursos as $key => $curso) {
            if ($curso['codigo_alumno'] == $codigo_alumno) {
                array_push($notas, $curso['nota']);
            }
        }
        foreach($notas as $key => $nota){
            $promedio_ponderado += $nota;
        }
        return round($promedio_ponderado / count($notas));
    }

    public function crear_xml_ep2($alumnos, $periodos, $cursos)
    {
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;
        $doc->encoding = 'utf-8';
        $doc->formatOutput = false;

        $xml = '<?xml version="1.0" encoding="UTF-8"?><isil>';
        foreach ($alumnos as $key => $alumno) {
            $promedio_ponderado = 0;
            $xml = $xml .
                '<alumno>
                <codigo_alumno>' . $alumno['codigo_alumno'] . '</codigo_alumno>
                <nombres_apellidos>' . $alumno['nombres_apellidos'] . '</nombres_apellidos>
                <dni>' . $alumno['dni'] . '</dni>
                <carrera>' . $alumno['carrera'] . '</carrera>';
            foreach ($periodos as $key => $periodo) {
                if ($periodo['codigo_alumno'] == $alumno['codigo_alumno']) {
                    $xml = $xml .
                        '<periodo>
                        <periodo>' . $periodo['periodo'] . '</periodo>
                        <codigo_alumno>' . $periodo['codigo_alumno'] . '</codigo_alumno>
                        <promedio_ponderado>' . $this->promedio_ponderado($cursos, $periodo['codigo_alumno']) . '</promedio_ponderado>';
                foreach ($cursos as $key => $curso) {
                    if ($periodo['periodo'] == $curso['periodo']) {
                        $xml = $xml .
                        '<curso>
                        <nrc>' . $curso['nrc'] . '</nrc>
                        <nombre>' . $curso['nombre'] . '</nombre>
                        <periodo>' . $curso['periodo'] . '</periodo>
                        <codigo_alumno>' . $curso['codigo_alumno'] . '</codigo_alumno>
                        <nota>' . $curso['nota'] . '</nota>
                        </curso>';
                        $promedio_ponderado += $curso['nota'];
                    }
                }
                $xml = $xml . '</periodo>';
                }
            }
            $xml = $xml . '</alumno>';
        }
        $xml = $xml . '</isil>';

        $doc->loadXML($xml); //carga y convierte el texto en un xml
        $doc->save('cpe/xml/ep2.xml'); //guardamos el xml generado
        return 1;
    }
}
