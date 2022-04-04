<?php

declare(strict_types=1);

/**
 * Class used to trigger preloaded actions on all pages (and groups.)
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

class Page_Load_Action {


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

	public function __construct( Page $page, ?Abstract_Group $group = null ) {
		$this->page  = $page;
		$this->group = $group;
	}

	/**
	 * The callback method for the class.
	 *
	 * @return void
	 */
	public function __invoke() {
		// Register hooks for the group if part of group
		if ( null !== $this->group ) {
			$this->group->load( $this->group, $this->page );
		}

		$this->page->load( $this->page );
	}
}
