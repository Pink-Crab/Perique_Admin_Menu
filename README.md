![logo](./docs/Perique%20Admin%20MenuCard.jpg "PinkCrab Perique Hook Subscriber")

# Perique Admin Menu

A module for the Perique Plugin Framework, for rendering and processing Admin Menu Pages with WordPress

[![Latest Stable Version](https://poser.pugx.org/pinkcrab/perique-admin-menu/v)](https://packagist.org/packages/pinkcrab/perique-admin-menu)
[![Total Downloads](https://poser.pugx.org/pinkcrab/perique-admin-menu/downloads)](https://packagist.org/packages/pinkcrab/perique-admin-menu) 
[![License](https://poser.pugx.org/pinkcrab/perique-admin-menu/license)](https://packagist.org/packages/pinkcrab/perique-admin-menu)
[![PHP Version Require](https://poser.pugx.org/pinkcrab/perique-admin-menu/require/php)](https://packagist.org/packages/pinkcrab/perique-admin-menu)
![GitHub contributors](https://img.shields.io/github/contributors/Pink-Crab/Perique_Admin_Menu?label=Contributors)
![GitHub issues](https://img.shields.io/github/issues-raw/Pink-Crab/Perique_Admin_Menu)

[![W6.2 [PHP7.4-8.2] Tests](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_2.yaml/badge.svg)](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_2.yaml)
[![WP6.3 [PHP7.4-8.2] Tests](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_3.yaml/badge.svg)](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_3.yaml)
[![WP6.4 [PHP7.4-8.3] Tests](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_4.yaml/badge.svg)](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_4.yaml)
[![WP6.5 [PHP7.4-8.3] Tests](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_5.yaml/badge.svg)](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_5.yaml)
[![WP6.6 [PHP7.4-8.3] Tests](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_6.yaml/badge.svg)](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_6.yaml)

[![codecov](https://codecov.io/gh/Pink-Crab/Perique_Admin_Menu/branch/master/graph/badge.svg)](https://codecov.io/gh/Pink-Crab/Perique_Admin_Menu)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pink-Crab/Perique_Admin_Menu/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pink-Crab/Perique_Admin_Menu/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/e2a31a8cb4df21afcad3/maintainability)](https://codeclimate.com/github/Pink-Crab/Perique_Admin_Menu/maintainability)


## Why?
WordPress admin pages can be added with a simple function, but this can easily lead into messy structural code with includes for templates and the inevitable mixing of logic and presentation in templates.

The Admin Menu module gives full access to Perique DI Container, for more separation of logic and presentation, with the added advantage of all your services being easy to test and reason with.
 
**Requires [Perique](https://github.com/Pink-Crab/Perique-Framework)** - for more details please visit our docs. https://perique.info

# Setup

Include the module using composer (via CLI)
```bash
$ composer require pinkcrab/perique-admin-menu
```
Once this has been included, we can add the module to Perique and its underlying Middleware will be added to the registration process.

```php
$app = ( new PinkCrab\Perique\Application\App_Factory() )
  // Perique bootstrapping as normal.   
  ->module( Admin_Menu::class )
  ->boot();
```
Once the middleware has been included, we can use Page & Group models as part of the usual [Registration](https://perique.info/core/Registration/) process

# Usage

It is possible to register either a single page or a group of pages.

## Group

A page can be used to register a group of pages, that can be registered as a top level menu item, or as a sub menu item of another page.

```php
class My_Group extends Abstract_Group{
  // Required  
  protected string $group_title = 'My Page Group';
  protected string $primary_page = 'Acme\My_Plugin\Page\Primary_Page';
  protected array $pages = array(
    'Acme\My_Plugin\Page\Secondary_Page',
    'Acme\My_Plugin\Page\Tertiary_Page',
  );
  
  // Optional
  protected string $capability = 'edit_posts';    // Defaults to manage_options
  protected string $icon = 'dashicons-chart-pie'; // Defaults to dashicons-admin-generic
  protected int $position = 24;                   // Defaults to 65
}
```

It is possible to enqueue scripts and styles, explicitly for this group using the [enqueue method](./docs/group.md#public-function-enqueue-abstract_group-group-page-page--void). This would see those scripts and styles only loaded on the pages within the group.

Please see the [group docs](./docs/group.md) for more details.

## Page

A page is a single menu item, that can be registered as a top level menu item, or as a sub menu item of another page.

```php
class My_Page extends Menu_Page{

  // Required  
  protected string $page_slug = 'acme_pages';
  protected string $page_title = 'Acme Pages';
  protected string $menu_title = 'Acme Pages';
  
  // Optional
  protected ?string $parent_slug = null;       // If null, will be a top level menu item.
  protected string $capability = 'edit_post';  // Default capability for page.
  protected ?int $position = 12;

  // View to render
  protected string $view_template = 'my-page.php';
  protected array $view_data = array('key' => 'value');
}
```

It is possible to enqueue scripts and styles, explicitly for this page using the [enqueue method](./docs/page.md#public-function-enqueue-page-page--void).

You can also trigger a callback on page load, using the [load method](./docs/page.md#public-function-load-page-page--void).

For more details on the Page model, please see the [Page docs](./docs/page.md)

---

### Page Form Handling Example

```php 
class Settings_Page extends Menu_page{
  // Page definitions.
  protected string $page_slug = 'acme_pages';
  protected string $page_title = 'Acme Pages';
  protected string $menu_title = 'Acme Pages';
  protected string $view_template = 'my-page.php';

  // Custom form handler service.
  private Form_Handler $form_handler;

  // Injected settings service and the form handler.
  public __construct( Settings $settings, Form_Handler $form_handler ){
    $this->form_handler = $form_handler;
    $this->view_data = $settings->as_array();
  }

  // On page load, check if form has been submitted, and if so, handle it.
  public function load( Page $page ): void{
    if( $this->form_handler->is_submitted() ){
      $new_settings = $this->form_handler->handle();
      $this->view_data = $new_settings;
    }
  }
}
```
In the above example the page would be use the settings from the `Settings` service, and if the form has been submitted, it would use the `Form_Handler` service to handle the form and update the view data.

# Example

There is a basic example of how to use this Module as part of a plugin, please see [Example Plugin](https://github.com/gin0115/Perique-Menu-Page-Example)

# License

## MIT License

http://www.opensource.org/licenses/mit-license.html 

# Contributions

If you would like to contribute to this or any other Perique module, please feel free to submit a PR with your changes. 

All code must be supplied with matching tests and must pass PHPUNIT, PHPStan and PHPCS checks and not see a large drop in coverage.

See composer.json for details on the test and linting commands. `composer all` is the most important.

# Change Log 
* 2.1.0 - Added support for Perique 2.1.x and updated some dev dependencies.
* 2.0.0 - Migrated to Perique 2.0.0
* 1.0.1 - Separated out the docs and added `page_hook()` method to page models and sets the hook after registration.
* 1.0.0 - Finalised API for Perique 1.4.*
* 0.2.1 - Allows pages that extend Menu_Page to be registered as both parent of child pages.
* 0.2.0 - Re introduced the `register()` and `load()` methods for both Groups and Pages.
* 0.1.2 - Fixed hardcoded primary page slug in Page_Dispatcher, updated dev dependencies
* 0.1.1 - Bumped version for Collection
* 0.1.0 - Migrated from https://github.com/Pink-Crab/Module__Admin_Pages
