<?php

declare(strict_types=1);

/**
 * Integration test with a valid Menu Page group.
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

namespace PinkCrab\Perique_Admin_Menu\Tests\Application;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Output;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Perique\Services\View\PHP_Engine;
use Gin0115\WPUnit_Helpers\WP\Menu_Page_Inspector;
use PinkCrab\Perique_Admin_Menu\Tests\Integration\Helper_Factory;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Valid_Menu_Group extends WP_UnitTestCase {

	use Helper_Factory;

	public function setUp(): void {
		parent::setup();
		$this->unset_app_instance();
	}

	/**
	 * @testdox [APPLICATION] When a valid group is registered, all of the defined group values should be used to regiser the group and all the pages should also be registered, with the group title differing form that of the primary page.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	*/
	public function test_valid_group(): void {

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
			->boot();

		$app->registration_middleware( $this->middleware_provider( $app ) );
		$app->registration_classes( array( Valid_Group::class ) );

		// Log in as admin and run the apps initialisation (on init hook)
		$admin_user = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'dashboard' );
		do_action( 'init' );

		$defined_group   = new Valid_Group();
		$defined_primary = new Valid_Primary_Page();

		// Build Page Inspector
		$inspector = Menu_Page_Inspector::initialise( true );

		// Group Tests.
		$group = $inspector->find_group( Valid_Primary_Page::PAGE_SLUG );

		$this->assertNotNull( $group );
		$this->assertEquals( $defined_group::GROUP_TITLE, $group->menu_title );
		$this->assertEquals( $defined_group::CAPABILITY, $group->permission );
		$this->assertEquals( $defined_group::ICON, $group->icon );
		$this->assertEquals( (int) $defined_group::POSITION, (int) $group->position );
		$this->assertEquals( $defined_primary::PAGE_SLUG, $group->menu_slug );
		$this->assertEquals( 'toplevel_page_' . $defined_primary::PAGE_SLUG, $group->hook_name );

		// Primary Page Tests.
		$primary = $inspector->find_child( $defined_primary::PAGE_SLUG );

		$this->assertNotNull( $primary );
		$this->assertEquals( $defined_primary->menu_title(), $primary->menu_title );
		$this->assertEquals( $defined_primary->page_title(), $primary->page_title );
		$this->assertEquals( $defined_primary->capability(), $primary->permission );
		$this->assertEquals( $defined_primary->slug(), $primary->menu_slug );
		$this->assertEquals( $defined_primary->slug(), $primary->parent_slug ); // Primary so slugs should match
		$output_priamry = Output::buffer(
			function() use ( $defined_primary ) {
				do_action(
					get_plugin_page_hookname( $defined_primary->slug(), '' ),
					function( $e ) {}
				);
			}
		);
		$this->assertEquals( 'Valid Primary Page Data', $output_priamry );
	}

	/**
	 * @testdox [APPLICATION] When a valid group is registered, all the page and group hooks should be registered.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	*/
	public function test_valid_group_page_hooks(): void {
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
			->boot();

		$app->registration_middleware( $this->middleware_provider( $app ) );
		$app->registration_classes( array( Valid_Group::class ) );

		// Log in as admin and run the apps initialisation (on init hook)
		$admin_user = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'dashboard' );
		do_action( 'init' );

		// Build Page Inspector
		$inspector = Menu_Page_Inspector::initialise( true );

		// Group Tests.
		$group = $inspector->find_group( Valid_Primary_Page::PAGE_SLUG );
	}
}
