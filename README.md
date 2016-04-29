# yii2-session-timeout-warning
Users are warned about expiring session.

This is simple implementation of session timeout warning.
Plugins does not make any ajax calls, otherwise it would not work on many projects, because many components (e.g. RBAC) are using user identity and in this case session is renewed all the time.

This plugin writes and reads cookies. It supports multiple tabs.
Plugin will not work if user is logged out from server side, or sessions are cleared from database.

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
todo
