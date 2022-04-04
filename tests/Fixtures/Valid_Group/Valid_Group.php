<?php

declare(strict_types=1);

/**
 * The group of a valid settings group.
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

namespace PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group;

use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique_Admin_Menu\Group\Abstract_Group;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Page;
use PinkCrab\Perique_Admin_Menu\Tests\Fixtures\Valid_Group\Valid_Primary_Page;

class Valid_Group extends Abstract_Group {

	// Constant helpers.
	public const GROUP_TITLE  = 'Valid Page Group';
	public const PRIMARY_PAGE = Valid_Primary_Page::class;
	public const PAGES        = array( Valid_Primary_Page::class, Valid_Page::class );
	public const CAPABILITY   = 'manage_options';
	public const ICON         = 'dashicons-admin-generic';
	public const POSITION     = 65;

	// Enqueue log.
	public static $enqueue_log = array();

	protected $group_title = self::GROUP_TITLE;

	protected $primary_page = self::PRIMARY_PAGE;

	protected $pages = self::PAGES;

	protected $capability = self::CAPABILITY;

	protected $icon = self::ICON;

	protected $position = self::POSITION;

	/**
	 * Callback for enqueuing scripts and styles at a group level.
	 *
	 * @param Abstract_Group $group
	 * @param Page $page
	 * @return void
	 */
	public function enqueue( Abstract_Group $group, Page $page ): void {
		self::$enqueue_log[] = array( $group, $page );
	}
}
