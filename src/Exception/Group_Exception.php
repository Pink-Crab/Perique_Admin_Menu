<?php

declare(strict_types=1);

/**
 * Group exceptions
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
use PinkCrab\Perique_Admin_Menu\Group\Abstract_Group;
use PinkCrab\Perique_Admin_Menu\Validator\Group_Validator;
use PinkCrab\Perique_Admin_Menu\Validator\Abstract_Validator;

class Group_Exception extends Exception {

	/**
	 * Exception for a group with no primary page defined.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group $group
	 * @return self
	 * @code 250
	 */
	public static function primary_page_undefined( Abstract_Group $group ): self {
		return new self(
			sprintf(
				'The primary page is not defined in %s',
				get_class( $group )
			),
			250
		);
	}

	/**
	 * Exception for a group with no title set
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group $group
	 * @return self
	 * @code 251
	 */
	public static function group_title_undefined( Abstract_Group $group ): self {
		return new self(
			sprintf(
				'The group title is not defined in %s',
				get_class( $group )
			),
			251
		);
	}

	/**
	 * Exception for a group that fails validation
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Validator\Group_Validator $validator
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group $group
	 * @return self
	 * @code 252
	 */
	public static function failed_validation( Group_Validator $validator, Abstract_Group $group ): self {
		return new self(
			sprintf(
				'%s failed Group validation (%s)',
				get_class( $group ),
				join( ',', $validator->get_errors() )
			),
			252
		);
	}
}
