<?php
include('XMLSecurityDSig.php');
//ПОДПИСЬ

if (!$cert_store = file_get_contents("RSA256_db50cedfbb3eea2b033b82f27d5d19dbda89d27f.p12")) {
    echo "Ошибка: невозможно прочитать файл сертификата\n";
    exit;
}

if (openssl_pkcs12_read($cert_store, $cert_info, "Qwerty12")) {
    echo "Информация о сертификате\n";
    print_r($cert_info);
} else {
    echo "Ошибка: невозможно прочитать хранилище сертификата.\n";
    exit;
}

$fp = fopen("RSA256_db50cedfbb3eea2b033b82f27d5d19dbda89d27f.pem", "r");
$privKey = fread($fp, 8192);
fclose($fp);
$pKeyId = openssl_get_privatekey($privKey, 'Qwerty12');
$data = '';
openssl_sign($data, $signatureVar, $pKeyId, OPENSSL_ALGO_SHA256);
$signature = base64_encode($signatureVar);
// echo $signature;
$pub_key = openssl_pkey_get_public(file_get_contents("RSA256_db50cedfbb3eea2b033b82f27d5d19dbda89d27f.pem")); 
$ok = openssl_verify($data, $signature, $pub_key);
if ($ok == 1) {
    echo "good";
} elseif ($ok == 0) {
    echo "bad";
} else {
    echo "ugly, error checking signature";
}
// ОЧИСТИТЬ ПАМЯТЬ
openssl_free_key($pKeyId);
*/
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);

$UploadInvoiceService = new SoapClient(NULL,  
    array(  
        "location" => "https://esf-test.kgd.gov.kz:9443/esf-web/ws/api1/UploadInvoiceService",
        "uri"	   => "https://esf-test.kgd.gov.kz:9443",   
        "style"    => SOAP_DOCUMENT,  
        "use"      => SOAP_LITERAL,
        "stream_context" => $context
    )); 
$VersionService = new SoapClient(NULL,  
    array(  
        "location" => "https://212.154.167.194:9443/esf-web/ws/api1/VersionService?wsdl",  
        "uri"      => "https://esf-test.kgd.gov.kz:9443",  
        "style"    => SOAP_DOCUMENT,  
        "use"      => SOAP_LITERAL,
        "stream_context" => $context
  
    )); 
$SessionService = new SoapClient(NULL,  
    array(  
        "location" => "https://esf.gov.kz:8443/esf-web/ws/SessionService",  
        "uri"      => "https://esf-test.kgd.gov.kz:9443",  
        "style"    => SOAP_DOCUMENT,  
        "use"      => SOAP_LITERAL,
        "stream_context" => $context
    ));
$InvoiceService = new SoapClient(NULL,  
    array(  
        "location" => "https://esf-test.kgd.gov.kz:9443/esf-web/ws/api1/SessionService",  
        "uri"      => "https://esf-test.kgd.gov.kz:9443",  
        "style"    => SOAP_DOCUMENT,  
        "use"      => SOAP_LITERAL,
        "stream_context" => $context
    ));
$EsfXsdService = new SoapClient(NULL,  
    array(  
        "location" => "https://esf-test.kgd.gov.kz:9443/esf-web/ws/api1/EsfXsdService",  
        "uri"      => "https://esf-test.kgd.gov.kz:9443",  
        "style"    => SOAP_DOCUMENT,  
        "use"      => SOAP_LITERAL,
        "stream_context" => $context
	));

$Version_request_xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
   <soapenv:Header/>
   <soapenv:Body>
      <esf:esfVersionRequest xmlns:esf="esf"/>
   </soapenv:Body>
</soapenv:Envelope>';

$Close_session_xml1 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:esf="esf">
   <soapenv:Header>
      <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
         <wsse:UsernameToken wsu:Id="UsernameToken-664678CEF9FFC67AD214168421472821">
            <wsse:Username>123456789011</wsse:Username>
            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">TestPass123</wsse:Password>
         </wsse:UsernameToken>
      </wsse:Security>
   </soapenv:Header>
   <soapenv:Body>
      <esf:closeSessionRequest>
         <sessionId>'.$SessionId.'</sessionId>
      </esf:closeSessionRequest>
   </soapenv:Body>
