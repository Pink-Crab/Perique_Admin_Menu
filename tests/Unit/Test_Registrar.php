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
use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique_Admin_Menu\Registrar\Registrar;
use PinkCrab\Perique_Admin_Menu\Exception\Page_Exception;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Registrar extends WP_UnitTestCase {

	/** @testdox Attempting to register a primary menu_page without a group, should result in an error. */
	public function test_throws_exception_with_menu_primary_page_but_no_group() {
		$this->expectException( TypeError::class );
		$factory = new Registrar();
		$factory->register_primary( new Valid_Primary_Page(), null );
	}

	/** @testdox An invalid primary page type should result in an error.*/
	public function test_throws_exception_with_invalid_primary_page_type(): void {
		$this->expectException( Page_Exception::class );

		$factory = new Registrar();
		$this->createMock( Page::class );
		$factory->register_primary(
			$this->createMock( Page::class )
		);
	}

	/** @testdox An invalid sub page type should result in an error.*/
    public function test_throws_exception_with_invalid_sub_page_type(): void {
		$this->expectException( Page_Exception::class );

		$factory = new Registrar();
		$this->createMock( Page::class );
		$factory->register_subpage(
			$this->createMock( Page::class ),
			'parent_slug'
		);
	}

}
