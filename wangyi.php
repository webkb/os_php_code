<?php
define('WANGYI_ACCESSKEYID', '');
define('WANGYI_ACCESSKEYSECRET', '');
define('WANGYI_BUCKET', '');
define('WANGYI_HOST', '');



function 网易对象存储 ($method, $filename = '', $filepath = '', $query = '') {
	$accessKeyId        = WANGYI_ACCESSKEYID;
	$accessKeySecret    = WANGYI_ACCESSKEYSECRET;
	$bucket             = WANGYI_BUCKET;
	$domain             = WANGYI_HOST;

	if ($filename) {
		$url            = "http://$bucket.$domain/$filename";
	} elseif ($query) {
		$url            = "http://$bucket.$domain/$query";
	}

	$date               = gmdate('D, d M Y H:i:s \G\M\T');
	$options            = join("\n", array(
		strtoupper($method),
		'',
		'',
		$date,
		"/$bucket/$filename"
	));
	$hash               = hash_hmac(
		'sha256',
		$options,
		$accessKeySecret,
		true
	);
	$auth               = $accessKeyId . ':' . base64_encode($hash);
	$headers            = array(
		"Host: $bucket.$domain",
		"Date: $date",
		"Authorization: NOS $auth"
	);

	if ($file) {
		return curl($url, $headers, $method, $file);
	} else {
		return curl($url, $headers, $method);
	}
}
function 网易对象存储上传 ($filename, $filepath) {
	return 网易对象存储('PUT', $filename, $filepath);
}
function 网易对象存储删除 ($filename) {
	return 网易对象存储('DELETE', $filename);
}
function 网易对象存储列表 ($query = '') {
	$xmllist = 网易对象存储('GET', '', '', $query);
	$objectlist = simplexml_load_string($xmllist, 'SimpleXMLElement', LIBXML_NOCDATA);
	return $objectlist;
}