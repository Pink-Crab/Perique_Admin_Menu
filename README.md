# Perique Admin Menu

A module for the Perique Plugin Framework, for rendering and processing Admin Menu Pages with WordPress

![Packagist Version](https://img.shields.io/packagist/v/pinkcrab/perique-admin-menu?color=yellow&label=Latest%20Version)
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)
![GitHub contributors](https://img.shields.io/github/contributors/Pink-Crab/Perique_Admin_Menu?label=Contributors)
![GitHub issues](https://img.shields.io/github/issues-raw/Pink-Crab/Perique_Admin_Menu)
![GitHub branch checks state](https://img.shields.io/github/checks-status/Pink-Crab/Perique_Admin_Menu/master?label=Github%20CI)
[![codecov](https://codecov.io/gh/Pink-Crab/Perique_Admin_Menu/branch/master/graph/badge.svg)](https://codecov.io/gh/Pink-Crab/Perique_Admin_Menu)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pink-Crab/Perique_Admin_Menu/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pink-Crab/Perique_Admin_Menu/?branch=master)



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

# Groups

Admin_Menu allows for the defining of Groups, compared to the vanilla `add_menu_page()` provided by WordPress. Using groups allows for custom **Group Titles** separate of the main page. This allows for the dynamic shifting of pages based on any conditions (user role, subscriptions etc), to alter which pages are added or not and keeping a unified appearance.

To define a group, there are a few properties which must be defined and a few methods which can be used to share assets and functionality between all pages within the group.

> All groups must extend from [`PinkCrab\Perique_Admin_Menu\Group\Abstract_Group`](https://github.com/Pink-Crab/Perique_Admin_Menu/blob/feature/docs/src/Group/Abstract_Group.php).

## Group Properties

These must all be declared as `protected` and can either be defined directly or via the constructor.

> ### protected string $group_title  
> @var string  
> @throws Group_Exception (code 252) If not defined and fails validation.

Define the title of the Group.
```php
class My_Group extends Abstract_Group{
    protected string $group_title = 'My Page Group';
}
```
---

> ### protected string $capability  
> @var string  
> @default 'manage_options' if not defined

Define the min capabilities a user must have for the group to be displayed.
```php
class My_Group extends Abstract_Group{
    protected string $capability = 'edit_posts';
}
```
---

> ### protected string $icon  
> @var string  
> @default 'dashicons-admin-generic' if not defined

Define which dash icon or img url to use as the group icon.
```php
class My_Group extends Abstract_Group{
    protected string $icon = 'edit_posts';
}
```
---

> ### protected string $primary_page  
> @var string / class-string   
> @throws Group_Exception (code 252) If not defined and fails validation.

The fully namespaced class name for the primary page (this must also be included in pages)
```php
class My_Group extends Abstract_Group{
    protected string $primary_page = 'Acme\My_Plugin\Page\Primary_Page';
}
```
---

> ### protected array $pages  
> @var string[] | class-string[]   

An array of fully namespaced class names, which extend the Menu_Page object
```php
use Acme\My_Plugin\Page\Child_Page;

class My_Group extends Abstract_Group{
    protected array $pages = [
        'Acme\My_Plugin\Page\Primary_Page',
        Child_Page::class
    ];
}
```
> You can use the `::class` helper constant if you wish.
---

> ### protected int $position  
> @var int  
> @default 65 if not defined

Define the menu position [See for more details](https://developer.wordpress.org/reference/functions/add_menu_page/#menu-structure)
```php
class My_Group extends Abstract_Group{
    protected int $position = 24;
}
```
---

## Group Methods

These must all be declared as `public` and are optional.

> Shared Group Methods are called before individual page methods are called. Applied to `enqueue()` and `load()`  
  
> ### public function enqueue( Abstract_Group $group, Page $page ): void 
> @param Abstract_Group $group
> @param Page $page  

This allows for the enqueueing of Scripts and Styles using `wp_enqueue_script()`, `wp_enqueue_style()` or [PinkCrab - Enqueue](https://github.com/Pink-Crab/Enqueue). Any scripts of styles defined here, will be applied to every page registered in the group.

```php
class My_Group extends Abstract_Group{
    public function enqueue( Abstract_Group $group, Page $page ): void {
        wp_enqueue_script( 
            'acme_script', 
            'https://www.acme.com/wp-content/plugins/acme/assets/script.js', 
            array( 'jquery' ),
            '1.2.4',
            true
        );
    }
}
```
> You have access to the Group and Page as its being enqueued, so some conditional logic can be handled at runtime, if needed.
# License

## MIT License

http://www.opensource.org/licenses/mit-license.html 

# Change Log 
* 0.2.0 - Re introduced the `register()` and `load()` methods for both Groups and Pages.
* 0.1.2 - Fixed hardcoded primary page slug in Page_Dispatcher, updated dev dependencies
* 0.1.1 - Bumped version for Collection
* 0.1.0 - Migrated from https://github.com/Pink-Crab/Module__Admin_Pages
