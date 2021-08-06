<?php

declare(strict_types=1);

/**
 * Validates groups.
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

use PinkCrab\Perique_Admin_Menu\Group\Abstract_Group;
use PinkCrab\Perique_Admin_Menu\Validator\Abstract_Validator;

class Group_Validator extends Abstract_Validator {

	/**
	 * Validates a passed group.
	 *
	 * @param mixed $group
	 * @return bool
	 */
	public function validate( $group ): bool {
		$this->reset_errors();

		if ( ! is_a( $group, Abstract_Group::class ) ) {
			$this->push_error( sprintf( '%s Is not a valid group type.', get_class( $group ) ) );
			return false;
		}

		return $this->check_properties( $group );
	}

	/**
	 * Attempts to get all properties
	 * Catches any exceptions thrown and sets to error log.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group $group
	 * @return bool
	 */
	protected function check_properties( Abstract_Group $group ): bool {
		try {
			$group->get_primary_page();
			$group->get_group_title();
		} catch ( \Throwable $th ) {
			$this->push_error( $th->getMessage() );
			return false;
		}
		return true;
	}
}
