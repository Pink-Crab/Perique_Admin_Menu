<?php

declare(strict_types=1);

/**
 * The abstract class used to create Page Groups within WP-Admin
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

namespace PinkCrab\Perique_Admin_Menu\Group;

use PinkCrab\Perique_Admin_Menu\Exception\Group_Exception;
use PinkCrab\Perique_Admin_Menu\Page\Page;

use PinkCrab\Perique_Admin_Menu\Page\Menu_Page;



abstract class Abstract_Group {

	/**
	 * The group title.
	 *
	 * @var string
	 * @required
	 */
	protected $group_title;

	/**
	 * The minimum capabilities to show menu group
	 *
	 * @var string
	 * @default 'manage_options'
	 */
	protected $capability = 'manage_options';

	/**
	 * The icon to display, either url or dashicon
	 *
	 * @var string
	 * @default 'dashicons-admin-generic'
	 */
	protected $icon = 'dashicons-admin-generic';

	/**
	 * The primary page
	 *
	 * @var string
	 */
	protected $primary_page;

	/**
	 * Array of classnames, of all the pages.
	 *
	 * @var string[]
	 */
	protected $pages;

	/**
	 * Holds the groups menu position.
	 *
	 * @var int
	 */
	protected $position = 65;

	/**
	 * Get the group title.
	 *
	 * @return string
	 */
	public function get_group_title(): string {
		if ( $this->group_title === null ) {
			throw Group_Exception::group_title_undefined( $this ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped, escaped in exception.
		}
		return $this->group_title;
	}

	/**
	 * Get the minimum capabilities to show menu group
	 *
	 * @return string
	 */
	public function get_capability(): string {
		return $this->capability;
	}

	/**
	 * Get the icon to display, either url or dashicon
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return $this->icon;
	}

	/**
	 * Get the primary page
	 *
	 * @return string
	 */
	public function get_primary_page(): string {
		if ( $this->primary_page === null ) {
			throw Group_Exception::primary_page_undefined( $this ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped, escaped in exception.
		}
		return $this->primary_page;
	}

	/**
	 * Get array of classnames, of all the pages.
	 *
	 * @return string[]
	 */
	public function get_pages(): array {
		return $this->pages;
	}

	/**
	 * Set holds the groups menu position.
	 *
	 * @return integer
	 */
	public function get_position(): int {
		return $this->position;
	}

	/**
	 * Callback for enqueuing scripts and styles at a group level.
	 *
	 * @param Abstract_Group $group
	 * @param Page           $page
	 * @return void
	 * @codeCoverageIgnore This can't be tested as it does nothing and is extended only
	 */
	public function enqueue( Abstract_Group $group, Page $page ): void {
		// Do nothing by default.
	}

	/**
	 * Callback for triggering pre load actions for the groups page (at group level)
	 *
	 * @param Abstract_Group $group
	 * @param Page           $page
	 * @return void
	 * @codeCoverageIgnore This can't be tested as it does nothing and is extended only
	 */
	public function load( Abstract_Group $group, Page $page ): void {
		// Do nothing by default.
	}
}
