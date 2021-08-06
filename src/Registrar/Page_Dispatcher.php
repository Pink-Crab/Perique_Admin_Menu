<?php

declare(strict_types=1);

/**
 * Registers page groups.
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

namespace PinkCrab\Perique_Admin_Menu\Registrar;

use Throwable;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique_Admin_Menu\Registrar\Registrar;
use PinkCrab\Perique_Admin_Menu\Group\Abstract_Group;
use PinkCrab\Perique_Admin_Menu\Exception\Page_Exception;
use PinkCrab\Perique_Admin_Menu\Exception\Group_Exception;
use PinkCrab\Perique_Admin_Menu\Validator\Group_Validator;

class Page_Dispatcher {

	/** @var \PinkCrab\Perique\Interfaces\DI_Container */
	protected $di_container;

	/** @var \PinkCrab\Perique\Services\View\View */
	protected $view;

	/** @var \PinkCrab\Perique_Admin_Menu\Registrar\Registrar */
	protected $registrar;

	public function __construct( DI_Container $di_container, View $view, Registrar $registrar ) {
		$this->di_container = $di_container;
		$this->view         = $view;
		$this->registrar    = $registrar;
	}

	/**
	 * Registers the group and all of its pages.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group $group
	 * @return void
	 */
	public function register_group( Abstract_Group $group ): void {

		// If current user can not access the group, bail without attempting to register.
		if ( ! current_user_can( $group->get_capability() ) ) {
			return;
		}

		try {

			// Validate the group.
			$validator = new Group_Validator();
			if ( ! $validator->validate( $group ) ) {
				throw Group_Exception::failed_validation( $validator, $group );
			}

			$this->register_primary_page( $group );

			// Register all pages and attempt to set primary page name in menu.
			foreach ( $this->get_pages( $group ) as $page ) {
				$this->register_subpage( $page, $this->get_primary_page( $group )->slug() );
			}
			$this->set_primary_page_details( $group );

		} catch ( \Throwable $th ) {
			$this->admin_exception_notice( $group, $th );
		}
	}

	/**
	 * Handles the display of errors thrown registering admin menu pages in wp_admin.
	 *
	 * @param Abstract_Group|Page $object
	 * @param \Throwable $exception
	 * @return void
	 */
	public function admin_exception_notice( $object, Throwable $exception ): void {
		add_action(
			'admin_notices',
			function() use ( $object, $exception ) {
				$class   = 'notice notice-error';
				$message = sprintf(
					'%s <i>%s</i> generated errors while being registered. This might result in admin pages being missing or broken. <br><b>%s(%s: %s)</b>',
					get_class( $object ) === Page::class ? 'Page' : 'Menu Group',
					get_class( $object ),
					$exception->getMessage(),
					$exception->getFile(),
					$exception->getLine()
				);
				printf(
					'<div class="%1$s"><p>%2$s</p></div>',
					esc_attr( $class ),
					wp_kses(
						$message,
						array(
							'br' => array(),
							'b'  => array(),
							'i'  => array(),
						)
					)
				);
			}
		);
	}

	/**
	 * Registers the primary page and group.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group $group
	 * @return void
	 */
	protected function register_primary_page( Abstract_Group $group ): void {
		$this->registrar->register_primary(
			$this->get_primary_page( $group ),
			$group
		);
	}

	/**
	 * Gets the constructed primary page.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group $group
	 * @return \PinkCrab\Perique_Admin_Menu\Page\Page
	 */
	protected function get_primary_page( Abstract_Group $group ): Page {
		/** @var Page */
		$page = $this->di_container->create( $group->get_primary_page() );

		if ( ! is_object( $page ) || ! is_a( $page, Page::class ) ) {
			throw Page_Exception::invalid_page_type( $page );
		}

		// Register view if requied.
		if ( \method_exists( $page, 'set_view' ) ) {
			$page->set_view( $this->view );
		}

		return $page;
	}

	/**
	 * Returns an array of all pages, constructed.
	 * Excludes the primary page.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group $group
	 * @return array<Page>
	 * @throws Page_Exception (Code 202)
	 */
	protected function get_pages( Abstract_Group $group ): array {
		return array_map(
			function( string $page ): Page {
				$constructed_page = $this->di_container->create( $page );
				if ( ! is_object( $constructed_page ) || ! is_a( $constructed_page, Page::class ) ) {
					throw Page_Exception::invalid_page_type( $constructed_page );
				}
				return $constructed_page;
			},
			array_filter(
				$group->get_pages(),
				function( string $page ) use ( $group ) {
					return $page !== $group->get_primary_page();
				}
			)
		);
	}


	/**
	 * Sets the menu title the primary page value.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group $group
	 * @return void
	 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
	 * @throws Group_Exception (code 253)
	 */
	protected function set_primary_page_details( Abstract_Group $group ) {
		global $submenu;

		$primary = $this->get_primary_page( $group );

		if ( ! array_key_exists( $primary->slug(), $submenu ) ) {
			return;
		}

		$primary_page_key = array_search(
			$primary->slug(),
			\array_column( $submenu[ $primary->slug() ], 2 ),
			true
		) ?: 0;

		$submenu['valid_primary_page'][ $primary_page_key ][0] = $primary->menu_title();
	}

	// Single Pages
		/**
	 * Registers a subpage.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Page\Page $page
	 * @param string $parent_slug
	 * @return void
	 */
	public function register_subpage( Page $page, string $parent_slug ): void {
		// If user cant access the page, bail before attemptin to register.
		if ( ! current_user_can( $page->capability() ) ) {
			return;
		}

		// Register view if requied.
		if ( \method_exists( $page, 'set_view' ) ) {
			$page->set_view( $this->view );
		}

		try {
			$this->registrar->register_subpage( $page, $parent_slug );
		} catch ( \Throwable $th ) {
			$this->admin_exception_notice( $page, $th );
		}
	}


}
