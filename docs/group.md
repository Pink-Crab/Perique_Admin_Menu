# Group

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
> @var class-string<Page>

An array of fully namespaced class names, which extend the Menu_Page object
```php
class My_Group extends Abstract_Group{
   protected array $pages = [
      'Acme\My_Plugin\Page\Primary_Page',
      \Acme\My_Plugin\Page\Child_Page::class
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

*Shared Group Methods are called before individual page methods are called. Applied to `enqueue()` and `load()`*
  
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

---

> ### public function load( Abstract_Group $group, Page $page ): void 
> @param Abstract_Group $group
> @param Page $page  

This allows for the handling for form submissions or other checks before any page which is added as part of the group.

```php
class My_Group extends Abstract_Group{
   public function load( Abstract_Group $group, Page $page ): void {
      // If data has expired in transient, refresh.
      $from_transient = get_transient('something');
      if(false === $from_transient){
         $data = do_something();
         update_transient('something', $data, 12 * HOURS_IN_SECONDS);
      }
   }
}
```