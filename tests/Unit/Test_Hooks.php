<?php

declare(strict_types=1);

/**
 * Unit tests for the Hooks constants.
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

namespace PinkCrab\Perique_Admin_Menu\Tests\Unit;

use WP_UnitTestCase;
use PinkCrab\Perique_Admin_Menu\Hooks;

class Test_Hooks extends WP_UnitTestCase {

	/** @testdox Every published Hooks constant uses the canonical pinkcrab/admin_menu/ prefix. */
	public function test_all_hooks_use_canonical_prefix(): void {
		$prefix = 'pinkcrab/admin_menu/';
		$this->assertStringStartsWith( $prefix, Hooks::PAGE_REGISTRAR_PRIMARY );
		$this->assertStringStartsWith( $prefix, Hooks::PAGE_REGISTRAR_SUB );
		$this->assertStringStartsWith( $prefix, Hooks::ENQUEUE_GROUP );
		$this->assertStringStartsWith( $prefix, Hooks::GROUPS_PROCESSED );
	}

	/** @testdox Hooks::GROUPS_PROCESSED resolves to the documented action name. */
	public function test_groups_processed_constant_value(): void {
		$this->assertSame( 'pinkcrab/admin_menu/groups_processed', Hooks::GROUPS_PROCESSED );
	}
}
