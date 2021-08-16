<?php

declare(strict_types=1);

/**
 * A valid settings class
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
 *
 * @docs https://www.advancedcustomfields.com/resources/acf_add_options_page/
 */

namespace PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Settings;

use PinkCrab\Perique_Admin_Menu\Setting\Field\Text;
use PinkCrab\Perique_Admin_Menu\Setting\Field\Field;
use PinkCrab\Perique_Admin_Menu\Setting\Abstract_Settings;
use PinkCrab\Perique_Admin_Menu\Setting\Setting_Collection;

class Valid_Settings extends Abstract_Settings {

	protected function fields( Setting_Collection $settings ): Setting_Collection {
		$settings->push(
			( new Field( 'number', 'number' ) )
				->set_label( 'Field 1' )
				->set_description( 'This is a field of text' )
				->set_attribute( 'placeholder', 'Hello' )
				->set_description('This is a number field')
		);

		$settings->push(
			Text::new( 'text' )
				->set_label('HELLO')
				->set_description('Im a text input but im disabled and read only, so jog on knob head.')
				->set_placeholder( 'FOO' )
				->set_disabled()
				->set_data('foo', 'bar')
				->set_pattern('[az]')
				->set_read_only()
		);
		return $settings;
	}

	protected function is_grouped(): bool {
		return false;
	}

	public function group_key(): string {
		return 'Valid_Settings';
	}
}
