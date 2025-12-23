import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import Edit from './edit';
import save from './save';
import 'slick-carousel';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import './style.css';
import './editor.css';

// Frontend init: activate slick if present.
const initFrontEnd = () => {
	if ( typeof window === 'undefined' || ! window.jQuery ) {
		return;
	}

	const $ = window.jQuery;
	$( '.pureblocks-carousel' ).each( function () {
		const $node = $( this );
		if ( $node.hasClass( 'slick-initialized' ) ) {
			return;
		}

		// Wait for images to load before initializing
		const $images = $node.find( 'img' );
		if ( $images.length > 0 ) {
			let imagesLoaded = 0;
			const totalImages = $images.length;

			$images.on( 'load', function () {
				imagesLoaded++;
				if ( imagesLoaded === totalImages ) {
					initSlick( $node );
				}
			} );

			// Fallback: if images are already loaded or fail to load
			$images.each( function () {
				if ( this.complete ) {
					imagesLoaded++;
				}
			} );

			if ( imagesLoaded === totalImages ) {
				initSlick( $node );
			} else {
				// Timeout fallback after 2 seconds
				setTimeout( () => {
					if ( ! $node.hasClass( 'slick-initialized' ) ) {
						initSlick( $node );
					}
				}, 2000 );
			}
		} else {
			// No images, initialize immediately
			initSlick( $node );
		}
	} );
};

const initSlick = ( $node ) => {
	if ( $node.hasClass( 'slick-initialized' ) ) {
		return;
	}

	// Get slidesToShow from data attribute, default to 1
	const slidesToShow = parseInt(
		$node.attr( 'data-slides-to-show' ) || '1',
		10
	);

	$node.slick( {
		dots: true,
		arrows: true,
		adaptiveHeight: true,
		slidesToShow: slidesToShow,
		slidesToScroll: 1,
		infinite: true,
		speed: 300,
		cssEase: 'ease',
	} );
};

// Use window.load to ensure all assets are loaded
if ( document.readyState === 'complete' ) {
	// Already loaded, wait a bit for images
	setTimeout( initFrontEnd, 100 );
} else if ( document.readyState === 'interactive' ) {
	window.addEventListener( 'load', initFrontEnd );
} else {
	document.addEventListener( 'DOMContentLoaded', () => {
		window.addEventListener( 'load', initFrontEnd );
	} );
}

if ( registerBlockType ) {
	registerBlockType( metadata.name, {
		...metadata,
		edit: Edit,
		save,
	} );
}


