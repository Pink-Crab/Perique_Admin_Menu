<?php

declare(strict_types=1);

/**
 * Base Page interface
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

namespace PinkCrab\Perique_Admin_Menu\Page;

interface Page {

	/**
	 * @return string|null
	 */
	public function parent_slug(): ?string;
	/**
	 * @return string
	 */
	public function slug(): string;
	/**
	 * @return string
	 */
	public function menu_title(): string;
	/**
	 * @return string|null
	 */
	public function page_title(): ?string;
	/**
	 * @return int|null
	 */
	public function position(): ?int;
	/**
	 * @return string
	 */
	public function capability(): string;
	/**
	 * @return callable
	 */
	public function render_view(): callable;
	/**
	 * @param Page $page
	 * @return void
	 */
	public function enqueue( Page $page ): void;

	/**
	 * @param Page $page
	 * @return void
	 */
	public function load( Page $page ): void;

	/**
	 * @return ?string
	 */
	public function page_hook(): ?string;
}
