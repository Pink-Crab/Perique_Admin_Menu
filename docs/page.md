# Page

Pages can either be added as part of a Group or standalone (as Main or Sub pages). Like Groups these are constructed using the DI Container, to allow for the injection of Services.

To define a page, there are a few properties which must be defined and a few methods which can be used to enqueue scripts/style or trigger actions before the page is rendered.

> All pages must extend from `PinkCrab\Perique_Admin_Menu\Page\Menu_Page`

## Page Properties

These must all be declared as `protected` and can either be defined directly or via the constructor.

> When used in context with a group, various values can be omitted and these will be populated by the `Group`s values.

> ### protected string|null $parent_slug  
> @var string  
> @optional If registered as part of a group, the parent will be set based on the Groups parent page.  

This should not be entered for parent pages, all child pages will be set based on the groups primary page (will overwrite any value defined). 

If being created as a stand alone child pate, please enter the parent slug as per `add_submenu_page()`
```php
class My_Page extends Menu_Page {
   protected ?string $parent_slug = 'acme_page_parent';
}
```
---

> ### protected string $page_slug  
> @var string  
> @throws Page_Exception (code 201) If not defined and fails validation. 

This is the pages slug, will be used as the group slug if defined as the parent page.

```php
class My_Page extends Menu_Page {
   protected string $page_slug = 'acme_pages';
}
```
---

> ### protected string $menu_title  
> @var string  
> @throws Page_Exception (code 201) If not defined and fails validation. 

This is used as the pages, sub menu title. 
```php
class My_Page extends Menu_Page {
   protected string $menu_title = 'Parent Page';
}
```
---

> ### protected string $page_title  
> @var string  

This is used as the pages title, is only automatically displayed if using the WP Settings API.
```php
class My_Page extends Menu_Page {
   protected string $page_title = 'Acme Parent Page';
}
```
---

> ### protected int|null $position  
> @var int|null  

An optional page position, this is only used in context of the submenu. Use group for main menu placements.
```php
class My_Page extends Menu_Page {
   protected ?int $position = 12;
}
```
---

> ### protected string $capability  
> @var string  
> @optional will default to 'manage_options' if not defined.

This sets the min user capabilities required to access the page. If not defined will default to 'manage_options'
```php
class My_Page extends Menu_Page {
   protected string $capability = 'edit_post';
}
```
---

> **There are 2 ways to render the view**

#### Render View with Template File
You can render your template using [Perique's View service](https://perique.info/core/App/view). To use `View`, you will need to define a template path and any optional data to pass to the view.


> ### protected string $view_template  
> @var string  
> @throws Page_Exception (code 200) If no view template defined (and not using the `render_view()` method)

This is the path the template. It is based as the view root path, which is defined during setup. By default the view base path is (`wp-content/plugins/acme_plugin/views`)

#### Render View with a function [Read More](#public-function-render_view-callable)

> As per View functionality the `.php` file extension is optional
```php
class My_Page extends Menu_Page {
   protected string $view_template = 'pages/primary-page';
   // transcribed to wp-content/plugins/acme_plugin/views/pages/primary-page.php
}
```
> In templates `$this` relates to the `Renderable` instance and gives access to `render()` and other View methods.

---

> ### protected string $view_data  
> @var string  
> @optional Will pass an empty array without access

Passes an array of data which can be accessed in the view template.
```php
class My_Page extends Menu_Page {
   protected array $view_data = [
      'key1' => 'value1'
      'key2' => 'value2'
   ];
}
```
> These would then be accessible in the template as `echo $key1;` would be `value1`  
> You can create the view_data property in the constructor to use injected services [See Example Project for more](https://github.com/gin0115/Perique-Menu-Page-Example/blob/main/src/Page/Parent_Page.php#L79)

---


## Page Methods

These must all be declared as `public` and are optional.

> ### public function enqueue( Page $page ): void 
> @param Page $page  

This allows for the enqueueing of Scripts and Styles using `wp_enqueue_script()`, `wp_enqueue_style()` or [PinkCrab - Enqueue](https://github.com/Pink-Crab/Enqueue). To whatever page this is declared as.

```php
class My_Page extends Menu_Page {
   public function enqueue( Page $page ): void {
      wp_enqueue_script( 
         'page_script', 
         'https://www.acme.com/wp-content/plugins/acme/assets/page-script.js', 
         array( 'jquery' ),
         '1.2.4',
         true
      );
   }
}
```
> Please note this is fired after the [Groups enqueue()](#public-function-enqueue-abstract_group-group-page-page--void) method

---

> ### public function load( Page $page ): void 
> @param Page $page  

This allows for the handling for form submissions or other checks before the page is loaded.

```php
class My_Page extends Menu_Page {
   public function load( Page $page ): void {
      // If data has expired in transient, refresh.
      $from_transient = get_transient('something');
      if(false === $from_transient){
         $data = do_something();
         update_transient('something', $data, 12 * HOURS_IN_SECONDS);
      }
   }
}
```

> Please note this is fired after the [Groups load()](#public-function-load-abstract_group-group-page-page--void) method

It is possible to update the `view_data` property in this method, which will be passed to the view template. This allows for form handling to carried out in this method and have any updated values represented in the view.

---

> ### public function render_view(): callable 
> @return callable  

This can be used to override the use of View and the definition of template files.
```php
class My_Page extends Menu_Page {
   public function render_view(): callable {
      return function(){
         print 'Something to the page';
      };
   }
}
```
---

> ### public function page_hook(): ?string  
> @return string|null  

This will return the hook name of the page, if it has been registered. This is useful for adding hooks to the page, such as `add_action( 'load-' . $page->page_hook(), 'my_callback' );`. If this is called before the page is registered, it will return `null`.
