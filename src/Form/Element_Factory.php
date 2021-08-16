<?php

declare(strict_types=1);

/**
 * Renders form fields.
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

namespace PinkCrab\Perique_Admin_Menu\Form;

use PinkCrab\Form_Fields\Abstract_Field;
use PinkCrab\Form_Fields\Fields\Input_Text;
use PinkCrab\Perique_Admin_Menu\Util\Hooks;
use PinkCrab\Perique_Admin_Menu\Setting\Field\Text;
use PinkCrab\Perique_Admin_Menu\Setting\Field\Field;
use PinkCrab\Perique_Admin_Menu\Form\Element_Default;

class Element_Factory {

	public static function from_field( Field $field ): string {
		$factory = new self();

		$form_element = $factory->create_element( $field );

		// Set all shared attibutes.
		$form_element = $factory->shared_attributes( $field, $form_element );

		// dump( array( $field, $form_element, $form_element->as_string() ) );

		return $form_element->as_string();

	}

	/**
	 * Renders the
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Setting\Field\Field $field
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	public function create_element( Field $field ): Abstract_Field {
		switch ( $field->get_type() ) {
			case 'number':
				return Input_Text::create( $field->get_key() );

			case Text::TYPE:
				return Input_Text::create( $field->get_key() );

			default:
				return '';
		}
	}

	/**
	 * SHARED FIELD HELPERS
	 */

	/**
	 * Sets all shared properties
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Setting\Field\Field $field
	 * @param \PinkCrab\Form_Fields\Abstract_Field $element
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	public function shared_attributes( Field $field, Abstract_Field $element ): Abstract_Field {

		// Maybe add the description.
		$element = $this->maybe_description( $field, $element );

		return $element
			->current( $field->get_value() )
			->class( $this->element_classes( $field ) )
			->disabled( $field->is_disabled() )
			->read_only( $field->is_read_only() );
	}

	/**
	 * Adds the description if defined.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Setting\Field\Field $field
	 * @param \PinkCrab\Form_Fields\Abstract_Field $element
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	private function maybe_description( Field $field, Abstract_Field $element ): Abstract_Field {
		if ( '' !== $field->get_description() ) {
			$element->description( $field->get_description() );
		}
		return $element;
	}

	/**
	 * Generates the
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Setting\Field\Field $field
	 * @return string
	 */
	private function element_classes( Field $field ): string {
		$classes = \array_merge( Element_Default::INPUT_CLASSES, array( $field->get_type() ) );

		/**
		 * Filters the element wrapper classes.
		 *
		 * @param string[] Current wrapper classes.
		 * @param Field The current field being processed.
		 * @return string[] Wrapper classes.
		 */
		$classes = apply_filters( Hooks::ELEMENT_INPUT_CLASS, $classes, $field );

		return join( ' ', $classes );
	}

}
