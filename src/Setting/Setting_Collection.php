<?php

declare(strict_types=1);

/**
 * Settings collection.
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

use PinkCrab\Collection\Collection;
use PinkCrab\Collection\Traits\Indexed;
use PinkCrab\Perique_Admin_Menu\Setting\Field\Field;

class Setting_Collection extends Collection {

	use Indexed;

	/**
	 * Overwrite this method in any extended classes, to modify the initial data.
	 *
	 * @param array<int|string, mixed> $data
	 * @return array<int|string, mixed>
	 */
	protected function map_construct( array $data ): array {
		return array_filter(
			$data,
			function( $e ) {
				return is_a( $e, Field::class );
			}
		);
	}

	/**
	 * Sets the value for a given key.
	 * Does nothing if key doesn't exist.
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return self
	 */
	public function set_value( string $key, $data ): self {
		if ( $this->has( $key ) ) {
			$field = $this->get( $key );
			$field->set_value( $data );
			$this->set( $key, $field );
		}
		return $this;
	}

	/**
	 * Returns an array of all keys.
	 *
	 * @return array
	 */
	public function get_keys(): array {
		return array_map(
			function( Field $field ):string {
				return $field->get_key();
			},
			$this->data
		);
	}

	/**
	 * Allow push to be used for settings by key.
	 *
	 * @param mixed ...$datum
	 * @return self
	 */
	public function push( ...$datum ): self {
		foreach ( $this->map_construct( $datum ) as $data ) {
			$this->set( $data->get_key(), $data );
		}
		return $this;
	}
}
