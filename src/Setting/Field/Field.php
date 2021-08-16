<?php

declare(strict_types=1);

/**
 * Base field model.
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

namespace PinkCrab\Perique_Admin_Menu\Setting\Field;

class Field {

	/**
	 * The fields key.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * field label
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * The data for this option.
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Is field read only
	 *
	 * @var bool
	 */
	protected $disabled = false;

	/**
	 * Is field readonly.
	 *
	 * @var bool
	 */
	protected $read_only = false;

	/**
	 * The fields icon
	 *
	 * @var string|null
	 */
	protected $icon;

	/**
	 * An array of all field attributes
	 *
	 * @var array<string, mixed>
	 */
	protected $attributes = array();

	/**
	 * An array of all field flags
	 *
	 * @var array<string, mixed>
	 */
	protected $flags = array();


	public function __construct( string $key, string $type ) {
		$this->key  = $key;
		$this->type = $type;
	}

	/**
	 * Get the fields key.
	 *
	 * @return string
	 */
	public function get_key(): string {
		return $this->key;
	}

	/**
	 * Get field type
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Get field label
	 *
	 * @return string
	 */
	public function get_label(): string {
		return $this->label ?? '';
	}

	/**
	 * Set field label
	 *
	 * @param string $label  label variable
	 * @return self
	 */
	public function set_label( string $label ): self {
		$this->label = $label;
		return $this;
	}

	/**
	 * Get the data for this option.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->value ?? '';
	}

	/**
	 * Set the data for this option.
	 *
	 * @param mixed $value  The data for this option.
	 * @return self
	 */
	public function set_value( $value ): self {
		$this->value = $value;
		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description ?? '';
	}

	/**
	 * Set description
	 *
	 * @param string $description  Description
	 *
	 * @return self
	 */
	public function set_description( string $description ): self {
		$this->description = $description;
		return $this;
	}

	/**
	 * Get attributes
	 *
	 * @return array<string, string>
	 */
	public function get_attributes(): array {
		return $this->attributes;
	}

	/**
	 * Set single attribute
	 *
	 * @param string $attribute
	 * @param string $value
	 * @return self
	 */
	public function set_attribute( string $attribute, string $value ): self {
		$this->attributes[ $attribute ] = $value;
		return $this;
	}

		/**
	 * Get flags
	 *
	 * @return array<string, string>
	 */
	public function get_flags(): array {
		return $this->flags;
	}

	/**
	 * Set single flag
	 *
	 * @param string $flag
	 * @param string $value
	 * @return self
	 */
	public function set_flag( string $flag ): self {
		$this->flags[] = $flag;
		return $this;
	}

	/**
	 * Sets if field is is disabled.
	 *
	 * @param bool $disabled
	 * @return self
	 */
	public function set_disabled( bool $disabled = true ):self {
		$this->disabled = $disabled;
		return $this;
	}

	/**
	 * Checks if a disabled exists.
	 *
	 * @return bool
	 */
	public function is_disabled(): bool {
		return $this->disabled;
	}

	/**
	 * Get is field readonly.
	 *
	 * @return bool
	 */
	public function is_read_only(): bool {
		return $this->read_only;
	}

	/**
	 * Set is field readonly.
	 *
	 * @param bool $read_only  Is field readonly.
	 * @return self
	 */
	public function set_read_only( bool $read_only = true ): self {
		$this->read_only = $read_only;
		return $this;
	}

	/**
	 * Get the fields icon
	 *
	 * @return string|null
	 */
	public function get_icon(): ?string {
		return $this->icon;
	}

	/**
	 * Set the fields icon
	 *
	 * @param string|null $icon  The fields icon
	 *
	 * @return self
	 */
	public function set_icon( string $icon ): self {
		$this->icon = $icon;
		return $this;
	}
}
