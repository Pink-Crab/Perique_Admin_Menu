<?php

declare(strict_types=1);

/**
 * Unit tests for a Page Factory
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

use TypeError;
use WP_UnitTestCase;
use PinkCrab\Perique_Admin_Menu\Hooks;
use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique_Admin_Menu\Registrar\Registrar;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Registrar extends WP_UnitTestCase {

	/** @testdox Attempting to register a primary menu_page without a group, should result in an error. */
	public function test_throws_exception_with_menu_primary_page_but_no_group() {
		$this->expectException( TypeError::class );
		$registrar = new Registrar();
		$registrar->register_primary( new Valid_Primary_Page(), null );
	}

	/** @testdox When other types of primary page are passed to the registrar, an action should be fired to allow other extensions to register pages.*/
	public function test_fires_primary_page_action_with_custom_page_typey(): void {

		$mock_page     = $this->createMock( Page::class );
		$action_called = false;

		add_action(
			Hooks::PAGE_REGISTRAR_PRIMARY,
			function( $page, $group ) use ( &$action_called, $mock_page ) {
				if ( $page === $mock_page ) {
					$action_called = true;
				}
			},
			10,
			2
		);

		$registrar = new Registrar();
		$registrar->register_primary( $mock_page );

		$this->assertTrue( $action_called );
	}

	/** @testdox When other types of sub page are passed to the registrar, an action should be fired to allow other extensions to register pages.*/
	public function test_fires_sub_page_action_with_custom_page_type(): void {
		
		$mock_page     = $this->createMock( Page::class );
		$action_called = false;

		add_action(
			Hooks::PAGE_REGISTRAR_SUB,
			function( $page, $parent_slug ) use ( &$action_called, $mock_page ) {
				if ( $page === $mock_page && $parent_slug === 'parent_slug' ) {
					$action_called = true;
				}
			},
			10,
			2
		);

		$registrar = new Registrar();
		$registrar->register_subpage( $mock_page, 'parent_slug' );
		$this->assertTrue( $action_called );
	}

}
