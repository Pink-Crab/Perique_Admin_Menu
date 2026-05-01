<?php

declare(strict_types=1);

/**
 * Registry of page classes admin-menu has discovered inside an Abstract_Group.
 *
 * Serves two consumers:
 *   1. Page_Dispatcher consults the registry for skip checks so a Page listed
 *      in both registration_classes() and a Group's primary_page / $pages is
 *      registered exactly once.
 *   2. Downstream module middlewares subscribe to Hooks::GROUPS_PROCESSED and
 *      read the registry to apply their own DI rules to Group-declared pages
 *      (e.g. Settings_Page wires set_settings() call rules).
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

namespace PinkCrab\Perique_Admin_Menu\Registry;

use PinkCrab\Perique_Admin_Menu\Group\Abstract_Group;

class Group_Page_Registry {

	/**
	 * Page class names mapped to the Group instance that declared them.
	 *
	 * @var array<string, Abstract_Group>
	 */
	private array $entries = array();

	/**
	 * Records a page class as declared by a Group. First-write-wins.
	 *
	 * @param string         $page_class Fully-qualified Page class name.
	 * @param Abstract_Group $group      The group that declared the page.
	 * @return void
	 */
	public function record( string $page_class, Abstract_Group $group ): void {
		if ( '' === $page_class ) {
			return;
		}
		if ( isset( $this->entries[ $page_class ] ) ) {
			return;
		}
		$this->entries[ $page_class ] = $group;
	}

	/**
	 * Returns true if the given page class has been recorded.
	 *
	 * @param string $page_class Fully-qualified Page class name.
	 * @return bool
	 */
	public function has( string $page_class ): bool {
		return isset( $this->entries[ $page_class ] );
	}

	/**
	 * Returns the Group instance that declared the given page class, or null if not recorded.
	 *
	 * @param string $page_class Fully-qualified Page class name.
	 * @return Abstract_Group|null
	 */
	public function group_for( string $page_class ): ?Abstract_Group {
		return $this->entries[ $page_class ] ?? null;
	}

	/**
	 * Returns the full map of recorded page class names to their declaring Group.
	 *
	 * @return array<string, Abstract_Group>
	 */
	public function all(): array {
		return $this->entries;
	}

	/**
	 * Returns the subset of recorded entries whose page class is a subclass of $base_class.
	 *
	 * @param string $base_class A base class or interface name.
	 * @return array<string, Abstract_Group>
	 */
	public function all_for_subclass( string $base_class ): array {
		$out = array();
		foreach ( $this->entries as $page_class => $group ) {
			if ( ! \class_exists( $page_class ) ) {
				continue;
			}
			if ( \is_subclass_of( $page_class, $base_class ) ) {
				$out[ $page_class ] = $group;
			}
		}
		return $out;
	}
}
