<?php

declare(strict_types=1);

/**
 * Series of helper functions regarding files and directories.
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

namespace PinkCrab\Perique_Admin_Menu\Util;

class Hooks {

	/**
	 * Prefix for all admin menu hook handles.
	 */
	protected const HOOK_PREFIX = 'pinkcrab/perique-admin-menu/';

	/**
	 * Hook used to set the element label classes.
	 */
	public const ELEMENT_LABEL_CLASS = self::HOOK_PREFIX . 'element-label-class';

	/**
	 * Hook used to set the element input classes.
	 */
	public const ELEMENT_INPUT_CLASS = self::HOOK_PREFIX . 'element-input-class';

	/**
	 * Hook used to set the element wrapper class.
	 */
	public const ELEMENT_WRAPPER_CLASS = self::HOOK_PREFIX . 'element-input-class';


	/**
	 *     ##    DYNAMIC HOOKS    ##
	 */

	/**
	 * Returns the populated action handle for a settings page header
	 * Used to add additional content above the setting page html.
	 *
	 * @param string $key
	 * @return string
	 */
	public static function settings_page_header_action( string $key ): string {
		return \sprintf( '%ssettings-page-%s-header', self::HOOK_PREFIX, $key );
	}

	/**
	 * Returns the populated action handle for a settings page footer
	 * Used to add additional content below the setting page html.
	 *
	 * @param string $key
	 * @return string
	 */
	public static function settings_page_footer_action( string $key ): string {
		return \sprintf( '%ssettings-page-%s-footer', self::HOOK_PREFIX, $key );
	}

	/**
	 * Returns the populated action handle for a settings page view path.
	 *
	 * @param string $key
	 * @return string
	 */
	public static function settings_page_view_path( string $key ): string {
		return \sprintf( '%ssettings-page-%s-view-path', self::HOOK_PREFIX, $key );
	}
}
