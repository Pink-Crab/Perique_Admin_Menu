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

namespace PinkCrab\Perique_Admin_Menu\Tests\Integration;

use Exception;
use WP_UnitTestCase;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique_Admin_Menu\Registrar\Registrar;
use PinkCrab\Perique_Admin_Menu\Registrar\Page_Dispatcher;
use PinkCrab\Perique_Admin_Menu\Tests\Integration\Helper_Factory;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Group;

class Test_Page_Dispatcher extends WP_UnitTestCase {

	use Helper_Factory;


	public function setUp(): void {
		parent::setup();
		$this->unset_app_instance();
	}

	/** @testdox When an exception is thrown creating the page, an admin notice should be generated to show the errors. */
	public function test_admin_exception_notice(): void {
		$di             = $this->createMock( DI_Container::class );
		$view           = $this->createMock( \PinkCrab\Perique\Services\View\View::class );
		$registrar = $this->createMock( Registrar::class );

		$registrar = new Page_Dispatcher( $di, $view, $registrar );

		$group     = new Valid_Group();
		$exception = new Exception( 'TEST EXCEPTION' );

		$this->expectOutputRegex( '/PinkCrab\\\Perique_Admin_Menu\\\Tests\\\Fixtures\\\Valid_Group\\\Valid_Group/' );

		$registrar->admin_exception_notice( $group, $exception );
		\do_action( 'admin_notices' );
	}



}
