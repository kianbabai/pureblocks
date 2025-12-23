import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { slidesToShow = 1 } = attributes;
	const blockProps = useBlockProps.save( {
		className: 'pureblocks-carousel',
		'data-slides-to-show': slidesToShow,
	} );

	return (
		<div { ...blockProps }>
			<InnerBlocks.Content />
		</div>
	);
}


