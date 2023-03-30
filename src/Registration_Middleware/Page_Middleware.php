<?php

declare(strict_types=1);

/**
 * Registration Middleware for dispatching pages.
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

namespace PinkCrab\Perique_Admin_Menu\Registration_Middleware;

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique_Admin_Menu\Group\Abstract_Group;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique_Admin_Menu\Registrar\Page_Dispatcher;
use PinkCrab\Perique_Admin_Menu\Validator\Group_Validator;

class Page_Middleware implements Registration_Middleware {

	public Page_Dispatcher $dispatcher;
	public Group_Validator $group_validator;
	public Hook_Loader $hook_loader;

	public function __construct(
		Page_Dispatcher $dispatcher,
		Group_Validator $group_validator,
		Hook_Loader $hook_loader
	) {
		$this->dispatcher      = $dispatcher;
		$this->group_validator = $group_validator;
		$this->hook_loader     = $hook_loader;
	}

	/**
	 * Add all valid ajax calls to the dispatcher.
	 *
	 * @param object $class
	 * @return object
	 */
	public function process( $class ) {
		// If we have a valid SUB page.
		if (
			is_a( $class, Page::class )
			&& is_admin()
			&& ! is_null( $class->parent_slug() )
		) {
			$this->add_to_loader(
				function () use ( $class ) : void {
					$this->dispatcher->register_subpage( $class, $class->parent_slug() );
				}
			);
		}

		// If we have a valid SINGLE/PARENT page.
		if (
			is_a( $class, Page::class )
			&& is_admin()
			&& is_null( $class->parent_slug() )
		) {
			$this->add_to_loader(
				function () use ( $class ) : void {
					$this->dispatcher->register_single_page( $class );
				}
			);
		}

		// If we have a valid group.
		if (
			is_a( $class, Abstract_Group::class )
			&& is_admin()
		) {

			$this->add_to_loader(
				function () use ( $class ): void {
					$this->dispatcher->register_group( $class );
				}
			);
		}
		return $class;
	}

	/**
	 * Adds a page to the Hook Loader
	 *
	 * @param callable $callback
	 * @return void
	 */
	protected function add_to_loader( callable $callback ): void {
		$this->hook_loader->action( 'admin_menu', $callback );
	}


	/**
	 * Registers the new page hook subscribers
	 * Creates the page on a custom callback.
	 *
	 * @return void
	 */
	public function setup(): void {}

	/**
	 * Register all ajax calls.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		$this->hook_loader->register_hooks();
	}
}
