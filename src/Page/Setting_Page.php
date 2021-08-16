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
use PinkCrab\Perique\Interfaces\DI_Container;

use PinkCrab\Perique_Admin_Menu\Util\Hooks;

use PinkCrab\Perique_Admin_Menu\Setting\Abstract_Settings;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique_Admin_Menu\Exception\Page_Exception;
use PinkCrab\Perique_Admin_Menu\Setting\Setting_View_Renderer;
use PinkCrab\Perique_Admin_Menu\Util\File_Helper;

abstract class Setting_Page implements Page {

	/**
	 * The pages menu slug.
	 *
	 * @var string|null
	 */
	protected $parent_slug;

	/**
	 * The pages menu slug.
	 *
	 * @var string
	 */
	protected $page_slug;

	/**
	 * The menu title
	 *
	 * @var string
	 */
	protected $menu_title;

	/**
	 * The pages title
	 *
	 * @var string
	 */
	protected $page_title;

	/**
	 * The pages position, in relation to other pages in group.
	 *
	 * @var int|null
	 */
	protected $position = null;

	/**
	 * The min capabilities required to access page.
	 *
	 * @var string
	 */
	protected $capability = 'manage_options';

	/**
	 * The settings data.
	 *
	 * @var Abstract_Settings
	 */
	protected $settings;

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
		if ( $this->page_slug === null ) {
			throw Page_Exception::undefined_property( 'page_slug', $this );
		}
		return $this->page_slug;
	}

	/**
	 * @return string
	 */
	public function menu_title(): string {
		if ( $this->menu_title === null ) {
			throw Page_Exception::undefined_property( 'menu_title', $this );
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
	 * Renders the settings for this page using the DI Container.
	 *
	 * @param DI_Container $container
	 * @return Abstract_Settings|null
	 */
	public function construct_settings( DI_Container $container ): void {
		$settings = $container->create( $this->settings_class_name() );

		// Throw exception if not settings created.
		if ( ! is_a( $settings, Abstract_Settings::class ) ) {
			// @TODO
		}

		$this->settings = $settings;
	}

	/**
	 * Returns the full name of the settings class.
	 *
	 * @return string
	 */
	abstract public function settings_class_name(): string;

	/**
	 * Renders the page view.
	 *
	 * @return callable
	 * @throws Page_Exception code 200 if view not defined.
	 * @throws Page_Exception code 201 if template not defined.
	 */
	public function render_view(): callable {

		if ( $this->settings === null ) {
			throw Page_Exception::undefined_property( 'settings_data', $this );
		}

		// Maybe render header.
		$header = View::print_buffer(
			function() {
				/**
				 * Calls the settings page header action.
				 *
				 * @param Page $this The current page.
				 */
				do_action( Hooks::settings_page_header_action( $this->slug() ), $this );
			}
		);

		// Maybe render footer.
		$footer = View::print_buffer(
			function() {
				/**
				 * Calls the settings page footer action.
				 *
				 * @param Page $this The current page.
				 */
				do_action( Hooks::settings_page_footer_action( $this->slug() ), $this );
			}
		);

		/**
		 * Filters the path used for the settings page view path.
		 *
		 * @param string The current path to the view file.
		 * @param Page The current page
		 * @return string The path to the view file.
		 */
		$settings_view = apply_filters(
			Hooks::settings_page_view_path( $this->slug() ),
			\dirname( __DIR__, 1 ) . '/Form/form_view.php',
			$this
		);

		$view = new Setting_View_Renderer(
			$settings_view,
			$this->settings,
            $this,
			$header,
			$footer
		);

		return $view->generate_view_callback();
	}
}
