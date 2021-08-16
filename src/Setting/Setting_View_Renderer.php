<?php

declare(strict_types=1);

/**
 * Interface for a settings repository
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

namespace PinkCrab\Perique_Admin_Menu\Setting;

use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique_Admin_Menu\Util\Hooks;
use PinkCrab\Perique_Admin_Menu\Page\Setting_Page;
use PinkCrab\Perique_Admin_Menu\Setting\Field\Field;
use PinkCrab\Perique_Admin_Menu\Form\Element_Default;
use PinkCrab\Perique_Admin_Menu\Form\Element_Factory;

class Setting_View_Renderer {

	/**
	 * The path to the view file.
	 *
	 * @var string
	 */
	protected $view;

	/**
	 * The curren settings
	 *
	 * @var Abstract_Settings
	 */
	protected $settings;

	/**
	 * The page
	 *
	 * @var Setting_Page
	 */
	protected $page;

	/**
	 * Additional header content.
	 *
	 * @var string
	 */
	protected $header;

	/**
	 * Additional footer content.
	 *
	 * @var string
	 */
	protected $footer;

	public function __construct(
		string $view,
		Abstract_Settings $settings,
		Setting_Page $page,
		string $header,
		string $footer
	) {
		$this->view     = $view;
		$this->settings = $settings;
		$this->page     = $page;
		$this->header   = $header;
		$this->footer   = $footer;
	}

	/**
	 * Renders the passed view as a callable.
	 *
	 * @return callable
	 */
	public function generate_view_callback(): callable {
		return function() {
			print $this->parse_view();
		};
	}

	/**
	 * Parses the settings page view.
	 *
	 * @return string
	 */
	private function parse_view(): string {
		return View::print_buffer(
			function() {
				// Generate template variables.
				$data['title'] = $this->page->page_title();
				$data['header'] = $this->header;
				$data['page']   = $this->page->slug();
				$data['nonce']  = \wp_create_nonce( 'pc_settings_page_' . $this->page->slug() );
				$data['fields'] = join( PHP_EOL, $this->parse_fields() );
				$data['footer'] = $this->footer;

				/**
				 * Filters all of the view data used to generate the settings page
				 *
				 * @param array{title:string,header:string,page:string,nonce:string,fields:string,footer:string} $data
				 * @param Page $page
				 * @param Abstract_Settings $settings
				 * @return array{title:string,header:string,page:string,nonce:string,fields:string,footer:string} $data
				 */
				$data = \apply_filters( 'foo', $data, $this->page, $this->settings );

				include $this->view;
			}
		);
	}

	/**
	 * Parses each field to a string representation of the field.
	 *
	 * @return array
	 */
	private function parse_fields(): array {
		return array_map(
			function( Field $field ): string {

				// Set all parameters
				$input           = Element_Factory::from_field( $field );
				$wrapper_classes = $this->render_wrapper_classes( $field );
				$icon            = $this->render_icon( $field );
				$label           = $field->get_label();
				$description     = $this->render_description( $field );

				// Generate the element.
				return <<<HTML
                <div class="$wrapper_classes">
                    <div class="settings-page-field__title">
                        $icon $label
                    </div>
                    <div class="settings-page-field__input">
                        $input
                        $description
                    </div>

                </div>

HTML;
			},
			$this->settings->export()
		);
	}

	/**
	 * Renders the wrapper class for the field.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Setting\Field\Field $field
	 * @return void
	 */
	private function render_wrapper_classes( Field $field ) {
		$classes = apply_filters( Hooks::ELEMENT_WRAPPER_CLASS, Element_Default::WRAPPER_CLASSES, $field );
		return join( ' ', array_merge( $classes, array( $field->get_type() ) ) );
	}

	/**
	 * Renders the fields icon if defined.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Setting\Field\Field $field
	 * @return string
	 */
	private function render_icon( Field $field ): string {
		if ( ! is_null( $field->get_icon() ) ) {
			return \sprintf(
				'<span class="settings-page-field__icon"><img src="%s" alt="%s"></span>',
				esc_url( $field->get_icon() ),
				$field->get_label()
			);
		}
		return '';
	}

	/**
	 * Renders the field description.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Setting\Field\Field $field
	 * @return string
	 */
	private function render_description( Field $field ): string {
		if ( $field->get_description() !== '' ) {
			return sprintf( "<p class='description'>%s</p>", $field->get_description() );
		}
		return '';
	}
}
