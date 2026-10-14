/**
 * Theme ESLint: the default @wordpress/scripts configuration plus one note.
 * The @wordpress/* packages (except icons) and '@ci-theme/editor' are not installed: WordPress
 * provides them at runtime (see webpack.config.js and dependency-extraction-webpack-plugin).
 */
const defaultConfig = require( '@wordpress/scripts/config/eslint.config.cjs' );

module.exports = [
	...defaultConfig,
	{
		rules: {
			'import/no-unresolved': [ 'error', { ignore: [ '^@wordpress/', '^@ci-theme/' ] } ],
			'import/no-extraneous-dependencies': 'off',
		},
	},
];
