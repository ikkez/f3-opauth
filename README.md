# F3 Opauth Plugin
Opauth Plugin for PHP Fat-Free Framework

### Install

* A: use composer
* or B: put the opauth folder somewhere into your libs or autoloaded directories, and adjust the autoload path:

```php
// adjust the autoloader
$f3->set('AUTOLOAD','lib/opauth/');
```

### Usage

```php
// load opauth config (allow token resolve)
$f3->config('lib/opauth/opauth.ini', TRUE);

// init with config
$opauth = OpauthBridge::instance($f3->opauth);

// define login handler
$opauth->onSuccess(function($data){
	header('Content-Type: text');
	echo 'User successfully authenticated.'."\n";
	print_r($data['info']);
});

// define error handler
$opauth->onAbort(function($data){
	header('Content-Type: text');
	echo 'Auth request was canceled.'."\n";
	print_r($data);
});
```

To login, call `http://domain.com/auth/facebook`.

### Demo

[facebook](http://f3.ikkez.de/auth/facebook)
[twitter](http://f3.ikkez.de/auth/twitter)
[google](http://f3.ikkez.de/auth/google)
[github](http://f3.ikkez.de/auth/github)