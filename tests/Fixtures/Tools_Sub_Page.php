<?php

declare(strict_types=1);

/**
 * Sub page of the tool.php menu.
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

namespace PinkCrab\Perique_Admin_Menu\Tests\Fixtures;

use PinkCrab\Perique_Admin_Menu\Page\Menu_Page;

class Tools_Sub_Page extends Menu_Page {

	// Helper constants
	public const PAGE_SLUG  = 'tools_sub_page';
	public const MENU_TITLE = 'Tools Sub';
	public const PAGE_TITLE = 'Tools Sub Page';
	public const PARENT_SLUG = 'foo_tools';
	public const POSITION   = 1;
	public const VIEW_DATA  = array( 'data' => 'Sub page of Tools' );

	// Enqueue log.
	public static $enqueue_log = array();

	// Load Log.
	public static $load_log = array();

	/**
	 * The pages menu slug.
	 *
	 * @var string
	 */
	protected $page_slug = self::PAGE_SLUG;

    /**
     * Parent slug
     * 
     * @var string
     */
    protected $parent_slug = self::PARENT_SLUG;

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
	 * The template to be rendered.
	 *
	 * @var string
	 */
	protected $view_template = __DIR__ . '/view.php';

	/**
	 * The view data used by view.
	 *
	 * @var array{data:string}
	 */
	protected $view_data = self::VIEW_DATA;
}
