/**
 * WordPress dependencies
 */
import { AlignmentToolbar, InspectorControls } from '@wordpress/block-editor';
import { BaseControl, PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { GridInspectorPanel, ImageInspectorPanel, featuredImageSizePresets } from '../../components';

/**
 * Component for block controls that appear in the Inspector Panel.
 */
export default class extends Component {
	/**
	 * Render the controls.
	 *
	 * @return {Element}
	 */
	render() {
		const { attributes, blockData, setAttributes } = this.props;
		const {
			content,
			featured_image_width,
			headingAlign,
			image_align,
			show_category,
			show_images,
			show_meta,
			show_speaker,
			sort,
		} = attributes;
		const { options, schema } = blockData;

		return (
			<InspectorControls>
				<GridInspectorPanel
					attributes={ attributes }
					blockData={ blockData }
					setAttributes={ setAttributes }
				/>

				<ImageInspectorPanel
					title={ __( 'Featured Image Settings', 'wordcamporg' ) }
					show={ show_images }
					onChangeShow={ ( value ) => setAttributes( { show_images: value } ) }
					size={ featured_image_width }
					onChangeSize={ ( value ) => setAttributes( { featured_image_width: value } ) }
					sizeSchema={ schema.featured_image_width }
					sizePresets={ featuredImageSizePresets }
					align={ image_align }
					onChangeAlign={ ( value ) => setAttributes( { image_align: value } ) }
					alignOptions={ options.align_image }
				/>

				<PanelBody title={ __( 'Content Settings', 'wordcamporg' ) } initialOpen={ true }>
					<BaseControl>
						<span className="components-base-control__label">
							{ __( 'Session name alignment', 'wordcamporg' ) }
						</span>
						<AlignmentToolbar
							isCollapsed={ false }
							value={ headingAlign }
							onChange={ ( nextAlign ) => {
								setAttributes( { headingAlign: nextAlign } );
							} }
						/>
					</BaseControl>

					<SelectControl
						label={ __( 'Description', 'wordcamporg' ) }
						value={ content || 'full' }
						options={ options.content }
						onChange={ ( value ) => setAttributes( { content: value } ) }
					/>
					<ToggleControl
						label={ __( 'Details', 'wordcamporg' ) }
						help={ show_meta
							? __( 'Date, time, and track are visible.', 'wordcamporg' )
							: __( 'Date, time, and track are hidden.', 'wordcamporg' ) }
						checked={ show_meta === undefined ? false : show_meta }
						onChange={ ( value ) => setAttributes( { show_meta: value } ) }
					/>
					<ToggleControl
						label={ __( 'Categories', 'wordcamporg' ) }
						help={ show_category
							? __( 'Session categories are visible.', 'wordcamporg' )
							: __( 'Session categories are hidden.', 'wordcamporg' ) }
						checked={ show_category === undefined ? false : show_category }
						onChange={ ( value ) => setAttributes( { show_category: value } ) }
					/>
					<ToggleControl
						label={ __( 'Speakers', 'wordcamporg' ) }
						help={ show_speaker
							? __( 'Session speakers are visible.', 'wordcamporg' )
							: __( 'Session speakers are hidden.', 'wordcamporg' ) }
						checked={ show_speaker === undefined ? false : show_speaker }
						onChange={ ( value ) => setAttributes( { show_speaker: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Sorting', 'wordcamporg' ) }>
					<SelectControl
						label={ __( 'Sort by', 'wordcamporg' ) }
						value={ sort }
						options={ options.sort || 'session_time' }
						onChange={ ( value ) => setAttributes( { sort: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
		);
	}
}
