<?php

declare(strict_types=1);

/**
 * Full Perique Application tests for invalid menu groups.
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
use PinkCrab\Perique_Admin_Menu\Tests\Integration\Helper_Factory;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Invalid_Group\Invalid_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Test_Invalid_Menu_Group extends WP_UnitTestCase {

	use Helper_Factory;

	public function setUp(): void {
		parent::setUp();
		$this->unset_app_instance();
	}

	/**
	 * @testdox [APPLICATION] If the current user fails to meet the permissions defined to the group, do not create group.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	*/
	public function test_inusfficiant_capabilities_register_group(): void {

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

		// Log in as customer and run the apps initialisation (on init hook)
		$admin_user = self::factory()->user->create( array( 'role' => 'customer' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'dashboard' );
		do_action( 'init' );

		$defined_group   = new Valid_Group();
		$defined_primary = new Valid_Primary_Page();

		// Build Page Inspector
		$inspector = Menu_Page_Inspector::initialise( true );

		// Group Tests.
		$group = $inspector->find_group( Valid_Primary_Page::PAGE_SLUG );

		$this->assertNull( $group );

	}
	/**
	 * @testdox [APPLICATION] If an exception is thrown during group regisutration and admin notice should be shown.
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	*/
	public function test_throws_error_creating_group(): void {
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
		$app->registration_classes( array( Invalid_Group::class ) );

		// Log in as customer and run the apps initialisation (on init hook)
		$admin_user = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'dashboard' );
		do_action( 'init' );
		do_action( 'admin_menu' );
		$this->expectOutputRegex( '/The primary page is not defined in/' );
		$this->expectOutputRegex( '/PinkCrab\\\Perique_Admin_Menu\\\Tests\\\Fixtures\\\Invalid_Group\\\Invalid_Group/' );
		do_action( 'admin_notices' );

	}
}
