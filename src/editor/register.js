/**
 * Registers a section block with an empty save, because every section is rendered in PHP
 * (render.php).
 *
 * Attributes are not passed here on purpose: the server definition (block.json plus the common
 * section attributes and the translated defaults added in CI_Theme_Blocks) is the single source
 * of truth, and WordPress merges it into the editor registration.
 */

import { registerBlockType } from '@wordpress/blocks';

export function registerSectionBlock( metadata, settings ) {
	return registerBlockType( metadata, {
		...settings,
		save: () => null,
	} );
}
