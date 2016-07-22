# yii2-session-timeout-warning
Users are warned about expiring session.

This is simple implementation of session timeout warning.
Plugin does not make any ajax calls, otherwise it would not work with many projects. Components like RBAC are using user identity all time and auth session is renewed every request.

This plugin writes and reads cookies. 

It supports multiple tabs.
Plugin will not work if user is logged out from server side, or sessions are cleared from database.

If user session is expired, page will be reloaded.

### Install

Either run

```
$ php composer.phar require mg-code/yii2-session-timeout-warning "@dev"
```

or add

```
"mg-code/yii2-session-timeout-warning": "@dev"
```

to the ```require``` section of your `composer.json` file.

### Usage

1) Register bootstrap class in application config.
```php
[
  'bootstrap' => [
      [
          'class' => \mgcode\sessionWarning\components\SessionWarningBootstrap::className(),
          'initMessages' => true,
      ]
  ],
];
```
`initMesscompoages` property initializes translations. 
Currently only English and Latvian languages are supported.
Please contribute and add your language.

2) Add controller to controllerMap in application config.
```php
[
  'controllerMap' => [
    'session-warning' => [
      'class' => 'mgcode\sessionWarning\controllers\SessionWarningController',
    ],
  ],
];
```
Note: If you are using RBAC, you should allow all visitors to access this controller.

3) Insert widget in layout view.
```php
<?= \mgcode\sessionWarning\widgets\SessionWarningWidget::widget([
  //... Properties  ...
]); ?>
```
Properties:
* `logoutUrl`  - if is set, logout button will be shown before Continue button. Default: null
* `extendUrl`  - url where ajax request is sent, when continue button is clicked. Default: ['/session-warning/extend']
* `warnBefore` - time in seconds before user is warned about expiring session. Default: 300 (5min)
