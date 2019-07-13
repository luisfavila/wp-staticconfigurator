# WordPress Static Configurator

WordPress Static Configurator allows you to configure WordPress installations directly from `wp-config.php`. This plugin gives you the possibility to override any wordpress option saved in the database.

## Usage

Define a `WP_OPTIONS` constant (array) to your `wp_config.php` and define the keys and values of the options you wish to override.
Example:

```PHP
define('WP_OPTIONS', array(
	'blogname' => 'My blog',
	'permalink_structure' => '/%postname%/'
));
```

## Disable field edition

This will add a `disabled='disabled'` value to the HTML fields on the admin dashboard.

If you wish to let your users know the overrided fields cannot be modified, enable the experimental field disabling feature:


### Disable all overrided fields
```PHP
define('WP_OPTIONS_DISABLE_FIELDS', true);
```

### Disable some overrided fields
```PHP
define('WP_OPTIONS_DISABLE_FIELDS', array('blogname', 'some-other-field'));
```

### Field exceptions
**Useful if disabling a specific field breaks any other field on the admin dashboard**
```PHP
define('WP_OPTIONS_ENABLE_FIELDS', array('blogname', 'some-other-exempt-field'));
```