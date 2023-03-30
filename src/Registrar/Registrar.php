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
use PinkCrab\Perique_Admin_Menu\Registrar\Page_Load_Action;

class Registrar {

	/**
	 * Used to register a primary page for a group.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Page\Page $page
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group|null $group
	 * @return void
	 */
	public function register_primary( Page $page, ?Abstract_Group $group = null ): void {

		switch ( true ) {
			// For menu pages
			case $page instanceof Menu_Page:
				/** @var Menu_Page $page */
				$page = $page;

				$hook = add_menu_page(
					$page->page_title() ?? '',
					$group ? $group->get_group_title() : $page->menu_title(),
					$group ? $group->get_capability() : $page->capability(),
					$page->slug(),
					$page->render_view(),
					$group ? $group->get_icon() : '',
					(int) ( $group ? $group->get_position() : $page->position() )
				);

				$page->set_page_hook( $hook );

				// Register Enqueue hooks for page/group.
				$this->enqueue_scripts( $hook, $page, $group );
				// Register hook for pre-load page
				$this->pre_load_hook( $hook, $page, $group );

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
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group|null $group
	 * @return void
	 */
	public function register_subpage( Page $page, string $parent_slug, ?Abstract_Group $group = null ): void {
		switch ( true ) {
			case $page instanceof Menu_Page:
				/** @var Menu_Page $page */
				$page = $page;

				$hook = add_submenu_page(
					$parent_slug,
					$page->page_title() ?? '',
					$page->menu_title(),
					$page->capability(),
					$page->slug(),
					$page->render_view(),
					$page->position()
				);

				// If the sub page cant be registered because of permissions. Then we need to register the page as a primary page.
				if ( ! is_string( $hook ) ) {
					return;
				}

				// Set the pages hook.
				$page->set_page_hook( $hook );

				// Register Enqueue hooks for page/group.
				$this->enqueue_scripts( $hook, $page, $group );

				// Register hook for pre-load page
				$this->pre_load_hook( $hook, $page, $group );

				break;
			default:
				do_action( Hooks::PAGE_REGISTRAR_SUB, $page, $parent_slug );
		}
	}

	/**
	 * Adds enqueue admin scripts based on the current page hook.
	 *
	 * @param string $hook
	 * @param \PinkCrab\Perique_Admin_Menu\Page\Page $page
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group|null $group
	 * @return void
	 */
	protected function enqueue_scripts( string $hook, Page $page, ?Abstract_Group $group = null ): void {
		add_action( 'admin_enqueue_scripts', new Page_Enqueue_Action( $hook, $page, $group ) );
	}

	/**
	 * Adds the pre load actions for the current page.
	 *
	 * @param string $hook
	 * @param \PinkCrab\Perique_Admin_Menu\Page\Page $page
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group|null $group
	 * @return void
	 */
	protected function pre_load_hook( string $hook, Page $page, ?Abstract_Group $group = null ): void {
		add_action( 'load-' . $hook, new Page_Load_Action( $page, $group ) );
	}
}
