<?php

declare(strict_types=1);

/**
 * Class used to enqueue all the assets for a page
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

namespace PinkCrab\Perique_Admin_Menu\Registrar;

use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique_Admin_Menu\Group\Abstract_Group;

class Page_Enqueue {

	/**
	 * The hook being enqueued
	 *
	 * @var string
	 */
	protected $hook;

	/**
	 * The current page being enqueued
	 *
	 * @var Page
	 */
	protected $page;

	/**
	 * The option group being enqueued
	 *
	 * @var Abstract_Group|null
	 */
	protected $group;

	public function __construct( string $hook, Page $page, ?Abstract_Group $group = null ) {
		$this->hook  = $hook;
		$this->page  = $page;
		$this->group = $group;
	}

	/**
	 * The callback method for the class.
	 *
	 * @param string $page_hook
	 * @return void
	 */
	public function __invoke( string $page_hook ) {
		if ( $page_hook === $this->hook ) {

			// Register hooks for the group if part of group
			if ( null !== $this->group ) {
				$this->group->enqueue( $this->group, $this->page );
			}

			$this->page->enqueue( $this->page );
		}
	}
}
