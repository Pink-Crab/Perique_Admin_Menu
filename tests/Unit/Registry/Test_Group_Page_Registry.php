<?php

declare(strict_types=1);

/**
 * Unit tests for Group_Page_Registry.
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

namespace PinkCrab\Perique_Admin_Menu\Tests\Unit\Registry;

use WP_UnitTestCase;
use PinkCrab\Perique_Admin_Menu\Registry\Group_Page_Registry;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Group_Page_Registry extends WP_UnitTestCase {

	/** @testdox A new registry has no entries — has() returns false for any class and all() returns an empty array. */
	public function test_empty_registry(): void {
		$registry = new Group_Page_Registry();
		$this->assertFalse( $registry->has( Valid_Page::class ) );
		$this->assertSame( array(), $registry->all() );
		$this->assertNull( $registry->group_for( Valid_Page::class ) );
	}

	/** @testdox After record() the page class is reported by has() and the stored group is returned by group_for(). */
	public function test_record_then_has_and_group_for(): void {
		$registry = new Group_Page_Registry();
		$group    = new Valid_Group();

		$registry->record( Valid_Page::class, $group );

		$this->assertTrue( $registry->has( Valid_Page::class ) );
		$this->assertSame( $group, $registry->group_for( Valid_Page::class ) );
	}

	/** @testdox all() returns the full map of every recorded page class to its group. */
	public function test_all_returns_full_map(): void {
		$registry = new Group_Page_Registry();
		$group    = new Valid_Group();

		$registry->record( Valid_Primary_Page::class, $group );
		$registry->record( Valid_Page::class, $group );

		$all = $registry->all();
		$this->assertCount( 2, $all );
		$this->assertArrayHasKey( Valid_Primary_Page::class, $all );
		$this->assertArrayHasKey( Valid_Page::class, $all );
		$this->assertSame( $group, $all[ Valid_Page::class ] );
	}

	/** @testdox record() is first-write-wins — re-recording the same page class with a different group is a no-op. */
	public function test_record_first_write_wins(): void {
		$registry = new Group_Page_Registry();
		$first    = new Valid_Group();
		$second   = new Valid_Group();

		$registry->record( Valid_Page::class, $first );
		$registry->record( Valid_Page::class, $second );

		$this->assertSame( $first, $registry->group_for( Valid_Page::class ) );
	}

	/** @testdox record() silently ignores an empty page class string. */
	public function test_record_empty_string_is_noop(): void {
		$registry = new Group_Page_Registry();
		$registry->record( '', new Valid_Group() );

		$this->assertSame( array(), $registry->all() );
	}

	/** @testdox all_for_subclass() returns only entries whose key class is a subclass of the supplied base. */
	public function test_all_for_subclass_filters_by_subclass(): void {
		$registry = new Group_Page_Registry();
		$group    = new Valid_Group();

		$registry->record( Valid_Page::class, $group );
		$registry->record( Valid_Primary_Page::class, $group );

		$matched = $registry->all_for_subclass( \PinkCrab\Perique_Admin_Menu\Page\Menu_Page::class );

		$this->assertCount( 2, $matched );
		$this->assertArrayHasKey( Valid_Page::class, $matched );
		$this->assertArrayHasKey( Valid_Primary_Page::class, $matched );
	}

	/** @testdox all_for_subclass() returns an empty array when nothing matches the supplied base class. */
	public function test_all_for_subclass_returns_empty_when_no_match(): void {
		$registry = new Group_Page_Registry();
		$registry->record( Valid_Page::class, new Valid_Group() );

		$this->assertSame(
			array(),
			$registry->all_for_subclass( \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group::class )
		);
	}

	/** @testdox all_for_subclass() ignores classes that don't exist (defensive — registry could in principle hold stale class names). */
	public function test_all_for_subclass_skips_nonexistent_class_keys(): void {
		$registry = new Group_Page_Registry();
		$registry->record( Valid_Page::class, new Valid_Group() );
		$registry->record( 'PinkCrab\\Perique_Admin_Menu\\Tests\\Phantom_Class', new Valid_Group() );

		$matched = $registry->all_for_subclass( \PinkCrab\Perique_Admin_Menu\Page\Menu_Page::class );

		$this->assertCount( 1, $matched );
		$this->assertArrayHasKey( Valid_Page::class, $matched );
	}
}
