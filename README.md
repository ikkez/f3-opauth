# F3 Opauth Plugin
This is a plugin for easy integration of [Opauth](http://opauth.org/) for PHP Fat-Free Framework.

### Installation

* A: use composer `composer require ikkez/f3-opauth:dev-master`
* or B: put the opauth folder somewhere into your libs or autoloaded directory, and adjust the autoload path:

```php
// adjust the autoloader
$f3->set('AUTOLOAD','lib/opauth/');
```

### Configuration

This plugin comes with its own [configuration file](https://github.com/ikkez/f3-opauth/blob/master/lib/opauth/opauth.ini).
You should at least change the *security_salt* and the given strategy keys and secrets. You'll find instructions how to do that in each strategy folder's readme file. If you need more OAuth providers, check the the [list of strategies](https://github.com/uzyn/opauth/wiki/List-of-strategies).

### Usage

```php
// in your index.php
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

The event handlers can also be callable string:

```php
$opauth->onSuccess('\Controller\User->socialLogin');
```

To login, call a strategy, i.e.`http://domain.com/auth/facebook`.

### Demo

*  [facebook](http://f3.ikkez.de/auth/facebook)
*  [twitter](http://f3.ikkez.de/auth/twitter)
*  [google](http://f3.ikkez.de/auth/google)
*  [github](http://f3.ikkez.de/auth/github)
