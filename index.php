<?php
include_once 'mime.php';
include_once 'proxy.php';

// if ($_SERVER['REQUEST_URI'] === '/') {
// 	Mime::header('html');
// 	include 'home.html';
// 	die;
// }
Proxy::$allowed_users = ['bobanum', 'else-angels', 'js-cstj', 'tim-cstj', 'vue-cstj', 'web1cstj', 'web2cstj', 'web3cstj', 'web4cstj',];
Proxy::call();
