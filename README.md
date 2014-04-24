Laravel OAuth
=============

[![Build Status](https://travis-ci.org/jenssegers/Laravel-OAuth.svg)](https://travis-ci.org/jenssegers/Laravel-OAuth)

A Laravel 4 OAuth library, using [PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib). This library uses your Laravel configured session to store tokens.

*Original code by [hannesvdvreken](https://github.com/hannesvdvreken/laravel-oauth) (not maintained).*

Installation
------------

Add the package to your `composer.json` and run `composer update`.

    {
        "require": {
            "jenssegers/oauth": "*"
        }
    }

Add the service provider in `app/config/app.php`:

    'Jenssegers\OAuth\OAuthServiceProvider',

Add the OAuth alias to `app/config/app.php`:

    'OAuth'            => 'Jenssegers\OAuth\Facades\OAuth',

Configuration
-------------

Create an `oauth.php` config file containing your OAuth consumers:

    'consumers' => array(

        'Facebook' => array(
            'client_id'     => '',
            'client_secret' => '',
            'scope'         => array(),
        )

    )

Usage
-----

Once you have added your credentials, you can create PHPoAuthLib service objects like this:

    $oauth = OAuth::consumer('Facebook');

To override the default redirect url, or scope use:

    $oauth = OAuth::consumer('Facebook', URL::to('url'), array('email', 'publish_actions'));

For more information check out [PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib).

Example
-------

Example usage for the Facebook API.

    $oauth = OAuth::consumer('Facebook');

    // Response from Facebook
    if ($code = Input::get('code'))
    {
        $token = $oauth->requestAccessToken($code);

        $result = json_decode($oauth->request('/me'), true);

        echo 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
    }

    // Redirect to login
    else
    {
        return Redirect::to($oauth->getAuthorizationUri());
    }

For more examples check out [PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib).
