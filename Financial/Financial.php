<?php

session_start();


function buxfer($path, $params=array()) {
	$_SESSION['buxfer_token'] = isset($_SESSION['buxfer_token'])? $_SESSION['buxfer_token']: null;

	if (! $_SESSION['buxfer_token']) {
		$user = 'guitarsolo92@gmail.com';
		$pass = '689120ra';
		$url = "https://www.buxfer.com/api/login?userid={$user}&password={$pass}";
		$content = file_get_contents($url);
		$content = json_decode($content, true);
		$_SESSION['buxfer_token'] = $content['response']['token'];
	}

	$parse = parse_url($path);
	$parse['query'] = isset($parse['query'])? $parse['query']: '';
	parse_str($parse['query'], $parse['query']);
	$params = array_merge($parse['query'], $params);
	$params['token'] = $_SESSION['buxfer_token'];
	$path = ltrim((isset($parse['path'])? $parse['path']: ''), '/');
	$url = "https://www.buxfer.com/api/{$path}?" . http_build_query($params);
	$content = file_get_contents($url);
	return json_decode($content, true);
}



// $transactions = buxfer('/transactions');
// echo '<pre>'. print_r($transactions, true) .'</pre>';