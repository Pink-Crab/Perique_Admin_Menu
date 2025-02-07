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

namespace PinkCrab\Perique_Admin_Menu\Page;

use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique_Admin_Menu\Exception\Page_Exception;

abstract class Menu_Page implements Page {

	/**
	 * The pages menu slug.
	 *
	 * @var string|null
	 */
	protected ?string $parent_slug = null;

	/**
	 * The pages menu slug.
	 *
	 * @var string
	 */
	protected string $page_slug = '';

	/**
	 * The menu title
	 *
	 * @var string
	 */
	protected string $menu_title = '';

	/**
	 * The pages title
	 *
	 * @var string
	 */
	protected string $page_title = '';

	/**
	 * The pages position, in relation to other pages in group.
	 *
	 * @var int|null
	 */
	protected ?int $position = null;

	/**
	 * The min capabilities required to access page.
	 *
	 * @var string
	 */
	protected string $capability = 'manage_options';

	/**
	 * The template to be rendered.
	 *
	 * @var string
	 */
	protected string $view_template = '';

	/**
	 * Data to be used to render the page.
	 *
	 * @var array<string, mixed>
	 */
	protected array $view_data = array();

	/**
	 * Holds the page hook.
	 *
	 * @var ?string
	 */
	protected ?string $page_hook = null;

	/**
	 * View
	 *
	 * @var View
	 */
	protected ?View $view = null;

	/**
	 * Set view
	 *
	 * @param View $view  View
	 * @return self
	 */
	public function set_view( View $view ): self {
		$this->view = $view;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function parent_slug(): ?string {
		return $this->parent_slug;
	}

	/**
	 * @return string
	 */
	public function slug(): string {
		if ( $this->page_slug === '' ) {
			throw Page_Exception::undefined_property( 'page_slug', $this ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped, escaped in exception.
		}
		return $this->page_slug;
	}

	/**
	 * @return string
	 */
	public function menu_title(): string {
		if ( $this->menu_title === '' ) {
			throw Page_Exception::undefined_property( 'menu_title', $this ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped, escaped in exception.
		}
		return $this->menu_title;
	}

	/**
	 * @return string|null
	 */
	public function page_title(): ?string {
		return $this->page_title;
	}

	/**
	 * @return int|null
	 */
	public function position(): ?int {
		return $this->position;
	}

	/**
	 * @return string
	 */
	public function capability(): string {
		return $this->capability;
	}

	/**
	 * Renders the page view.
	 *
	 * @return callable
	 * @throws Page_Exception code 200 if view not defined.
	 * @throws Page_Exception code 201 if template not defined.
	 */
	public function render_view(): callable {
		if ( null === $this->view ) {
			throw Page_Exception::view_not_set( $this ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped, escaped in exception.
		}

		if ( '' === $this->view_template ) {
			throw Page_Exception::undefined_property( 'view_template', $this ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped, escaped in exception.
		}

		return function () {
			// @phpstan-ignore-next-line, as we have already checked for null.
			$this->view->render( $this->view_template, $this->view_data );
		};
	}

	/**
	 * Callback for enqueuing scripts and styles at a page level.
	 *
	 * @param Page $page
	 * @return void
	 * @codeCoverageIgnore This can be tested as it does nothing and is extended only
	 */
	public function enqueue( Page $page ): void {
		// Do nothing.
		// Can be extended in any child class that extends.
	}

	/**
	 * Callback for the pre-load of the page
	 *
	 * @param Page $page
	 * @return void
	 * @codeCoverageIgnore This can be tested as it does nothing and is extended only
	 */
	public function load( Page $page ): void {
		// Do nothing.
		// Can be extended in any child class that extends.
	}

	/**
	 * Sets the page hook
	 *
	 * @param string $page_hook
	 * @return void
	 */
	final public function set_page_hook( string $page_hook ): void {
		$this->page_hook = $page_hook;
	}

	/**
	 * Gets the page hook
	 *
	 * @return string|null
	 */
	final public function page_hook(): ?string {
		return $this->page_hook;
	}
}
