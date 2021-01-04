/**
 * External Dependencies
 */
import { __ } from '@wordpress/i18n';
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save() {
	return (
		<div className="toc">
			<h2>{ __( 'Series Table of Contents', 'dwr-blocks' ) }</h2>
			<InnerBlocks.Content />
		</div>
	);
}
