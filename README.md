# Perique Admin Menu

A module for the Perique Plugin Framework, for rendering and processing Admin Menu Pages with WordPress

[![Latest Stable Version](http://poser.pugx.org/pinkcrab/perique-admin-menu/v)](https://packagist.org/packages/pinkcrab/perique-admin-menu)
[![Total Downloads](http://poser.pugx.org/pinkcrab/perique-admin-menu/downloads)](https://packagist.org/packages/pinkcrab/perique-admin-menu) 
[![License](http://poser.pugx.org/pinkcrab/perique-admin-menu/license)](https://packagist.org/packages/pinkcrab/perique-admin-menu)
[![PHP Version Require](http://poser.pugx.org/pinkcrab/perique-admin-menu/require/php)](https://packagist.org/packages/pinkcrab/perique-admin-menu)
![GitHub contributors](https://img.shields.io/github/contributors/Pink-Crab/Perique_Admin_Menu?label=Contributors)
![GitHub issues](https://img.shields.io/github/issues-raw/Pink-Crab/Perique_Admin_Menu)
[![WP5.9 [PHP7.2-8.1] Tests](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_5_9.yaml/badge.svg)](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_5_9.yaml)
[![WP6.0 [PHP7.2-8.1] Tests](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_0.yaml/badge.svg)](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_0.yaml)
[![WP6.1 [PHP7.2-8.1] Tests](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_1.yaml/badge.svg)](https://github.com/Pink-Crab/Perique_Admin_Menu/actions/workflows/WP_6_1.yaml)
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
Once the module is included, we need to include the `Registration Middleware`. As this has its own dependencies, this will need to be added using `construct_registration_middleware()` from the `App_Factory` instance.

```php
$app = ( new PinkCrab\Perique\Application\App_Factory() )
  // Perique bootstrapping as normal.   
  ->construct_registration_middleware( Page_Middleware::class )
  ->boot();
```
Once the middleware has been included, we can use Page & Group models as part of the usual [Registration](https://perique.info/core/Registration/) process



---



# Example

There is a basic example of how to use this Module as part of a plugin, please see https://github.com/gin0115/Perique-Menu-Page-Example for more details.

# License

## MIT License

http://www.opensource.org/licenses/mit-license.html 

# Contributions

If you would like to contribute to this or any other Perique module, please feel free to submit a PR with your changes. 

All code must be supplied with matching tests and must pass PHPUNIT, PHPStan and PHPCS checks and not see a large drop in coverage.

See composer.json for details on the test and linting commands. `composer all` is the most important.

# Change Log 
* 0.2.1 - Allows pages that extend Menu_Page to be registered as both parent of child pages.
* 0.2.0 - Re introduced the `register()` and `load()` methods for both Groups and Pages.
* 0.1.2 - Fixed hardcoded primary page slug in Page_Dispatcher, updated dev dependencies
* 0.1.1 - Bumped version for Collection
* 0.1.0 - Migrated from https://github.com/Pink-Crab/Module__Admin_Pages
