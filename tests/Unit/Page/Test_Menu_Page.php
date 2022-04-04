<?php

declare(strict_types=1);

/**
 * Test for the menu page.
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

namespace PinkCrab\Perique_Admin_Menu\Tests\Unit\Page;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique_Admin_Menu\Exception\Page_Exception;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Invalid_Group\Invalid_Primary_Page;

class Test_Menu_Page extends WP_UnitTestCase {

	/** @testdox It should be possible to set View to the page and have it set as a property. */
	public function test_can_set_view(): void {
		$page = new Valid_Primary_Page();
		$page->set_view( $this->createMock( View::class ) );
		$this->assertInstanceOf( View::class, Objects::get_property( $page, 'view' ) );
	}

	/** @testdox It should be possible to get the slug of a page, if its defined. */
	public function test_get_slug(): void {
		$page = new Valid_Primary_Page();
		$this->assertEquals( Valid_Primary_Page::PAGE_SLUG, $page->slug() );
	}

	/** @testdox If a page has no slug defined, it should throw an exception when trying to access it */
	public function test_throws_unset_slug(): void {
		$this->expectException( Page_Exception::class );
		$this->expectExceptionCode( 201 );
		$page = new Invalid_Primary_Page();
		$page->slug();
	}

	/** @testdox It should be possible to get the menu_title of a page, if its defined. */
	public function test_get_menu_title(): void {
		$page = new Valid_Primary_Page();
		$this->assertEquals( Valid_Primary_Page::MENU_TITLE, $page->menu_title() );
	}

	/** @testdox If a page has no menu_title defined, it should throw an exception when trying to access it */
	public function test_throws_unset_menu_title(): void {
		$this->expectException( Page_Exception::class );
		$this->expectExceptionCode( 201 );
		$page = new Invalid_Primary_Page();
		$page->menu_title();
	}

	/** @testdox It should be possible to get the page_title of a page, if its defined. */
	public function test_get_page_title(): void {
		$page = new Valid_Primary_Page();
		$this->assertEquals( Valid_Primary_Page::PAGE_TITLE, $page->page_title() );
	}

	/** @testdox If a page title is not set, it should return null if attempting to access */
	public function test_get_page_title_null_if_unset(): void {
		$page = new Invalid_Primary_Page();
		$this->assertNull( $page->page_title() );
	}

	/** @testdox It should be possible to get the position of a page, if its defined. */
	public function test_get_position(): void {
		$page = new Valid_Primary_Page();
		$this->assertEquals( Valid_Primary_Page::POSITION, $page->position() );
	}

	/** @testdox If a page title is not set, it should return null if attempting to access */
	public function test_get_position_null_if_unset(): void {
		$page = new Invalid_Primary_Page();
		$this->assertNull( $page->position() );
	}

	/** @testdox It should be possible to get the capabilities for accessing a page, if its defined. */
	public function test_get_capabilities(): void {
		$page = new Valid_Primary_Page();
		$this->assertEquals( 'manage_options', $page->capability() );
	}

	/** @testdox It should be possible to render the view of page */
	public function test_can_render_view(): void {
		$this->expectOutputString( 'test' );

		// Create a mocked version of View.
		$view = $this->createMock( View::class );
		$view->method( 'render' )->will(
			$this->returnCallback(
				function( $template, $data ) {
					print 'test';
				}
			)
		);

		$page = new Valid_Primary_Page();
		$page->set_view( $view );
		$page->render_view()(); // Returns callable, so curried for test.
	}

	/** @testdox Attempting to render the view of a page, without callign set_view() first, will result in an expection */
	public function test_throws_if_view_not_set_and_attempting_to_render(): void {
		$this->expectException( Page_Exception::class );
		$this->expectExceptionCode( 200 );
		$page = new Valid_Primary_Page();
		$page->render_view();
	}

	/** @testdox Attempting to render the view of a pag with no view_template defined, should throw and exception. */
	public function test_throws_no_view_template(): void {
		$this->expectException( Page_Exception::class );
		$this->expectExceptionCode( 201 );

		$page = new Invalid_Primary_Page();
		$page->set_view( $this->createMock( View::class ) );
		$page->render_view();
	}

	/** @testdox It should be possible to get the parent slug if defined to a menu page. */
	public function test_get_parent_slug(): void
	{
		$page = new Valid_Page();
		Objects::set_property($page, 'parent_slug', 'mock_parent');
		$this->assertEquals('mock_parent', $page->parent_slug());

		$this->assertNull((new Valid_Primary_Page())->parent_slug());
	}
}

