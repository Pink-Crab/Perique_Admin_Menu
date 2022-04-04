<?php

declare(strict_types=1);

/**
 * Abstract Group unit tests
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

use WP_UnitTestCase;
use PinkCrab\Perique_Admin_Menu\Exception\Group_Exception;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Invalid_Group\Invalid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Abstract_Group extends WP_UnitTestCase {

	/** @testdox It should be possible to get the title for a group. */
	public function test_can_get_group_title(): void {
		$valid_group = new Valid_Group();
		$this->assertEquals( Valid_Group::GROUP_TITLE, $valid_group->get_group_title() );
	}

	/** @testdox If a group title is not set, it should result in an error. */
	public function test_throws_with_missing_group_title(): void {
		$this->expectException( Group_Exception::class );
		$this->expectExceptionCode( 251 );
		$invalid_group = new Invalid_Group();
		$this->assertEquals( Invalid_Group::class, $invalid_group->get_group_title() );
	}

	/** @testdox It should be possible to get the group access capabilities. */
	public function test_can_get_capabilities(): void {
		$valid_group = new Valid_Group();
		$this->assertEquals( 'manage_options', $valid_group->get_capability() );
	}

	/** @testdox It should be possible to get the group  icon. */
	public function test_can_get_icon(): void {
		$valid_group = new Valid_Group();
		$this->assertEquals( 'dashicons-admin-generic', $valid_group->get_icon() );
	}

	/** @testdox It should be possible to get the group access primary_page. */
	public function test_can_get_primary_page(): void {
		$valid_group = new Valid_Group();
		$this->assertEquals( Valid_Primary_Page::class, $valid_group->get_primary_page() );
	}

	/** @testdox If a groups primary page is not set, it should throw an exception if trying to access its value. */
	public function test_throws_if_no_primary_page_defined(): void {
		$this->expectException( Group_Exception::class );
		$this->expectExceptionCode( 250 );
		$invalid_group = new Invalid_Group();
		$this->assertEquals( Invalid_Group::class, $invalid_group->get_primary_page() );
	}

	/** @testdox It should be possible to get the groups page list */
	public function test_can_get_pages(): void {
		$valid_group = new Valid_Group();
		$this->assertTrue( is_array( $valid_group->get_pages() ) );
		$this->assertContains( Valid_Primary_Page::class, $valid_group->get_pages() );
	}

	/** @testdox It should be possible to get the groups position */
	public function test_can_get_positions(): void {
		$valid_group = new Valid_Group();
		$this->assertEquals( 65, $valid_group->get_position() );
	}
}
