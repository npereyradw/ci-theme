/**
 * Editor: ci/news.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import {
	IntroPanel,
	LinkField,
	SectionPreview,
	SectionStyle,
	TextField,
} from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const listing = attributes.layout === 'listing';
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );
	const categories = useSelect(
		( select ) =>
			listing
				? null
				: select( coreStore ).getEntityRecords(
						'taxonomy',
						'category',
						{
							per_page: -1,
							_fields: 'id,slug,name',
						}
					),
		[ listing ]
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'ci-theme' ) }>
					<SelectControl
						label={ __( 'Layout', 'ci-theme' ) }
						value={ attributes.layout }
						options={ [
							{
								value: 'cards',
								label: __(
									'Section with the latest posts',
									'ci-theme'
								),
							},
							{
								value: 'listing',
								label: __(
									'Full listing (posts page)',
									'ci-theme'
								),
							},
						] }
						onChange={ set( 'layout' ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>
				{ listing ? (
					<PanelBody title={ __( 'Listing', 'ci-theme' ) }>
						<div className="ci-editor-stack">
							<p className="ci-editor-help">
								{ __(
									'Shows every published post; the first one of each page is featured. Posts per page are set in Settings → Reading.',
									'ci-theme'
								) }
							</p>
							<TextField
								label={ __( 'Featured badge', 'ci-theme' ) }
								value={ attributes.featuredBadge }
								onChange={ set( 'featuredBadge' ) }
							/>
							<TextField
								label={ __( 'Featured post link', 'ci-theme' ) }
								value={ attributes.readMore }
								onChange={ set( 'readMore' ) }
							/>
							<TextField
								label={ __(
									'Filter name (screen readers)',
									'ci-theme'
								) }
								value={ attributes.filterLabel }
								onChange={ set( 'filterLabel' ) }
							/>
							<TextField
								label={ __(
									'All categories button',
									'ci-theme'
								) }
								value={ attributes.allLabel }
								onChange={ set( 'allLabel' ) }
							/>
							<TextField
								label={ __( 'Next page button', 'ci-theme' ) }
								value={ attributes.nextLabel }
								onChange={ set( 'nextLabel' ) }
							/>
							<TextField
								label={ __( 'No posts: text', 'ci-theme' ) }
								value={ attributes.emptyTitle }
								onChange={ set( 'emptyTitle' ) }
								multiline
								rows={ 2 }
							/>
							<TextField
								label={ __(
									'No posts: button (empty = no button)',
									'ci-theme'
								) }
								value={ attributes.emptyButtonLabel }
								onChange={ set( 'emptyButtonLabel' ) }
							/>
							<LinkField
								label={ __(
									'No posts: button destination',
									'ci-theme'
								) }
								url={ attributes.emptyButtonUrl }
								showNewTab={ false }
								onChange={ ( { url } ) =>
									setAttributes( { emptyButtonUrl: url } )
								}
							/>
						</div>
					</PanelBody>
				) : (
					<>
						<IntroPanel
							attributes={ attributes }
							setAttributes={ setAttributes }
							link
						/>
						<PanelBody title={ __( 'Posts to show', 'ci-theme' ) }>
							<div className="ci-editor-stack">
								<SelectControl
									label={ __( 'Category', 'ci-theme' ) }
									value={ attributes.category }
									options={ [
										{
											value: '',
											label: __( 'All', 'ci-theme' ),
										},
										...( categories || [] ).map(
											( c ) => ( {
												value: c.slug,
												label: c.name,
											} )
										),
									] }
									onChange={ set( 'category' ) }
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
								<RangeControl
									label={ __(
										'Number of posts',
										'ci-theme'
									) }
									value={ attributes.limit }
									min={ 1 }
									max={ 6 }
									onChange={ ( limit ) =>
										setAttributes( { limit: limit || 3 } )
									}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
								<p className="ci-editor-help">
									{ __(
										'The section is hidden while there are no published posts.',
										'ci-theme'
									) }
								</p>
							</div>
						</PanelBody>
					</>
				) }
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
