<?php

declare(strict_types=1);

/**
 * Admin Menu hooks
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

namespace PinkCrab\Perique_Admin_Menu;

class Hooks {

	/**
	 * The base prefix for all hooks.
	 */
	protected const HOOK_PREFIX = 'pinkcrab/admin_menu/';

	/**
	 * Register other primary pages.
	 */
	public const PAGE_REGISTRAR_PRIMARY = self::HOOK_PREFIX . 'page_registrar_primary';

	/**
	 * Register other sub pages.
	 */
	public const PAGE_REGISTRAR_SUB = self::HOOK_PREFIX . 'page_registrar_sub';

	/**
	 * Hook for action triggered after each in a group is registered
	 */
	public const ENQUEUE_GROUP = self::HOOK_PREFIX . 'enqueue_group';
}
