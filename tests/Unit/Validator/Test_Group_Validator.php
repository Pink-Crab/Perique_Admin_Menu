<?php

declare(strict_types=1);

/**
 * Group Validator unit tests
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
use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique_Admin_Menu\Validator\Group_Validator;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Invalid_Group\Invalid_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Invalid_Group\Invalid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Invalid_Group\Invalid_Primary_Page;

class Test_Group_Validator extends WP_UnitTestCase {

	/** @testdox When validating a non group, an error should left in the error list to denote its not being a group. */
	public function test_fails_validation_on_non_group(): void {
		$validator = new Group_Validator();
		$result    = $validator->validate( new stdClass );

		$this->assertFalse( $result );
		$this->assertEquals( 'stdClass Is not a valid group type.', $validator->get_errors()[0] );
	}

	/** @testdox When validating a group, errors should be logged if either the group has no menu title or primary page. */
	public function test_fails_validation_due_to_missing_properties(): void {
		$group     = new Invalid_Group();
		$validator = new Group_Validator();

		// Fails on no group slug
		$result = $validator->validate( $group );
		$this->assertFalse( $result );
		$this->assertStringContainsString( 'primary page', $validator->get_errors()[0] );
		$this->assertStringContainsString( Invalid_Group::class, $validator->get_errors()[0] );
		Objects::set_property( $group, 'primary_page', Valid_Primary_Page::class );

		// Fails on no group title
		$result = $validator->validate( $group );
		$this->assertFalse( $result );
		$this->assertStringContainsString( 'group title', $validator->get_errors()[0] );
		$this->assertStringContainsString( Invalid_Group::class, $validator->get_errors()[0] );
	}

	/** @testdox A valid group (has menu title and primary page defined.) should pass the Group Validators validation.*/
    public function test_valid_group_passes_validation(): void {
		$group     = new Valid_Group();
		$validator = new Group_Validator();
		$result    = $validator->validate( $group );
		$this->assertTrue( $result );
	}
}
