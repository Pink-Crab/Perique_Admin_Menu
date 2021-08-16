<?php

declare(strict_types=1);

/**
 * Object (in memory) based setting repository.
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

use PinkCrab\Perique_Admin_Menu\Setting\Setting_Repository;

class Object_Setting_Repository implements Setting_Repository {

	public $settings = array('number'=> 12, 'text'=> 'foo');

	public function set( string $key, $data ): bool {
		$this->settings[ $key ] = $data;
		return array_key_exists( $key, $this->settings );
	}

	public function get( string $key ) {
		return array_key_exists( $key, $this->settings ) 
            ? $this->settings[ $key ] 
            : null;
	}

	public function delete( string $key ): bool {
		unset( $this->settings[ $key ] );
		return array_key_exists( $key, $this->settings );
	}

	public function has( string $key ): bool {
		return array_key_exists( $key, $this->settings );
	}
}
