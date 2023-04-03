<?php

declare(strict_types=1);

/**
 * Primary valid page.
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
use PinkCrab\Perique_Admin_Menu\Page\Menu_Page;

class Valid_Primary_Page extends Menu_Page {

	// Helper constants
	public const PAGE_SLUG  = 'valid_primary_page';
	public const MENU_TITLE = 'Valid (Primary)';
	public const PAGE_TITLE = 'Valid Primary Page Title';
	public const POSITION   = 99999;
	public const VIEW_DATA  = array( 'data' => 'Valid Primary Page Data' );

	// Enqueue log.
	public static $enqueue_log = array();

	// Load Log.
	public static $load_log = array();

	/**
	 * The pages menu slug.
	 *
	 * @var string
	 */
	protected string $page_slug = self::PAGE_SLUG;

	/**
	 * The menu title
	 *
	 * @var string
	 */
	protected string $menu_title = self::MENU_TITLE;

	/**
	 * The pages title
	 *
	 * @var string
	 */
	protected string $page_title = self::PAGE_TITLE;

	/**
	 * The pages position, in relation to other pages in group.
	 *
	 * @var string
	 */
	protected ?int $position = self::POSITION;

	/**
	 * The template to be rendered.
	 *
	 * @var string
	 */
	protected string $view_template = __DIR__ . '/view.php';
	// protected string $view_template = '/view.php';

	/**
	 * The view data used by view.
	 *
	 * @var array{data:string}
	 */
	protected array $view_data = self::VIEW_DATA;	

	/**
	 * Callback for enqueuing scripts and styles at a group level.
	 *
	 * @param Page $page
	 * @return void
	 */
	public function enqueue( Page $page ): void {
		self::$enqueue_log[] = $page;
	}

	/**
	 * Callback for loading the page
	 *
	 * @param Page $page
	 * @return void
	 */
	public function load( Page $page ): void {
		self::$load_log[] = $page;

		// Pass data to the view.
		$this->view_data['on_load'] = '--Loaded Primary Page';
	}
}