</soapenv:Envelope>
';
try{
    $CloseSession1= $SessionService->__doRequest($Close_session_xml1, 'https://esf-test.kgd.gov.kz:9443/esf-web/ws/api1/SessionService', '', SOAP_1_2);
    echo $CloseSession1;
} catch(SoapFault $e){
    echo "Error: ".$e->getMessage().PHP_EOL;
}
$Create_session_xml = '
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:esf="esf"> 
   <soapenv:Header> 
      <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd"> 
         <wsse:UsernameToken wsu:Id="UsernameToken-664678CEF9FFC67AD214168421472821"> 
            <wsse:Username>123456789011</wsse:Username> 
            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">TestPass123</wsse:Password> 
         </wsse:UsernameToken> 
      </wsse:Security> 
   </soapenv:Header> 
   <soapenv:Body> 
      <esf:createSessionRequest> 
         <tin>123456789021</tin> 
         <x509Certificate>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</x509Certificate> 
      </esf:createSessionRequest> 
   </soapenv:Body> 
</soapenv:Envelope>';

try{
	$CreateSession = $SessionService->__doRequest($Create_session_xml, 'https://212.154.167.194:9443/esf-web/ws/api1/SessionService?wsdl', '', SOAP_1_2);// ОТПРАВКА ЗАПРОСА НА СОЗДАНИЕ СЕССИИ
    echo $CreateSession;
    $xmlSession = new SimpleXMLElement($CreateSession);//ПОЛУЧЕНИЕ XML ИЗ СТРОКИ
    echo $xmlSession;
    $SessionId = (string) $xmlSession->children('soap', true)->children('esf', true)->sessionid;//ИЗВЛЕЧЕНИЕ НАЧИНКИ
    echo $SessionId;
} catch(SoapFault $e){
	echo "Error: ".$e->getMessage().PHP_EOL;
}

