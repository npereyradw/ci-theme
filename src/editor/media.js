/**
 * Image from the Media Library: choose, replace and remove. The ID is stored (render.php builds
 * the srcset from the sizes WordPress generated).
 */

import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { BaseControl, Button, Flex } from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

export function ImageField( { label, help, value, onChange } ) {
	const id = useInstanceId( ImageField, 'ci-image-field' );
	const media = useSelect(
		( select ) =>
			value
				? select( coreStore ).getMedia( value, { context: 'view' } )
				: null,
		[ value ]
	);
	const thumb =
		media?.media_details?.sizes?.medium?.source_url || media?.source_url;

	return (
		<BaseControl
			id={ id }
			label={ label }
			help={ help }
			__nextHasNoMarginBottom
		>
			<MediaUploadCheck>
				<MediaUpload
					allowedTypes={ [ 'image' ] }
					value={ value }
					onSelect={ ( m ) => onChange( m.id ) }
					render={ ( { open } ) => (
						<div className="ci-editor-stack">
							{ thumb && (
								<button
									type="button"
									className="ci-image-field-preview"
									onClick={ open }
								>
									<img
										src={ thumb }
										alt={ media?.alt_text || '' }
									/>
								</button>
							) }
							<Flex justify="flex-start" gap={ 2 }>
								<Button
									variant="secondary"
									onClick={ open }
									__next40pxDefaultSize
								>
									{ value
										? __( 'Replace image', 'ci-theme' )
										: __( 'Choose image', 'ci-theme' ) }
								</Button>
								{ !! value && (
									<Button
										variant="tertiary"
										isDestructive
										onClick={ () => onChange( 0 ) }
										__next40pxDefaultSize
									>
										{ __( 'Remove', 'ci-theme' ) }
									</Button>
								) }
							</Flex>
						</div>
					) }
				/>
			</MediaUploadCheck>
		</BaseControl>
	);
}
