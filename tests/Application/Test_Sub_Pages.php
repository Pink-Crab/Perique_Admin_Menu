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
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Perique\Services\View\PHP_Engine;
use Gin0115\WPUnit_Helpers\WP\Menu_Page_Inspector;
use PinkCrab\Perique_Admin_Menu\Module\Admin_Menu;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Tools_Sub_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Integration\Helper_Factory;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Sub_Pages extends WP_UnitTestCase {

	use Helper_Factory;

	public function setUp(): void {
		parent::setup();
		$this->unset_app_instance();
	}

	/**
	 * @testdox [APPLICATION] When a sub page is registered, it should be added to the menu without using a group.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	*/
	public function test_sub_page_of_tools_php(): void {

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
			->module(Admin_Menu::class)
			->boot();

		// $app->registration_middleware( $this->middleware_provider( $app ) );
		$app->registration_classes( array( Tools_Sub_Page::class, Valid_Primary_Page::class ) );

		// Log in as admin and run the apps initialisation (on init hook)
		$admin_user = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'dashboard' );

		// Create a menu page to act as the parent for sub page.
		add_action(
			'admin_menu',
			function() {
				\add_menu_page( 'Tools', 'Tools', 'manage_options', 'foo_tools', '', 'dashicons-admin-generic', 6 );
			}
		);
		// Boot the app.
		do_action( 'init' );

		// Build Page Inspector (Force to run admin_action hook call)
		$inspector = Menu_Page_Inspector::initialise( true );

		// Primary Page Tests.
		$single_page = $inspector->find_parent( 'valid_primary_page' );
		$this->assertNotNull( $single_page );
		$this->assertEquals( Valid_Primary_Page::MENU_TITLE, $single_page->menu_title );
		$this->assertEquals( Valid_Primary_Page::PAGE_TITLE, $single_page->page_title );
		$this->assertEquals( Valid_Primary_Page::PAGE_SLUG, $single_page->menu_slug );

		// Sub Page Tests.
		$sub_page = $inspector->find_child( 'tools_sub_page' );
		$this->assertNotNull( $sub_page );
		$this->assertEquals( Tools_Sub_Page::MENU_TITLE, $sub_page->menu_title );
		$this->assertEquals( Tools_Sub_Page::PAGE_TITLE, $sub_page->page_title );
		$this->assertEquals( Tools_Sub_Page::PAGE_SLUG, $sub_page->menu_slug );
		$this->assertEquals( Tools_Sub_Page::PARENT_SLUG, $sub_page->parent_slug );
	}
}
