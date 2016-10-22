# WordPress Plugin Development

## Setting Up Plugin Structure

Create a folder in `wp-content/plugins` directory named `plugin-name`

```
plugin-name
	app
		views
		controllers
		models
		bootstrap.php
	assets
		css
		fonts
		img
		js
	inc
	vendor
	plugin-name.php
```

## Setting Up Plugin Details

Open up `plugin-name.php` and add the following details

```php
/*
Plugin Name: Plugin Name
Version: 1.0.0
Description: A simple plugin 
Author: Nasrul Hazim Bin Mohamad
Author URI: http://blog.nasrulhazim.com
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Set Plugin Directory Path
define('RB_DIR', plugin_dir_path(__FILE__));

// Set Plugin URI
define('RB_URI', plugin_dir_url(__FILE__));

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require RB_DIR . 'app/bootstrap.php';
```

## Activation and Deactivation

Add the following in `plugin-name.php` in order to handle plugin activation and deactivation.

```php
register_activation_hook(__FILE__, 'plugin_prefix_activate');

register_deactivation_hook(__FILE__, 'plugin_prefix_deactivate');

function plugin_prefix_activate()
{
    $version = $plugin_data['Version'];

    // check WordPress version, if less than version we need, don't activate
    if (version_compare(get_bloginfo('version'), '4.0', '<')) {
        wp_die("You must use at least WordPress 4.1 to use this plugin!");
    }

    // If no record on plugin version, do create one
    if (get_option('plugin_name_version') === false) {
        add_option('plugin_name_version', $version);
    } else if (get_option('plugin_name_version') < $version) {
        // else, do update the slider version
        update_option('plugin_name_version', $version);
    }
}

function plugin_prefix_deactivate()
{

}
```

## Uninstall

For plugin uninstallation, create a file named `uninstall.php` and paste in the following codes. You may add your logic later on, when deleting plugin from WordPress what need to be remove form WordPress.

```php
<?php

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// delete room booking version
delete_option('plugin_name_version');
```

## Setting Up Plugin Bootstrap File

Create a `bootstrap.php` in `plugin-name/app` directory and add the following content. `PluginBootstrap` class name can be rename based on plugin's name. It's a good practice for each WordPress plugin to have their own bootstrap class name.

```php
<?php

/**
 * PluginBootstrap bootstrap class - load scripts, styles, setup menus, shortcodes, define pages
 *
 * @package default
 * @author
 **/
class PluginBootstrap
{
    public function run()
    {
        // include css
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);

        // include javascripts
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

        // add plugin main menus
        add_action('admin_menu', [$this, 'menus']);

        // add shortcodes
        add_action('init', [$this, 'define_shortcodes']);
    }

    public function define_shortcodes()
    {
        // short code
        // https://developer.wordpress.org/reference/functions/add_shortcode/
    }

    public function menus()
    {
        //create new top-level menu
        // https://developer.wordpress.org/reference/functions/add_menu_page/
        
        //create sub menu
        // https://developer.wordpress.org/reference/functions/add_submenu_page/
    }

    public function enqueue_scripts()
    {
        // wp_enqueue_script()
    }

    public function enqueue_styles()
    {
        // wp_enqueue_style()
    }
} // END class
```

### Run the PluginBootstrap

In order to initiliaze the plugin, you need to create a new object of the `PluginBootstrap` and call the `run` method in the class. So, open up your `plugin-name.php` and append the following to the script.

```php
function plugin_prefix_run()
{
    $bootsrap = new PluginBootstrap();
    $bootsrap->run();
}

plugin_prefix_run();
```

## Setting Up Plugin Dependencies

### Installing FullCalendar

Go to [FullCalendar](https://fullcalendar.io/download/) download page and download the latest FullCalendar and extract it to `wp-content/plugins/plugin-name/vendor` directory. You should have the following directory structure by now:

```
vendor
	fullcalendar-3.0.1
```