<?php
define('TENCENT_APP_ID', '');
define('TENCENT_ACCESSKEYID', '');
define('TENCENT_ACCESSKEYSECRET', '');
define('TENCENT_BUCKET', '');
define('TENCENT_HOST', '');



function 腾讯对象存储 ($method, $filename = '', $filepath = '',$query = '') {
	$accessKeyId        = TENCENT_ACCESSKEYID;
	$accessKeySecret    = TENCENT_ACCESSKEYSECRET;
	$bucket             = TENCENT_BUCKET . '-' . TENCENT_APP_ID;
	$domain             = TENCENT_HOST;

	if ($filename) {
		$url            =  "http://$bucket.$domain/$filename";
	} elseif ($query) {
		$url            =  "http://$bucket.$domain/$query";
	}

	$signTime           = time() . ';' . (time() + 3600);
	$options            = sha1(join("\n", array(
		strtolower($method),
		"/$filename",
		'',
		"host=$bucket.$domain",
		''
	)));
	$signOptions        = join("\n", array(
		'sha1',
		$signTime,
		$options,
		''
	));
	$signSecret         = hash_hmac(
		'sha1',
		$signTime,
		$accessKeySecret
	);
	$hash               = hash_hmac(
		'sha1',
		$signOptions,
		$signSecret
	);
	$auth               = str_replace('%3B', ';', http_build_query(array(
		'q-sign-algorithm' => 'sha1',
		'q-ak' => $accessKeyId,
		'q-sign-time' => $signTime,
		'q-key-time' => $signTime,
		'q-header-list' => 'host',
		'q-url-param-list' => '',
		'q-signature' => $hash
	)));
	$headers            = array(
		"Authorization: $auth"
	);

	if ($file) {
		return curl($url, $headers, $method, $filepath);
	} else {
		return curl($url, $headers, $method);
	}
}
function 腾讯对象存储上传 ($filename, $filepath) {
	return 腾讯对象存储('PUT', $filename, $filepath);
}
function 腾讯对象存储删除 ($filename) {
	return 腾讯对象存储('DELETE', $filename);
}
function 腾讯对象存储列表 ($query = '') {
	$xmllist = 腾讯对象存储('GET', '', '', $query);
	$objectlist = simplexml_load_string($xmllist, 'SimpleXMLElement', LIBXML_NOCDATA);
	return $objectlist;
}