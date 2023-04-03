<?php

declare(strict_types=1);

/**
 * Integration test with a valid Menu Page group.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique_Admin_Menu
 *
 * @docs https://www.advancedcustomfields.com/resources/acf_add_options_page/
 */

namespace PinkCrab\Perique_Admin_Menu\Tests\Application;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Output;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Perique\Services\View\PHP_Engine;
use Gin0115\WPUnit_Helpers\WP\Menu_Page_Inspector;
use PinkCrab\Perique_Admin_Menu\Module\Admin_Menu;
use PinkCrab\Perique_Admin_Menu\Registrar\Page_Load_Action;
use PinkCrab\Perique_Admin_Menu\Registrar\Page_Enqueue_Action;
use PinkCrab\Perique_Admin_Menu\Tests\Integration\Helper_Factory;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Valid_Menu_Group extends WP_UnitTestCase {

	use Helper_Factory;

	public function setUp(): void {
		parent::setup();
		$this->unset_app_instance();
	}

	/**
	 * @testdox [APPLICATION] When a valid group is registered, all of the defined group values should be used to regiser the group and all the pages should also be registered, with the group title differing form that of the primary page.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	*/
	public function test_valid_group(): void {

		$app = ( new App_Factory() )->with_wp_dice( true )
			->di_rules(
				array(
					'*' => array(
						'substitutions' => array(
							Renderable::class => new PHP_Engine( '/' ),
						),
					),
				)
			)
			->module(Admin_Menu::class)
			->boot();

		// $app->registration_middleware( $this->middleware_provider( $app ) );
		$app->registration_classes( array( Valid_Group::class ) );

		// Log in as admin and run the apps initialisation (on init hook)
		$admin_user = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'dashboard' );
		do_action( 'init' );

		$defined_group   = new Valid_Group();
		$defined_primary = new Valid_Primary_Page();

		// Build Page Inspector
		$inspector = Menu_Page_Inspector::initialise( true );

		// Group Tests.
		$group = $inspector->find_group( Valid_Primary_Page::PAGE_SLUG );

		$this->assertNotNull( $group );
		$this->assertEquals( $defined_group::GROUP_TITLE, $group->menu_title );
		$this->assertEquals( $defined_group::CAPABILITY, $group->permission );
		$this->assertEquals( $defined_group::ICON, $group->icon );
		$this->assertEquals( (int) $defined_group::POSITION, (int) $group->position );
		$this->assertEquals( $defined_primary::PAGE_SLUG, $group->menu_slug );
		$this->assertEquals( 'toplevel_page_' . $defined_primary::PAGE_SLUG, $group->hook_name );

		// Primary Page Tests.
		$primary = $inspector->find_child( $defined_primary::PAGE_SLUG );

		$this->assertNotNull( $primary );
		$this->assertEquals( $defined_primary->menu_title(), $primary->menu_title );
		$this->assertEquals( $defined_primary->page_title(), $primary->page_title );
		$this->assertEquals( $defined_primary->capability(), $primary->permission );
		$this->assertEquals( $defined_primary->slug(), $primary->menu_slug );
		$this->assertEquals( $defined_primary->slug(), $primary->parent_slug ); // Primary so slugs should match
		$output_priamry = Output::buffer(
			function() use ( $defined_primary ) {
				$page_hook = get_plugin_page_hookname( $defined_primary->slug(), '' );

				do_action( 'load-' . $page_hook );
				do_action( $page_hook, function( $e ) {} );
			}
		);
		$this->assertEquals( 'Valid Primary Page Data--Loaded Primary Page', $output_priamry );
	}

	/**
	 * @testdox [APPLICATION] When a valid group is registered, all the page and group hooks should be registered for enqueue and on load.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	*/
	public function test_valid_group_page_enqueue_hooks(): void {

		$defined_group   = new Valid_Group();
		$defined_primary = new Valid_Primary_Page();
		$defined_child   = new Valid_Page();

		$app = ( new App_Factory() )->with_wp_dice( true )
			->di_rules(
				array(
					'*' => array(
						'substitutions' => array(
							Renderable::class => new PHP_Engine( '/' ),
						),
					),
				)
			)
			->module(Admin_Menu::class)
			->boot();

		// $app->registration_middleware( $this->middleware_provider( $app ) );
		$app->registration_classes( array( Valid_Group::class ) );

		// Log in as admin and run the apps initialisation (on init hook)
		$admin_user = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'toplevel_page_' . $defined_primary::PAGE_SLUG );
		do_action( 'init' );
		// Register the page hooks
		do_action( 'admin_menu' );

		// Look for all admin enqueue hooks with our enqueue class as the function.
		$hooks = array_values(
			array_filter(
				$GLOBALS['wp_filter']['admin_enqueue_scripts']->callbacks[10],
				function( array $hook_def ): bool {
					return $hook_def['function'] instanceof Page_Enqueue_Action;
				}
			)
		);

		// Should have 2 hooks registered
		$this->assertCount( 2, $hooks );

		// Check that the group enqueue hooks are called for parent page.
		$hooks[0]['function']->__invoke( 'toplevel_page_' . $defined_primary::PAGE_SLUG );
		$hooks[1]['function']->__invoke( 'toplevel_page_' . $defined_primary::PAGE_SLUG );

		// The log should only have 1 entry (parent), the child page wont be called as incorrect page_hook.
		$this->assertCount( 1, $defined_group::$enqueue_log );
		$this->assertInstanceOf( Valid_Group::class, $defined_group::$enqueue_log[0][0] );
		$this->assertInstanceOf( Valid_Primary_Page::class, $defined_group::$enqueue_log[0][1] );

		// Check the page hook was fired too.
		$this->assertCount( 1, $defined_primary::$enqueue_log );
		$this->assertInstanceOf( Valid_Primary_Page::class, $defined_primary::$enqueue_log[0] );

		// Reset Logs
		$defined_group::$enqueue_log   = array();
		$defined_primary::$enqueue_log = array();
		$defined_child::$enqueue_log   = array();

		// Check that the group enqueue hooks are called for child page.
		$hooks[0]['function']->__invoke( 'valid-page-group_page_valid_page' );
		$hooks[1]['function']->__invoke( 'valid-page-group_page_valid_page' );

		// The log should only have 1 entry (parent), the parent page wont be called as incorrect page_hook.
		$this->assertCount( 1, $defined_group::$enqueue_log );
		$this->assertInstanceOf( Valid_Page::class, $defined_group::$enqueue_log[0][1] );
		$this->assertInstanceOf( Valid_Group::class, $defined_group::$enqueue_log[0][0] );

		// Check that the hook was fired for child page.
		$this->assertCount( 1, $defined_child::$enqueue_log );
		$this->assertInstanceOf( Valid_Page::class, $defined_child::$enqueue_log[0] );
	}

	/**
	 * @testdox [APPLICATION] When a valid group is registered, all the page and group hooks should be registered for on_load and on load.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	*/
	public function test_valid_group_page_on_load_hooks(): void {

		$defined_group   = new Valid_Group();
		$defined_primary = new Valid_Primary_Page();
		$defined_child   = new Valid_Page();

		$app = ( new App_Factory() )->with_wp_dice( true )
			->di_rules(
				array(
					'*' => array(
						'substitutions' => array(
							Renderable::class => new PHP_Engine( '/' ),
						),
					),
				)
			)
			->module(Admin_Menu::class)
			->boot();

		// $app->registration_middleware( $this->middleware_provider( $app ) );
		$app->registration_classes( array( Valid_Group::class ) );

		// Log in as admin and run the apps initialisation (on init hook)
		$admin_user = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'toplevel_page_' . $defined_primary::PAGE_SLUG );
		do_action( 'init' );
		// Register the page hooks
		do_action( 'admin_menu' );

		// Trigger the primary page pre load hook.
		do_action( 'load-toplevel_page_valid_primary_page' );
		// The log should only have 1 entry (parent).
		$this->assertCount( 1, $defined_group::$load_log );
		$this->assertInstanceOf( Valid_Group::class, $defined_group::$load_log[0][0] );
		$this->assertInstanceOf( Valid_Primary_Page::class, $defined_group::$load_log[0][1] );

		// Check the PARENT page callback was fired too.
		$this->assertCount( 1, $defined_primary::$load_log );
		$this->assertInstanceOf( Valid_Primary_Page::class, $defined_primary::$load_log[0] );

		// Reset Logs
		$defined_group::$load_log   = array();
		$defined_primary::$load_log = array();
		$defined_child::$load_log   = array();

		// Trigger the child page pre load hook.
		do_action( 'load-valid-page-group_page_valid_page' );

		// The log should only have 1 entry (child).
		$this->assertCount( 1, $defined_group::$load_log );
		$this->assertInstanceOf( Valid_Page::class, $defined_group::$load_log[0][1] );
		$this->assertInstanceOf( Valid_Group::class, $defined_group::$load_log[0][0] );

		// Check that CHILD callback was fired too.
		$this->assertCount( 1, $defined_child::$load_log );
		$this->assertInstanceOf( Valid_Page::class, $defined_child::$load_log[0] );
	}
}

