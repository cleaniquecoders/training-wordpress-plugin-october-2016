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

## Talking to Database

### Calling $wpdb

You need to declare `$wpdb` as global in order to start making queries to database

```php
global $wpdb;
```

More details at [$wpdb](https://codex.wordpress.org/Class_Reference/wpdb#Using_the_.24wpdb_Object).

### Error Handler

#### Display Error

```php
 <?php $wpdb->show_errors(); ?> 
 ```

#### Hide Error

 ```php
 <?php $wpdb->hide_errors(); ?> 
```

#### Print Error

```php
<?php $wpdb->print_error(); ?> 
```

### Queries

#### Select Generic Results

```php
$wpdb->get_results( 'query', output_type );
```

`output_type` can be `OBJECT`, `OBJECT_K`, `ARRAY_A`, `ARRAY_N`.

#### Running General Query

```php
$wpdb->query('query');
```

#### Protect Queries Against SQL Injection Attacks

```php
$metakey    = "Harriet's Adages";
$metavalue  = "WordPress' database interface is like Sunday Morning: Easy.";

$wpdb->query( $wpdb->prepare( 
    "
        INSERT INTO $wpdb->postmeta
        ( post_id, meta_key, meta_value )
        VALUES ( %d, %s, %s )
    ", 
        10, 
    $metakey, 
    $metavalue 
) );
```

#### Select Variable

```php
$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
```

#### Select Row

```php
$mylink = $wpdb->get_row( "SELECT * FROM $wpdb->links WHERE link_id = 10" );
```

#### Insert Data

```php
$wpdb->insert( 
    'table', 
    array( 
        'column1' => 'value1', 
        'column2' => 123 
    ), 
    array( 
        '%s', 
        '%d' 
    ) 
);
```

Use `$wpdb->insert_id` to get the incremental ID value after insertion.

#### Update Data

```php
$wpdb->update( 
    'table', 
    array( 
        'column1' => 'value1',  // string
        'column2' => 'value2'   // integer (number) 
    ), 
    array( 'ID' => 1 ), 
    array( 
        '%s',   // value1
        '%d'    // value2
    ), 
    array( '%d' ) 
);
```

#### Delete Data

```php
$wpdb->delete( 'table', array( 'ID' => 1 ), array( '%d' ) );
```

## Setting Up Custom Table

Below are the sample how to create a custom table in WordPress using `$wpdb`

```php
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'my_analysis';

$sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    views smallint(5) NOT NULL,
    clicks smallint(5) NOT NULL,
    UNIQUE KEY id (id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
```

## Setting Up Plugin Dependencies

### Installing FullCalendar

Go to [FullCalendar](https://fullcalendar.io/download/) download page and download the latest FullCalendar and extract it to `wp-content/plugins/plugin-name/vendor` directory. You should have the following directory structure by now:

```
vendor
	fullcalendar-3.0.1
```