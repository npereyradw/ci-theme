/**
 * Theme build.
 *
 * - Blocks: every folder of src/blocks with a block.json (auto-detected by @wordpress/scripts).
 * - src/editor: shared editor library (panels, repeater, preview). Exposed as
 *   window.ciThemeEditor with the handle "ci-theme-editor"; blocks import it as
 *   '@ci-theme/editor' and get it as a dependency instead of bundling it.
 * - src/gsap: GSAP and its plugins, a single copy (handle "ci-theme-gsap", window.ciThemeGsap).
 *   Other scripts import 'gsap' and 'gsap/<Plugin>' and depend on that handle.
 * - src/motion: section animations, handle "ci-theme-motion".
 */

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

const EDITOR_REQUEST = '@ci-theme/editor';
const GSAP_REQUESTS = [ 'gsap', 'gsap/DrawSVGPlugin', 'gsap/ScrollTrigger', 'gsap/SplitText' ];

const toExternal = ( request ) => {
	if ( request === EDITOR_REQUEST ) {
		return 'ciThemeEditor';
	}
	if ( GSAP_REQUESTS.includes( request ) ) {
		return 'ciThemeGsap';
	}
	return undefined;
};

const toHandle = ( request ) => {
	if ( request === EDITOR_REQUEST ) {
		return 'ci-theme-editor';
	}
	if ( GSAP_REQUESTS.includes( request ) ) {
		return 'ci-theme-gsap';
	}
	return undefined;
};

module.exports = {
	...defaultConfig,
	entry: () => ( {
		...( typeof defaultConfig.entry === 'function' ? defaultConfig.entry() : defaultConfig.entry ),
		'editor/index': './src/editor/index.js',
		'gsap/index': './src/gsap/index.js',
		'motion/index': './src/motion/index.js',
	} ),
	plugins: [
		...defaultConfig.plugins.filter( ( plugin ) => plugin.constructor.name !== 'DependencyExtractionWebpackPlugin' ),
		new DependencyExtractionWebpackPlugin( {
			requestToExternal: toExternal,
			requestToHandle: toHandle,
		} ),
	],
};
