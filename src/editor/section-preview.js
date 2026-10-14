/**
 * Live preview of the section: the same render.php used on the site.
 * While it updates, the previous version stays visible (no flicker), and the content is not
 * clickable: it is edited from the sidebar.
 */

import { useBlockProps } from '@wordpress/block-editor';
import { Disabled, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';

function Loading( { children } ) {
	if ( ! children ) {
		return (
			<div className="ci-editor-preview-status">
				<Spinner />
			</div>
		);
	}
	return <div className="ci-editor-preview-loading">{ children }</div>;
}

function Empty() {
	return (
		<div className="ci-editor-preview-status">
			{ __(
				'This section has no content to show yet. Fill it in from the sidebar.',
				'ci-theme'
			) }
		</div>
	);
}

/**
 * @param {Object}  props
 * @param {string}  props.name            Block name.
 * @param {Object}  props.attributes      Atributos.
 * @param {boolean} props.withPostContext Sends the post ID (blocks of single templates).
 *                                        In the Site Editor (template, text ID) it is not sent
 *                                        and render.php uses a sample entry.
 */
export function SectionPreview( {
	name,
	attributes,
	withPostContext = false,
} ) {
	const blockProps = useBlockProps( { className: 'ci-editor-preview' } );
	const postId = useSelect(
		( select ) =>
			withPostContext
				? select( editorStore )?.getCurrentPostId?.()
				: undefined,
		[ withPostContext ]
	);

	return (
		<div { ...blockProps }>
			<Disabled>
				<ServerSideRender
					block={ name }
					attributes={ attributes }
					httpMethod="POST"
					urlQueryArgs={
						Number.isInteger( postId )
							? { post_id: postId }
							: undefined
					}
					LoadingResponsePlaceholder={ Loading }
					EmptyResponsePlaceholder={ Empty }
				/>
			</Disabled>
		</div>
	);
}
