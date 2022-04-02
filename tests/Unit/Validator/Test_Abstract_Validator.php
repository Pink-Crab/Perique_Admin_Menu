<?php

declare(strict_types=1);

/**
 * Abstract Validator unit tests
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
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique_Admin_Menu\Validator\Abstract_Validator;

class Test_Abstract_Validator extends WP_UnitTestCase {

	protected $mock_validator;

	public function setUp(): void {
		$this->mock_validator = new class() extends Abstract_Validator{
			public function validate( $subject ): bool {
				return true;
			}
		};
	}

	/** @testdox It should be possible to push an error message to the internal error list. */
	public function test_can_push_error(): void {
		$this->mock_validator->push_error( 'Test1' );
		$this->assertCount( 1, $this->mock_validator->get_errors() );
	}

	/** @testdox It should be possible to check if any errors have been logged. */
	public function test_has_errors(): void {
		$this->assertFalse( $this->mock_validator->has_errors() );
		$this->mock_validator->push_error( 'Test1' );
		$this->assertTrue( $this->mock_validator->has_errors() );
	}

	/** @testdox It should be possible for the error log to be reset (internally, no public facing access!) */
	public function test_can_reset_errors(): void {
		$this->mock_validator->push_error( 'Test1' );
		$this->assertCount( 1, $this->mock_validator->get_errors() );
		Objects::invoke_method( $this->mock_validator, 'reset_errors' );
		$this->assertCount( 0, $this->mock_validator->get_errors() );
	}

	/** @testdox It should be possible to get the contents of the internal error list. */
    public function test_can_get_errors(): void {
		$this->mock_validator->push_error( 'Test1' );
		$this->mock_validator->push_error( 'Test2' );
		$this->mock_validator->push_error( 'Test3' );
		$this->assertCount( 3, $this->mock_validator->get_errors() );
		$this->assertContains( 'Test1', $this->mock_validator->get_errors() );
		$this->assertContains( 'Test2', $this->mock_validator->get_errors() );
		$this->assertContains( 'Test3', $this->mock_validator->get_errors() );
	}
}
