<?php

declare(strict_types=1);

/**
 * Unit tests for the Page Middleware.
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

namespace PinkCrab\Perique_Admin_Menu\Tests\Unit;

use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique_Admin_Menu\Hooks;
use PinkCrab\Perique_Admin_Menu\Module\Page_Middleware;
use PinkCrab\Perique_Admin_Menu\Registrar\Page_Dispatcher;
use PinkCrab\Perique_Admin_Menu\Registrar\Registrar;
use PinkCrab\Perique_Admin_Menu\Registry\Group_Page_Registry;
use PinkCrab\Perique_Admin_Menu\Validator\Group_Validator;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Page_Middleware extends WP_UnitTestCase {

	/**
	 * Build a middleware backed by a real Page_Dispatcher whose DI container
	 * resolves Group_Page_Registry to a real instance we can inspect.
	 *
	 * @return array{0: Page_Middleware, 1: Page_Dispatcher, 2: Group_Page_Registry}
	 */
	private function middleware_with_real_dispatcher(): array {
		$registry = new Group_Page_Registry();
		$di       = $this->createMock( DI_Container::class );
		$di->method( 'create' )->willReturnCallback(
			function ( $class ) use ( $registry ) {
				if ( $class === Group_Page_Registry::class ) {
					return $registry;
				}
				return null;
			}
		);
		$view      = $this->createMock( \PinkCrab\Perique\Services\View\View::class );
		$registrar = $this->createMock( Registrar::class );

		$dispatcher = new Page_Dispatcher( $di, $view, $registrar );
		$middleware = new Page_Middleware( $dispatcher, new Group_Validator(), new Hook_Loader() );

		return array( $middleware, $dispatcher, $registry );
	}

	private function force_is_admin(): void {
		// is_admin() returns true when WP_ADMIN === true OR the screen is admin.
		set_current_screen( 'dashboard' );
	}

	/** @testdox When process() sees an Abstract_Group, every page class on the group (primary + $pages) is recorded on the registry against the group instance. */
	public function test_process_group_records_primary_and_pages(): void {
		$this->force_is_admin();
		[ $middleware, , $registry ] = $this->middleware_with_real_dispatcher();
		$group                       = new Valid_Group();

		$middleware->process( $group );

		$this->assertTrue( $registry->has( Valid_Primary_Page::class ) );
		$this->assertTrue( $registry->has( Valid_Page::class ) );
		$this->assertSame( $group, $registry->group_for( Valid_Primary_Page::class ) );
		$this->assertSame( $group, $registry->group_for( Valid_Page::class ) );
	}

	/** @testdox When process() sees a single Page (not a Group), the registry remains empty. */
	public function test_process_single_page_does_not_record(): void {
		$this->force_is_admin();
		[ $middleware, , $registry ] = $this->middleware_with_real_dispatcher();

		$middleware->process( new Valid_Primary_Page() );

		$this->assertSame( array(), $registry->all() );
	}

	/** @testdox Records are written synchronously during process(), so order of Page vs Group processing within a single registration pass doesn't matter for the eventual claim state. */
	public function test_records_are_written_synchronously_in_process(): void {
		$this->force_is_admin();
		[ $middleware, , $registry ] = $this->middleware_with_real_dispatcher();

		// Simulate a Page being processed BEFORE its owning Group.
		$middleware->process( new Valid_Page() );

		// At this point nothing is recorded — only Group processing records.
		$this->assertSame( array(), $registry->all() );

		// Group processed second; registry now populated.
		$middleware->process( new Valid_Group() );

		$this->assertTrue( $registry->has( Valid_Primary_Page::class ) );
		$this->assertTrue( $registry->has( Valid_Page::class ) );
	}

	/** @testdox tear_down() fires Hooks::GROUPS_PROCESSED with the populated Group_Page_Registry as the only argument. */
	public function test_tear_down_fires_groups_processed_with_registry(): void {
		$this->force_is_admin();
		[ $middleware, , $registry ] = $this->middleware_with_real_dispatcher();
		$middleware->process( new Valid_Group() );

		$received = null;
		add_action(
			Hooks::GROUPS_PROCESSED,
			function ( $r ) use ( &$received ) {
				$received = $r;
			}
		);

		$middleware->tear_down();

		$this->assertSame( $registry, $received );
		$this->assertTrue( $received->has( Valid_Primary_Page::class ) );
		$this->assertTrue( $received->has( Valid_Page::class ) );
	}

	/** @testdox tear_down() still calls Hook_Loader::register_hooks() so deferred admin_menu callbacks are wired into WP. */
	public function test_tear_down_registers_loaded_hooks(): void {
		$this->force_is_admin();
		[ $middleware ] = $this->middleware_with_real_dispatcher();
		$middleware->process( new Valid_Primary_Page() );

		// After tear_down() the deferred closure should be attached to admin_menu.
		$middleware->tear_down();

		$this->assertNotFalse( has_action( 'admin_menu' ) );
	}
}
