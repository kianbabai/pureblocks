import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
} from '@wordpress/block-editor';
import {
	PanelBody,
	Notice,
	RangeControl,
} from '@wordpress/components';

const ALLOWED_BLOCKS = [ 'core/image', 'core/heading', 'core/paragraph' ];

export default function Edit( { clientId, attributes, setAttributes } ) {
	const { slidesToShow = 1 } = attributes;
	const blockProps = useBlockProps( {
		className: 'pureblocks-carousel',
		'data-slides-to-show': slidesToShow,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Carousel Settings', 'pureblocks' ) }
					initialOpen={ true }
				>
					<RangeControl
						label={ __( 'Slides to Show', 'pureblocks' ) }
						value={ slidesToShow }
						onChange={ ( value ) =>
							setAttributes( { slidesToShow: value } )
						}
						min={ 1 }
						max={ 5 }
						help={ __(
							'Number of slides visible at once.',
							'pureblocks'
						) }
					/>
					<Notice status="info" isDismissible={ false }>
						{ __(
							'Add images or content blocks as slides.',
							'pureblocks'
						) }
					</Notice>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<InnerBlocks
					allowedBlocks={ ALLOWED_BLOCKS }
					template={ [
						[ 'core/image' ],
						[ 'core/image' ],
						[ 'core/image' ],
					] }
					templateLock={ false }
				/>
			</div>
		</>
	);
}


