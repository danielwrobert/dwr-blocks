/**
 * External Dependencies
 */
import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit() {
	return (
		<div { ...useBlockProps() }>
			<h2>{ __( 'Series Table of Contents', 'dwr-blocks' ) }</h2>
			<InnerBlocks allowedBlocks={['core/list']} />
		</div>
	);
}
