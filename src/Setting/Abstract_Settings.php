<?php

declare(strict_types=1);

/**
 * Abstract settings object.
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

use PinkCrab\Perique_Admin_Menu\Setting\Field\Field;
use PinkCrab\Perique_Admin_Menu\Setting\Setting_Collection;
use PinkCrab\Perique_Admin_Menu\Setting\Setting_Repository;

abstract class Abstract_Settings {

	/**
	 * The settings
	 *
	 * @var Setting_Collection;
	 */
	protected $settings;

	/**
	 * The settings repository.
	 *
	 * @var Setting_Repository
	 */
	protected $settings_repository;

	public function __construct( Setting_Repository $settings_repository ) {
		$this->settings_repository = $settings_repository;
		$this->settings            = $this->fields( new Setting_Collection() );
		$this->set_values();
	}

	/**
	 * Populates the settings group with all fields.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Setting\Setting_Collection $settings
	 * @return \PinkCrab\Perique_Admin_Menu\Setting\Setting_Collection
	 */
	abstract protected function fields( Setting_Collection $settings): Setting_Collection;

	/**
	 * Denotes of the settings is grouped
	 *
	 * @return bool
	 */
	abstract protected function is_grouped(): bool;

	/**
	 * Denotes the group key (can be used a prefix for key, or the key all settings are saved under)
	 *
	 * @return string
	 */
	abstract public function group_key(): string;

	/**
	 * Gets a setting from the repository.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get( string $key ) {
		return $this->settings->has( $key )
			? $this->settings->get( $key )
			: null;
	}

	/**
	 * Sets a setting based on its key and value.
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return bool
	 */
	public function set( string $key, $data ): bool {
		$this->settings->set_value( $key, $data );
		return $this->settings->has( $key );
	}

	/**
	 * Deletes a setting if it exists, returns null if it doesn't.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function delete( string $key ): ?bool {
		if ( $this->settings->has( $key ) ) {
			$this->settings->remove( $key );
			return ! $this->settings->has( $key );
		}
		return null;
	}

	/**
	 * Sets the value of the settings.
	 *
	 * @return void
	 */
	protected function set_values(): void {
		foreach ( $this->settings->get_keys() as $key ) {
			$this->settings->set_value( $key, $this->settings_repository->get( $key ) );
		}
	}

	/**
	 * Returns the settings collection as an array.
	 *
	 * @return Field[]
	 */
	public function export(): array {
		// Update values from repository.
		$this->set_values();
		return $this->settings->to_array();
	}
}
