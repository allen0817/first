<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 19:05
 */


	$param = array('USERNAME'=>'USERID','PASSWORD'=>'PASSW0RD');

	$url = 'http://172.16.253.181/session/create';

	$cookie = "HideIPv6WhenDisabled=0; session_id=none";

	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "USERNAME=USERID,PASSWORD=PASSW0RD");
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_COOKIE,$cookie);

	$str = curl_exec($ch);

	$c = explode(":",$str);

	$cc = "HideIPv6WhenDisabled=0; session_id=".$c[1];

	$u = 'http://172.16.253.181/wsman';
	curl_setopt($ch,CURLOPT_URL,$u);
	curl_setopt($ch,CURLOPT_POST,1);

	$param='<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:wsman="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd">
  <SOAP-ENV:Header>
    <wsa:To>http://172.16.253.181/wsman</wsa:To>
    <wsa:ReplyTo>
      <wsa:Address>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:Address>
    </wsa:ReplyTo>
    <wsman:ResourceURI>http://www.ibm.com/iBMC/sp/Monitors</wsman:ResourceURI>
    <wsa:Action>http://www.ibm.com/iBMC/sp/Monitors/GetSensorValues</wsa:Action>
    <wsa:MessageID>dt:1516960213903</wsa:MessageID>
  </SOAP-ENV:Header>
  <SOAP-ENV:Body>
    <GetSensorValues xmlns="http://www.ibm.com/iBMC/sp/Monitors" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></GetSensorValues>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
	curl_setopt($ch, CURLOPT_POSTFIELDS,$param);

	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch,CURLOPT_COOKIE,$cc);
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$d = curl_exec($ch);


	curl_close($ch);

	echo "<pre>";
	print_r($d);