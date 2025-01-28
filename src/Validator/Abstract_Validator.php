<?php

declare(strict_types=1);

/**
 * Abstract Validator
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

namespace PinkCrab\Perique_Admin_Menu\Validator;

abstract class Abstract_Validator {

	/**
	 * Holds any errors encounted when validation a group.
	 *
	 * @var array<string>
	 */
	protected array $errors = array();

	/**
	 * Resets the errors.
	 *
	 * @return void
	 */
	protected function reset_errors(): void {
		$this->errors = array();
	}

	/**
	 * Checks if current group being validated has errors.
	 *
	 * @return bool
	 */
	public function has_errors(): bool {
		return count( $this->errors ) > 0;
	}

	/**
	 * Returns the current errors.
	 *
	 * @return array<string>
	 */
	public function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Pushes an error to the list.
	 *
	 * @param string $error
	 * @return void
	 */
	public function push_error( string $error ): void {
		$this->errors[] = $error;
	}

	/**
	 * Validates some data
	 *
	 * @param mixed $subject
	 * @return bool
	 */
	abstract public function validate( $subject ): bool;
}
