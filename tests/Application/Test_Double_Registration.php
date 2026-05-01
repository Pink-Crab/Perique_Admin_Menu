<?php

declare(strict_types=1);

/**
 * Regression test for issue #58 — page double-registration when the same Page
 * appears in both registration_classes() AND inside an Abstract_Group's
 * primary_page / $pages.
 *
 * Without the fix the page registers twice (once via Page_Middleware's
 * register_single_page / register_subpage, once via Page_Dispatcher::register_group),
 * causing the render callback to fire twice in a single response.
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
 */

namespace PinkCrab\Perique_Admin_Menu\Tests\Application;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Output;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Perique\Services\View\PHP_Engine;
use Gin0115\WPUnit_Helpers\WP\Menu_Page_Inspector;
use PinkCrab\Perique_Admin_Menu\Hooks;
use PinkCrab\Perique_Admin_Menu\Module\Admin_Menu;
use PinkCrab\Perique_Admin_Menu\Registry\Group_Page_Registry;
use PinkCrab\Perique_Admin_Menu\Tests\Integration\Helper_Factory;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Double_Registration extends WP_UnitTestCase {

	use Helper_Factory;

	public function setUp(): void {
		parent::setup();
		$this->unset_app_instance();
	}

	/**
	 * Boot a test app with the Admin_Menu module and the given registration classes.
	 *
	 * @param array<class-string> $registration_classes
	 * @return void
	 */
	private function boot_app_with( array $registration_classes ): void {
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
			->module( Admin_Menu::class )
			->boot();

		$app->registration_classes( $registration_classes );

		$admin_user = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'dashboard' );
		do_action( 'init' );
		do_action( 'admin_menu' );
	}

	/**
	 * Counts how many times the slug appears as a top-level menu entry in $menu.
	 */
	private function count_menu_slug_entries( string $slug ): int {
		global $menu;
		if ( ! is_array( $menu ) ) {
			return 0;
		}
		$count = 0;
		foreach ( $menu as $entry ) {
			if ( isset( $entry[2] ) && $entry[2] === $slug ) {
				++$count;
			}
		}
		return $count;
	}

	/**
	 * Counts how many times the slug appears as a child entry under any parent in $submenu.
	 */
	private function count_submenu_slug_entries( string $slug ): int {
		global $submenu;
		if ( ! is_array( $submenu ) ) {
			return 0;
		}
		$count = 0;
		foreach ( $submenu as $children ) {
			foreach ( $children as $entry ) {
				if ( isset( $entry[2] ) && $entry[2] === $slug ) {
					++$count;
				}
			}
		}
		return $count;
	}

	/**
	 * @testdox [APPLICATION] When a page is in both registration_classes AND a Group's primary_page, it is registered exactly once (issue #58).
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_primary_page_in_group_and_registration_classes_registers_once(): void {
		$this->boot_app_with(
			array(
				Valid_Group::class,
				Valid_Primary_Page::class,
			)
		);

		$slug = Valid_Primary_Page::PAGE_SLUG;

		// The primary should appear exactly once as a top-level menu entry…
		$this->assertSame( 1, $this->count_menu_slug_entries( $slug ), 'Primary page slug appeared multiple times in $menu — double registration.' );

		// …and exactly once as a submenu entry under itself (the WP convention for top-level pages).
		$this->assertSame( 1, $this->count_submenu_slug_entries( $slug ), 'Primary page slug appeared multiple times in $submenu — double registration.' );
	}

	/**
	 * @testdox [APPLICATION] When a sub-page is in both registration_classes AND a Group's $pages array, it is registered exactly once (issue #58).
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_sub_page_in_group_and_registration_classes_registers_once(): void {
		$this->boot_app_with(
			array(
				Valid_Group::class,
				Valid_Page::class,
			)
		);

		// The sub-page should appear exactly once across the whole $submenu structure.
		$this->assertSame(
			1,
			$this->count_submenu_slug_entries( Valid_Page::PAGE_SLUG ),
			'Sub-page slug appeared multiple times in $submenu — double registration.'
		);
	}

	/**
	 * @testdox [APPLICATION] When the entire group (primary + sub) is duplicated in registration_classes, every page is still registered exactly once (issue #58).
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_full_group_duplicated_in_registration_classes_registers_each_once(): void {
		$this->boot_app_with(
			array(
				Valid_Group::class,
				Valid_Primary_Page::class,
				Valid_Page::class,
			)
		);

		$primary_slug = Valid_Primary_Page::PAGE_SLUG;
		$sub_slug     = Valid_Page::PAGE_SLUG;

		$this->assertSame( 1, $this->count_menu_slug_entries( $primary_slug ) );
		$this->assertSame( 1, $this->count_submenu_slug_entries( $primary_slug ) );
		$this->assertSame( 1, $this->count_submenu_slug_entries( $sub_slug ) );
	}

	/**
	 * @testdox [APPLICATION] A page registered ONLY through registration_classes (not in any group) still registers normally — backward compatibility.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_page_only_in_registration_classes_still_registers(): void {
		$this->boot_app_with(
			array(
				Valid_Primary_Page::class,
			)
		);

		$inspector   = Menu_Page_Inspector::initialise( true );
		$single_page = $inspector->find_parent( Valid_Primary_Page::PAGE_SLUG );

		$this->assertNotNull( $single_page );
		$this->assertSame( Valid_Primary_Page::PAGE_SLUG, $single_page->menu_slug );
	}

	/**
	 * @testdox [APPLICATION] A Group whose pages don't appear in registration_classes still registers every page — backward compatibility.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_group_only_no_overlap_still_registers_every_page(): void {
		$this->boot_app_with(
			array(
				Valid_Group::class,
			)
		);

		$this->assertSame( 1, $this->count_menu_slug_entries( Valid_Primary_Page::PAGE_SLUG ) );
		$this->assertSame( 1, $this->count_submenu_slug_entries( Valid_Page::PAGE_SLUG ) );
	}

	/**
	 * @testdox [APPLICATION] When the duplicated primary page is rendered, its render callback fires exactly once (the actual user-visible symptom of issue #58).
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_render_callback_fires_only_once_when_page_duplicated(): void {
		$this->boot_app_with(
			array(
				Valid_Group::class,
				Valid_Primary_Page::class,
			)
		);

		$output = Output::buffer(
			function () {
				$page_hook = get_plugin_page_hookname( Valid_Primary_Page::PAGE_SLUG, '' );
				do_action( 'load-' . $page_hook );
				do_action( $page_hook, function ( $e ) {} );
			}
		);

		// Without the fix the view template renders twice and we'd see the marker substring twice.
		$marker     = 'Valid Primary Page Data';
		$occurrence = substr_count( $output, $marker );
		$this->assertSame( 1, $occurrence, "Expected '{$marker}' to appear exactly once but it appeared {$occurrence} times — render callback fired more than once." );
	}

	/**
	 * @testdox [APPLICATION] Hooks::GROUPS_PROCESSED fires once during boot with the populated Group_Page_Registry — downstream modules subscribed in pre_register receive every Group-declared page class.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_groups_processed_hook_publishes_populated_registry(): void {
		$received = array(
			'count'    => 0,
			'registry' => null,
		);

		add_action(
			Hooks::GROUPS_PROCESSED,
			function ( $registry ) use ( &$received ) {
				$received['count']++;
				$received['registry'] = $registry;
			}
		);

		$this->boot_app_with(
			array(
				Valid_Group::class,
			)
		);

		$this->assertSame( 1, $received['count'], 'GROUPS_PROCESSED must fire exactly once during boot.' );
		$this->assertInstanceOf( Group_Page_Registry::class, $received['registry'] );
		$this->assertTrue(
			$received['registry']->has( Valid_Primary_Page::class ),
			'Registry passed to GROUPS_PROCESSED must contain the group primary page.'
		);
		$this->assertTrue(
			$received['registry']->has( Valid_Page::class ),
			'Registry passed to GROUPS_PROCESSED must contain every Group-declared sub-page.'
		);
	}
}
