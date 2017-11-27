#  Telegram Package

Allow your users to connect Telegram with your app and receive notifications;

## Prerequisites

* PHP 7.0+
* [**furqansiddiqui**/http-client](https://github.com/furqansiddiqui/http-client) >= 0.2.1

## Installation

`composer require furqansiddiqui/telegram`

# Integrating Telegram

#### Get `Telegram` instance by passing your telegram bot API key to constructor

```php
<?php

$telegram   = new \Telegram\Telegram("YOUR-API-KEY");
```

#### Setting up a web hook

```php
<?php
/** @var $telegram \Telegram\Telegram */
$telegram->webHooks()->setWebHook("https://www.domain.tld/telegram");
```

#### Custom Handlers

Create a handler class that extends `AbstractHandler` with each method for every command you wish to respond to.

* Your custom handler class must have "start" and "help" methods.
* Ideally, You should associate "chat_id" param with users in your DB.
* On start command, you should generate a temp. authentication code and send it to telegram chat asking them to enter this code at your website after proper authentication as a user. 
* Once user enters this code at your website, you can safely associate chat_id param associated with this temp. code with your user.
* How you generate/store and secure this code is entirely up to you.


#### Listening

* You should use secret tokens in your web hook URL as well as cross-check data received from Telegram web hook server for security. This has to be done entirely at your own end. Calling listen/handle method assumes you have already successfully validated this request.

```php
<?php
/** @var $telegram \Telegram\Telegram */
$telegram->setHandler(new MyCustomHandler($telegram));
$telegram->listen($_REQUEST);
```

Refer to class `BasicHandler` for better understanding on writing your own custom handler.