$Upload_request_xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:esf="esf">
   <soapenv:Header/>
   <soapenv:Body>
      <esf:syncInvoiceRequest>
         <sessionId>7f4b9f6d76c045cb9b87279f837999d8-123456789021-</sessionId>
         <invoiceUploadInfoList>
            <invoiceUploadInfo>
               <invoiceBody><![CDATA[<v2:invoice xmlns:a="abstractInvoice.esf" xmlns:v2="v2.esf">
                    <date>04.10.2018</date>
                    <invoiceType>ORDINARY_INVOICE</invoiceType>
                    <num>11111</num>
                    <operatorFullname>test</operatorFullname>
                    <turnoverDate>22.08.2018</turnoverDate>
                    <customers>
                        <customer>
                            <address>010000, Казахстан, г. Астана, ул. Мира 4</address>
                            <countryCode>KZ</countryCode>
                            <name>ИП Кайрат</name>
                            <tin>123456789011</tin>
                        </customer>
                    </customers>
                    <deliveryDocDate>19.07.2018</deliveryDocDate>
                    <deliveryDocNum>555</deliveryDocNum>
                    <deliveryTerm>
                        <contractDate>05.05.2018</contractDate>
                        <contractNum>517</contractNum>
                        <hasContract>true</hasContract>
                        <term>нал.расчет</term>
                        <transportTypeCode>30</transportTypeCode>
                        <warrant>112</warrant>
                        <warrantDate>23.08.2018</warrantDate>
                    </deliveryTerm>
                    <productSet>
                        <currencyCode>KZT</currencyCode>
                        <products>
                            <product>
                                <catalogTruId>1</catalogTruId>
                                <description>Малина 25 мл № 14 Раствор питьевой</description>
                                <ndsAmount>468</ndsAmount>
                                <ndsRate>12</ndsRate>
                                <priceWithTax>4368</priceWithTax>
                                <priceWithoutTax>3900</priceWithoutTax>
                                <productDeclaration>55301/280717/0044564</productDeclaration>
                                <productNumberInDeclaration>6</productNumberInDeclaration>
                                <quantity>1</quantity>
                                <tnvedName>БИОЛОГИЧЕСКИ АКТИВНАЯ ДОБАВКА К ПИЩЕ: ПРОФЛЕКС / PROFLEX, РАСТВОР ПИТЬЕВОЙ ВО ФЛАКОНАХ ПО 25 МЛ, ПО 14 ФЛАКОНОВ В ПАЧКЕ КАРТОННОЙ</tnvedName>
                                <truOriginCode>5</truOriginCode>
                                <turnoverSize>3900</turnoverSize>
                                <unitCode>3004900002</unitCode>
                                <unitNomenclature>796</unitNomenclature>
                                <unitPrice>3900</unitPrice>
                            </product>
                            <product>
                                <catalogTruId>1</catalogTruId>
                                <description>Капли для мозгов</description>
                                <ndsAmount>0</ndsAmount>
                                <priceWithTax>60000</priceWithTax>
                                <priceWithoutTax>60000</priceWithoutTax>
                                <quantity>10</quantity>
                                <tnvedName>Урсосан 0,25 № 50 капс</tnvedName>
                                <truOriginCode>5</truOriginCode>
                                <turnoverSize>60000</turnoverSize>
                                <unitCode>3004900002</unitCode>
                                <unitNomenclature>796</unitNomenclature>
                                <unitPrice>6000</unitPrice>
                            </product>
                            <product>
                                <catalogTruId>1</catalogTruId>
                                <description>Нистатин 500 тыс ед № 20 табл (БЗМ)</description>
                                <ndsAmount>129</ndsAmount>
                                <ndsRate>12</ndsRate>
                                <priceWithTax>1204</priceWithTax>
                                <priceWithoutTax>1075</priceWithoutTax>
                                <productDeclaration>600716082017N01405</productDeclaration>
                                <productNumberInDeclaration>6</productNumberInDeclaration>
                                <quantity>5</quantity>
                                <tnvedName>Нистатин, таблетки, покрытые оболочкой  500000 ЕД. По 10 таблеток в контурной ячейковой упаковке. По 2 контурные ячейковые упаковки в пачке из картона. (Нистатин)</tnvedName>
                                <truOriginCode>5</truOriginCode>
                                <turnoverSize>1075</turnoverSize>
                                <unitCode>3004900002</unitCode>
                                <unitNomenclature>796</unitNomenclature>
                                <unitPrice>215</unitPrice>
                            </product>
                        </products>
                        <totalExciseAmount>0</totalExciseAmount>
                        <totalNdsAmount>597</totalNdsAmount>
                        <totalPriceWithTax>65572</totalPriceWithTax>
                        <totalPriceWithoutTax>64975</totalPriceWithoutTax>
                        <totalTurnoverSize>64975</totalTurnoverSize>
                    </productSet>
                    <sellers>
                        <seller>
                            <address>010001, Казахстан, г.Алматы, ул.Мира 77</address>
                            <certificateNum>1399478</certificateNum>
                            <certificateSeries>13788</certificateSeries>
                            <name>ТОО "Асем-2"</name>
                            <tin>123456789021</tin>
                        </seller>
                    </sellers>
                </v2:invoice>]]></invoiceBody>
               <version>InvoiceV2</version>
               <signature>CsMUOJZekKK92ccmttd4VhIfpeKNxEguWmqiIsQ/wtUdyW6TWBZPEDvw5eMydbjX36pfrl6g+OKFUS28yBPBkQ==</signature>
               <signatureType>COMPANY</signatureType>
            </invoiceUploadInfo>
         </invoiceUploadInfoList>
        <x509Certificate>MIIEvTCCBGegAwIBAgIUEXox1k2Y6lSYZ0OikkTCkHfgPVIwDQYJKoMOAwoBAQECBQAwgc8xCzAJBgNVBAYTAktaMRUwEwYDVQQHDAzQkNCh0KLQkNCd0JAxFTATBgNVBAgMDNCQ0KHQotCQ0J3QkDFMMEoGA1UECgxD0KDQnNCaIMKr0JzQldCc0JvQldCa0JXQotCi0IbQmiDQotCV0KXQndCY0JrQkNCb0KvSmiDSmtCr0JfQnNCV0KLCuzFEMEIGA1UEAww70rDQm9Ci0KLQq9KaINCa0KPTmNCb0JDQndCU0KvQoNCj0KjQqyDQntCg0KLQkNCb0KvSmiAoR09TVCkwHhcNMTcxMjEyMDkzODAxWhcNMTgxMjEyMDkzODAxWjCB+zEeMBwGA1UEAwwV0KLQldCh0KLQntCSINCi0JXQodCiMRUwEwYDVQQEDAzQotCV0KHQotCe0JIxGDAWBgNVBAUTD0lJTjEyMzQ1Njc4OTAxMTELMAkGA1UEBhMCS1oxFTATBgNVBAcMDNCQ0KHQotCQ0J3QkDEVMBMGA1UECAwM0JDQodCi0JDQndCQMRgwFgYDVQQKDA/QkNCeICLQotCV0KHQoiIxGDAWBgNVBAsMD0JJTjEyMzQ1Njc4OTAyMTEZMBcGA1UEKgwQ0KLQldCh0KLQntCS0JjQpzEeMBwGCSqGSIb3DQEJARYPSU5GT0BQS0kuR09WLktaMGwwJQYJKoMOAwoBAQEBMBgGCiqDDgMKAQEBAQEGCiqDDgMKAQMBAQADQwAEQCIVOvZqhMY0/42aRtgTyQozBVNZekCuA1f5hjNV6ODdHMdMV3nPo7flfwHwGr0nL/qz/EjXZCnK8jUoAVuh+tqjggHbMIIB1zAOBgNVHQ8BAf8EBAMCBsAwKAYDVR0lBCEwHwYIKwYBBQUHAwQGCCqDDgMDBAECBgkqgw4DAwQBAgEwDwYDVR0jBAgwBoAEVbW0rjAdBgNVHQ4EFgQUrqADV1IX45u0v24GcRpFJn/Wr+MwXgYDVR0gBFcwVTBTBgcqgw4DAwIBMEgwIQYIKwYBBQUHAgEWFWh0dHA6Ly9wa2kuZ292Lmt6L2NwczAjBggrBgEFBQcCAjAXDBVodHRwOi8vcGtpLmdvdi5rei9jcHMwUAYDVR0fBEkwRzBFoEOgQYYeaHR0cDovL2NybC5wa2kuZ292Lmt6L2dvc3QuY3Jshh9odHRwOi8vY3JsMS5wa2kuZ292Lmt6L2dvc3QuY3JsMFQGA1UdLgRNMEswSaBHoEWGIGh0dHA6Ly9jcmwucGtpLmdvdi5rei9kX2dvc3QuY3JshiFodHRwOi8vY3JsMS5wa2kuZ292Lmt6L2RfZ29zdC5jcmwwYwYIKwYBBQUHAQEEVzBVMC8GCCsGAQUFBzAChiNodHRwOi8vcGtpLmdvdi5rei9jZXJ0L3BraV9nb3N0LmNlcjAiBggrBgEFBQcwAYYWaHR0cDovL29jc3AucGtpLmdvdi5rejANBgkqgw4DCgEBAQIFAANBAM80gga4MzJU8peoWDiD4D4OAzF98Hh+gYlUk/Fn4y67PEgYL8lgW1+sq2aLOWrkdFivhvKE/PziVnRQfiJ3nkc=</x509Certificate>
      </esf:syncInvoiceRequest>
   </soapenv:Body>
