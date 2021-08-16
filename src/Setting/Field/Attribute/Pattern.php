<?php

declare(strict_types=1);

/**
 * Pattern attribute
 *
 * The pattern attribute is an attribute of the text, tel, email, url, password, and search input types.
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

namespace PinkCrab\Perique_Admin_Menu\Setting\Field\Attribute;

trait Pattern {

	/**
	 * Sets the pattern for this input.
	 *
	 * @param string $pattern
	 * @return self
	 */
	public function set_pattern( string $pattern ):self {
		$this->set_attribute( 'pattern', $pattern );
		return $this;
	}

	/**
	 * Checks if a pattern exists.
	 *
	 * @return bool
	 */
	public function has_pattern(): bool {
		return \array_key_exists( 'pattern', $this->get_attributes() );
	}

	/**
	 * Gets the pattern if set.
	 *
	 * @return string|null
	 */
	public function get_pattern(): ?string {
		return $this->has_pattern()
			? $this->get_attributes()['pattern']
			: null;
	}
}
