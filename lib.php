<?php

/*
 * ������ ��������������� ������� ��� ��������
 */

/**
 *
 * @param SimpleXMLElement $requestXml
 * @param string $api_secret
 * @return string
 */
function make_sign($requestXml, $api_secret) {
	return md5(getStringForSign($requestXml) . $api_secret);
}

/**
 *
 * @param SimpleXMLElement $XmlElement
 * @return string
 */
function getStringForSign($XmlElement) {
	$result = '';
	$list = (array) $XmlElement;
	ksort($list);
	foreach ($list as $node) {
		if (is_array($node)) {
			$result .= implode('', array_map('getStringForSign', $node));
		} else if (is_object($node)) {
			$result .= getStringForSign($node);
		} else {
			$result .= (string) $node;
		}
	}
	return $result;
}

/**
 * ��������� �������� ������� �� ������
 * @param string $api_server
 * @param string $api_key
 * @param SimpleXMLElement $requestXml
 * @return array  success - ������� �� ���������� ���������� http-�������,
 * ��� ��������� ����� ������� � response ��� ������ ������ SimpleXMLElement
 */
function apiRequest($api_server, $api_key, $requestXml) {
	$result = array('success' => true, 'response' => null);
	$ch = curl_init($api_server);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // �� ������ ����� �� stdout
	curl_setopt($ch, CURLOPT_HEADER, 1);   // �������� ���������
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);   // ������������ ������������ ����� ��������
	// �� ��������� SSL ����������
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	// �� ��������� Host SSL �����������
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $api_key . ':X');

	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXml->asXML());

	$response = curl_exec($ch);
	$error = curl_error($ch);

	if ($error != "") {
		$result['success'] = false;
		$result['response'] = $error;
		return $result;
	}

	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$responseBody = substr($response, $header_size);

	try {
		$result['response'] = new SimpleXMLElement($responseBody);
	} catch (Exception $e) {
		// ������ ���������� XML
		$result['success'] = false;
		$result['response'] = 'broken xml';
	}
	return $result;
}

/**
 * ���������, �������� �� �����-������
 * @param SimpleXMLElement $responseXml
 */
function parseAPIError($responseXml) {
	if ($responseXml['status'] == 'error') {
		// ������������ ���� ������ ����� ���������� http://goo.gl/GWa1c
		echo 'code :' . $responseXml->code;
		echo ' message: ' . $responseXml->message;
		exit();
	}
}

?>