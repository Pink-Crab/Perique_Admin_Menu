<?php

declare(strict_types=1);

/**
 * Settings page where all values are saved as single option items
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

use PinkCrab\Perique_Admin_Menu\Page\Setting_Page;
use PinkCrab\Perique_Admin_Menu\Setting\Abstract_Settings;

class Settings_Page_Single extends Setting_Page {

	// Helper constants
	public const PARENT_PAGE = 'tools.php';
	public const PAGE_SLUG   = 'settings_single_page';
	public const MENU_TITLE  = 'Settings (Single)';
	public const PAGE_TITLE  = 'Settings Page Title';
	public const POSITION    = 1;

	/**
	 * The pages parent slug.
	 *
	 * @var string
	 */
	protected $parent_slug = self::PARENT_PAGE;

	/**
	 * The pages menu slug.
	 *
	 * @var string
	 */
	protected $page_slug = self::PAGE_SLUG;

	/**
	 * The menu title
	 *
	 * @var string
	 */
	protected $menu_title = self::MENU_TITLE;

	/**
	 * The pages title
	 *
	 * @var string
	 */
	protected $page_title = self::PAGE_TITLE;

	/**
	 * The pages position, in relation to other pages in group.
	 *
	 * @var string
	 */
	protected $position = self::POSITION;

	/**
	 * Returns the class name for the settings.
	 *
	 * @return class-string<Abstract_Settings>
	 */
	public function settings_class_name(): string {
		return Valid_Settings::class;
	}

}
