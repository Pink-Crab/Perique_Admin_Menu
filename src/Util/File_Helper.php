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

class File_Helper {

	/**
	 * Gets the URL of a file or directory.
	 *
	 * @param string $file
	 * @return string|null
	 */
	public static function get_file_url( string $file ): ?string {
		// Turn Windows paths to universal.
		$file_path = str_replace( \DIRECTORY_SEPARATOR, '\\', WP_CONTENT_DIR );
		$file_path = str_replace( $file_path, '', $file );

		// Set separator based on os type.
		$file_path = str_replace( '\\', \DIRECTORY_SEPARATOR, $file_path );

		return $file_path ? content_url( $file_path ) : null;
	}
}