</soapenv:Envelope>';
$Close_session_xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:esf="esf">
       <soapenv:Header>
          <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
             <wsse:UsernameToken wsu:Id="UsernameToken-664678CEF9FFC67AD214168421472821">
                <wsse:Username>123456789011</wsse:Username>
                <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">TestPass123</wsse:Password>
             </wsse:UsernameToken>
          </wsse:Security>
       </soapenv:Header>
       <soapenv:Body>
          <esf:closeSessionRequest>
             <sessionId>7f4b9f6d76c045cb9b87279f837999d8-123456789021-</sessionId>
          </esf:closeSessionRequest>
       </soapenv:Body>
    </soapenv:Envelope>';
try{
    //echo signXml($Upload_request_xml);
    $UploadResponse = $UploadInvoiceService->__doRequest($Upload_request_xml, 'https://212.154.167.194:9443/esf-web/ws/api1/UploadInvoiceService?wsdl', '', SOAP_1_2);

    echo $UploadResponse;
    
    $CloseSession= $SessionService->__doRequest($Close_session_xml, 'https://esf-test.kgd.gov.kz:9443/esf-web/ws/api1/SessionService', '', SOAP_1_2);
    echo $CloseSession;
} catch(Exception $e){
    trigger_error("Ошибка SOAP: (faultcode: {$e->faultcode}, faultstring: {$e->faultstring})", E_USER_ERROR);
}

?>
