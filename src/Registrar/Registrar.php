<?php

declare(strict_types=1);

/**
 * Used to register all pages and subpages.
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

use TypeError;
use PinkCrab\Perique_Admin_Menu\Hooks;
use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique_Admin_Menu\Page\Menu_Page;
use PinkCrab\Perique_Admin_Menu\Group\Abstract_Group;
use PinkCrab\Perique_Admin_Menu\Exception\Page_Exception;

class Registrar {

	/**
	 * Used to register a primary page for a group.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Page\Page $page
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group $group
	 * @return void
	 * @throws Page_Exception (Code 204)
	 * @throws TypeError
	 */
	public function register_primary( Page $page, Abstract_Group $group = null ): void {

		switch ( get_parent_class( $page ) ) {
			// For menu pages
			case Menu_Page::class:
				if ( $group === null ) {
					throw new TypeError( 'Valid group must be passed to create Menu_Page' );
				}

				$result = add_menu_page(
					$page->page_title() ?? '',
					$group->get_group_title(),
					$group->get_capability(),
					$page->slug(),
					$page->render_view(),
					$group->get_icon(),
					(int) $group->get_position()
				);

				// Call failed action for logging etc.
				if ( ! is_string( $result ) ) {
					throw Page_Exception::failed_to_register_page( $page );
				}
				break;

			default:
				do_action( Hooks::PAGE_REGISTRAR_PRIMARY, $page, $group );
		}
	}

	/**
	 * Used to register a sub menu page for any group.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Page\Page $page
	 * @param string $parent_slug
	 * @return void
	 * @throws Page_Exception (Code 204)
	 */
	public function register_subpage( Page $page, string $parent_slug ): void {
		switch ( get_parent_class( $page ) ) {
			case Menu_Page::class:
				$hook = add_submenu_page(
					$parent_slug,
					$page->page_title() ?? '',
					$page->menu_title(),
					$page->capability(),
					$page->slug(),
					$page->render_view(),
					$page->position()
				);

				if ( ! is_string( $hook ) ) {
					throw Page_Exception::failed_to_register_page( $page );
				}
				break;
			default:
				do_action( Hooks::PAGE_REGISTRAR_SUB, $page, $parent_slug );
		}
	}
}
