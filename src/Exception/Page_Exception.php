<?php

declare(strict_types=1);

/**
 * Page exceptions
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

namespace PinkCrab\Perique_Admin_Menu\Exception;

use Exception;
use PinkCrab\Perique_Admin_Menu\Page\Page;

class Page_Exception extends Exception {

	/**
	 * Exception for page that is attempting to render template, with no view
	 * defined.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Page\Page $page
	 * @return self
	 * @code 200
	 */
	public static function view_not_set( Page $page ): self {
		return new self(
			sprintf(
				'View must be defined in %s to render template',
				get_class( $page )
			),
			200
		);
	}

	/**
	 * Required property undefined.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Page\Page $page
	 * @return self
	 * @code 201
	 */
	public static function undefined_property( string $property, Page $page ): self {
		return new self(
			sprintf(
				'%s is a required property, not set in %s',
				$property,
				get_class( $page )
			),
			201
		);
	}

	/**
	 * Thrown when attempting to register a page type which is not defined in the
	 * Registrar.
	 *
	 * @param object|null $page
	 * @return self
	 * @code 202
	 */
	public static function invalid_page_type( $page ): self {
		return new self(
			sprintf(
				'%s is not defined in the Registrar and can not be registered.',
				is_object( $page ) ? get_class( $page ) : 'UNKONW TYPE'
			),
			202
		);
	}
}
