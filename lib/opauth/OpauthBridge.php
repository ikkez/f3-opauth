<?php
/**
 *	Opauth Plugin for the PHP Fat-Free Framework
 *
 *	The contents of this file are subject to the terms of the GNU General
 *	Public License Version 3.0. You may not use this file except in
 *	compliance with the license. Any of the license terms and conditions
 *	can be waived if you get permission from the copyright holder.
 *
 *	Copyright (c) 2015 ~ ikkez
 *	Christian Knuth <ikkez0n3@gmail.com>
 *
 *	@version: 0.8.0
 *	@date: 20.08.2015
 *
 **/

class OpauthBridge extends \Prefab {

	protected $config;
	protected $successFunc;
	protected $abortFunc;

	function __construct($config) {
		/** @var \Base $f3 */
		$f3 = \Base::instance();
		if (!isset($config['path']))
			$config['path'] = $f3->BASE.'/'.$config['auth_route'].'/';
		if (!isset($config['callback_url']))
			$config['callback_url'] = $f3->SCHEME.'://'.$f3->HOST.$f3->BASE.'/'.
				$config['callback_route'];
		if (!isset($config['callback_transport']))
			$config['callback_transport'] = 'post';
		$this->config = $config;
	}

	/**
	 * init auth request
	 * @param Base $f3
	 * @param $params
	 */
	function auth(\Base $f3, $params) {
		new \Opauth($this->config);
	}

	/**
	 * auth service callback
	 * @param Base $f3
	 * @param $params
	 */
	function callback(\Base $f3, $params) {
		$Opauth = new \Opauth($this->config,false);
		switch ($Opauth->env['callback_transport']) {
			case 'session':
				$response = $f3->get('SESSION.opauth');
				$f3->clear('SESSION.opauth');
				break;
			case 'post':
				$response = unserialize(base64_decode($f3->get('POST.opauth')));
				break;
			case 'get':
				$response = unserialize(base64_decode($f3->get('GET.opauth')));
				break;
			default:
				$f3->error(400,'Unsupported callback_transport');
				break;
		}
		if (isset($response['error'])) {
			$f3->call($this->abortFunc,array($response));
			return;
		}
		$data = $response['auth'];
		// validate
		if (empty($data) || empty($response['timestamp']) || empty($response['signature'])
			|| empty($data['provider']) || empty($data['uid']))
			$f3->error(400,'Invalid auth response: Missing key auth response components');
		elseif (!$Opauth->validate(sha1(print_r($data, true)),
			$response['timestamp'], $response['signature'], $reason))
			$f3->error(400,'Invalid auth response: '.$reason);
		else
			// It's all good
			$f3->call($this->successFunc,array($data));
	}

	/**
	 * set login handler
	 * @param $func
	 */
	function onSuccess($func) {
		$this->successFunc = $func;
	}

	/**
	 * set abort handler
	 * @param $func
	 */
	function onAbort($func) {
		$this->abortFunc = $func;
	}
}