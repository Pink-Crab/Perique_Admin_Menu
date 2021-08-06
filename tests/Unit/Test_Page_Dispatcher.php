<?php

declare(strict_types=1);

/**
 * Unit tests for the Group Registrar.
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

namespace PinkCrab\Perique_Admin_Menu\Tests\Unit;

use stdClass;
use Exception;
use TypeError;
use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique_Admin_Menu\Page\Menu_Page;
use PinkCrab\Perique_Admin_Menu\Registrar\Registrar;
use PinkCrab\Perique_Admin_Menu\Exception\Page_Exception;
use PinkCrab\Perique_Admin_Menu\Registrar\Page_Dispatcher;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Page_Dispatcher extends WP_UnitTestCase {

	public function get_mock_dispatcher(): Page_Dispatcher
	{
		$di            = $this->createMock( DI_Container::class );
		$view           = $this->createMock( \PinkCrab\Perique\Services\View\View::class );
		$registrar = $this->createMock( Registrar::class );

		return new Page_Dispatcher( $di, $view, $registrar );
	}
	
	/** @testdox When creating an instance of the group registrar, all used interal  */
	public function test_populates_internal_state(): void {
		$di            = $this->createMock( DI_Container::class );
		$view           = $this->createMock( \PinkCrab\Perique\Services\View\View::class );
		$registrar = $this->createMock( Registrar::class );

		$dispatcher = new Page_Dispatcher( $di, $view, $registrar );

		$this->assertSame( $di, Objects::get_property( $dispatcher, 'di_container' ) );
		$this->assertSame( $view, Objects::get_property( $dispatcher, 'view' ) );
		$this->assertSame( $registrar, Objects::get_property( $dispatcher, 'registrar' ) );
	}

	public function test_admin_exception(): void
	{
		$dispatcher = $this->get_mock_dispatcher();
		
		$group = new Valid_Group();
		$exception = new Exception('TEST EXCEPTION');

		$this->expectOutputRegex('/PinkCrab\\\Perique_Admin_Menu\\\Tests\\\Fixtures\\\Valid_Group\\\Valid_Group/');
		
		$dispatcher->admin_exception_notice($group, $exception);
		\do_action('admin_notices');
	}


	public function test_get_primary_page(): void
	{
		$di            = $this->createMock( DI_Container::class );
		$di->method( 'create' )->will(
			$this->returnCallback(
				function( $page ) {
					return new Valid_Primary_Page();
				}
			)
		);
		$view           = $this->createMock( \PinkCrab\Perique\Services\View\View::class );
		$registrar = $this->createMock( Registrar::class );
		$dispatcher = new Page_Dispatcher( $di, $view, $registrar );
		$group = new Valid_Group();
		$page = Objects::invoke_method($dispatcher, 'get_primary_page', [$group]);

		$this->assertInstanceOf(Page::class, $page);
		$this->assertInstanceOf(View::class, Objects::get_property($page, 'view'));
	}

	public function test_exception_thrown_if_invalid_primary_page_type()
	{
		$di            = $this->createMock( DI_Container::class );
		$di->method( 'create' )->will(
			$this->returnCallback(
				function( $page ) {
					return new stdClass();
				}
			)
		);
		$view           = $this->createMock( \PinkCrab\Perique\Services\View\View::class );
		$registrar = $this->createMock( Registrar::class );
		$dispatcher = new Page_Dispatcher( $di, $view, $registrar );
		$group = new Valid_Group();
		
		$this->expectException(Page_Exception::class);
		$page = Objects::invoke_method($dispatcher, 'get_primary_page', [$group]);
	}

	/** @testdoc The dispatcher should be able to create instance of all (non primary) pages defined within a group. */
	public function test_get_pages(): void
	{
		$di            = $this->createMock( DI_Container::class );
		$di->method( 'create' )->will($this->onConsecutiveCalls(
			new Valid_Page()
		));
		$view           = $this->createMock( \PinkCrab\Perique\Services\View\View::class );
		$registrar = $this->createMock( Registrar::class );
		$dispatcher = new Page_Dispatcher( $di, $view, $registrar );
		
		$group = new Valid_Group();
		$pages = Objects::invoke_method($dispatcher, 'get_pages', [$group]);
		
		$this->assertCount(1, $pages);
		$this->assertEquals(Valid_Page::class, get_class(end($pages)));
	}

	/** @testdox The dispatcher should throw an error if a group has a page defined which does not implement the Page interface. */
	public function test_exception_thrown_if_group_contains_non_Page_page()
	{
		$di            = $this->createMock( DI_Container::class );
		$di->method( 'create' )->will(
			$this->returnCallback(
				function( $page ) {
					return new stdClass();
				}
			)
		);
		$view           = $this->createMock( \PinkCrab\Perique\Services\View\View::class );
		$registrar = $this->createMock( Registrar::class );
		$dispatcher = new Page_Dispatcher( $di, $view, $registrar );
		
		$group = new Valid_Group();
		
		$this->expectException(Page_Exception::class);
		$this->expectExceptionCode(202);
		
		$page = Objects::invoke_method($dispatcher, 'get_pages', [$group]);
	}
}
