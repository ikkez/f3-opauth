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

1.  You need to configure each strategy you want to use on your service. You can find instructions how to do that in each [**strategy folder**](https://github.com/ikkez/f3-opauth/tree/master/lib/opauth/Strategy)'s readme file. If you need more OAuth providers, check the the [list of strategies](https://github.com/uzyn/opauth/wiki/List-of-strategies).
2.  This plugin comes with its own [configuration file](https://github.com/ikkez/f3-opauth/blob/master/lib/opauth/opauth.ini).
It's used to provide all strategy configuration to Opauth, as well as some F3 routing settings. You should at least change the *security_salt* and your favorite strategy keys and secrets.
3.  Integrate the authorization into your existing login procedure.

### Basic Usage

```php
// in your index.php
$f3 = \Base::instance();
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

$f3->run();
```

The event handlers can also be callable string:

```php
$opauth->onSuccess('\Controller\User->socialLogin');
```

To login, call a strategy, i.e.`http://domain.com/auth/facebook`.


### Application flow

1.  Alright, you place a social login button near your regular login form, that send the user to the login strategory, i.e. `http://domain.com/auth/facebook`.
2.  When a user authenticated to a provider, the user is send back to a callback-route. The callback checks if the request and auth was valid. Then, when the authorization was successful, the `onSuccess` handler is called and provides you some data from the given strategy.
3.  You can use this data to extract a username, access-token, email or what ever suits best for that strategy. 
Each strategy provides different data, so depending on your application needs, you perhaps should consider to add some fallback mechanisms into your application (i.e. it can happen that you wont get an email from twitter or facebook). 
You can use this data to find a match in your user database. Depending on the result you can start your login or registration controller.
4.  On registration, you might want to save that unique-something from the strategy-auth-data to the user table (email, or `$f3->hash` on multiple fields like username,token,id) - this way you know who has activated a social auth provider and get the chance to load the user based on this.
5.  After registration and on login, you probably save an active user session in your controller to memorize your logged in user. 
6.  When the session is expired or the user need to login again from another device, send him to the auth strategy route again (`auth/facebook` - instant redirect after successful activation on most providers), and now you can find him in your user table with activated social auth and login him in directly.

This is of course just one idea of implementation. Some providers also return tokens for further communication with their 3rd party API (i.e. to fetch more profile data or renew tokens), but that's something this plugin is not going to handle. Have a look at the strategy docs for more information.

### Demo

*  [facebook](http://f3.ikkez.de/auth/facebook)
*  [twitter](http://f3.ikkez.de/auth/twitter)
*  [google](http://f3.ikkez.de/auth/google)
*  [github](http://f3.ikkez.de/auth/github)

License
-

GPLv3