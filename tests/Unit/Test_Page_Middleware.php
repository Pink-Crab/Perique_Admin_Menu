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
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique_Admin_Menu\Module\Page_Middleware;
use PinkCrab\Perique_Admin_Menu\Registrar\Page_Dispatcher;
use PinkCrab\Perique_Admin_Menu\Registrar\Registrar;
use PinkCrab\Perique_Admin_Menu\Validator\Group_Validator;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Page_Middleware extends WP_UnitTestCase {

	/**
	 * Build a middleware backed by a real Page_Dispatcher (with mocked deps)
	 * so we can inspect the dispatcher's internal claim map after process().
	 */
	private function middleware_with_real_dispatcher(): array {
		$di        = $this->createMock( DI_Container::class );
		$view      = $this->createMock( \PinkCrab\Perique\Services\View\View::class );
		$registrar = $this->createMock( Registrar::class );

		$dispatcher = new Page_Dispatcher( $di, $view, $registrar );
		$middleware = new Page_Middleware( $dispatcher, new Group_Validator(), new Hook_Loader() );

		return array( $middleware, $dispatcher );
	}

	private function force_is_admin(): void {
		// is_admin() returns true when WP_ADMIN === true OR the screen is admin.
		set_current_screen( 'dashboard' );
	}

	/** @testdox When process() sees an Abstract_Group, every page class on the group (primary + $pages) is marked claimed on the dispatcher. */
	public function test_process_group_marks_primary_and_pages_as_claimed(): void {
		$this->force_is_admin();
		[ $middleware, $dispatcher ] = $this->middleware_with_real_dispatcher();

		$middleware->process( new Valid_Group() );

		$claimed = Objects::get_property( $dispatcher, 'group_claimed' );

		$this->assertIsArray( $claimed );
		$this->assertArrayHasKey( Valid_Primary_Page::class, $claimed );
		$this->assertArrayHasKey( Valid_Page::class, $claimed );
	}

	/** @testdox When process() sees a single Page, no claims are recorded — only Group processing produces claims. */
	public function test_process_single_page_does_not_add_claims(): void {
		$this->force_is_admin();
		[ $middleware, $dispatcher ] = $this->middleware_with_real_dispatcher();

		$middleware->process( new Valid_Primary_Page() );

		$claimed = Objects::get_property( $dispatcher, 'group_claimed' );
		$this->assertSame( array(), $claimed );
	}

	/** @testdox Claims are recorded synchronously during process(), before any deferred admin_menu callback fires — so order of class processing doesn't matter. */
	public function test_claims_are_recorded_synchronously_in_process(): void {
		$this->force_is_admin();
		[ $middleware, $dispatcher ] = $this->middleware_with_real_dispatcher();

		// Simulate a Page being processed BEFORE its owning Group.
		$middleware->process( new Valid_Page() );

		// At this point no claims yet — only Group processing claims.
		$this->assertSame( array(), Objects::get_property( $dispatcher, 'group_claimed' ) );

		// Group processed second; claims now populated.
		$middleware->process( new Valid_Group() );
		$claimed = Objects::get_property( $dispatcher, 'group_claimed' );

		$this->assertArrayHasKey( Valid_Primary_Page::class, $claimed );
		$this->assertArrayHasKey( Valid_Page::class, $claimed );
	}
}
