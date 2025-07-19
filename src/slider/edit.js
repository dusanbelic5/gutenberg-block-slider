import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { ComboboxControl, Notice, ToggleControl, SelectControl, PanelBody, TabPanel } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useEffect } from '@wordpress/element';
import { v4 as uuidv4 } from 'uuid';

export default function Edit({ attributes, setAttributes }) {
	const { uniqueId, selectedPosts, arrowShow, backgroundColor, textColor, arrowColor } = attributes;
	const [search, setSearch] = useState('');

	const blockProps = useBlockProps({ id: uniqueId });

	// generate uniqueId once
	useEffect(() => {
		if (!uniqueId) {
			setAttributes({ uniqueId: uuidv4() });
		}
	}, [uniqueId]);


	// Query posts
	const posts = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'post', {
			per_page: 20,
			search,
		});
	}, [search]);

	// Load titles of selected posts
	const selectedTitles = useSelect((select) => {
		if (!selectedPosts.length) return [];
		return select('core').getEntityRecords('postType', 'post', {
			include: selectedPosts,
			per_page: selectedPosts.length,
		});
	}, [selectedPosts]);

	const handleAddPost = (postId) => {
		if (selectedPosts.includes(postId) || selectedPosts.length >= 3) return;
		setAttributes({ selectedPosts: [...selectedPosts, postId] });
	};

	const handleRemovePost = (postId) => {
		setAttributes({
			selectedPosts: selectedPosts.filter((id) => id !== postId),
		});
	};

	const handleBackgroundColorChange = (value) => {
		setAttributes({ backgroundColor: value });
	};

	const handleTextColorChange = (value) => {
		setAttributes({ textColor: value });
	};

    const handleArrowColorChange = (value) => {
		setAttributes({ arrowColor: value });
	};

	if (!posts) {
		return <p {...blockProps}>{__('Loading posts…', 'textdomain')}</p>;
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Slider Settings', 'textdomain')}>
                    Test here
				</PanelBody>

                <TabPanel
                    className="my-tabs"
                    activeClass="is-active"
                    tabs={[
                        { name: 'general', title: 'General', className: 'tab-general' },
                        { name: 'style', title: 'Style', className: 'tab-style' },
                    ]}
                >
                    {( tab ) => {
                        switch (tab.name) {
                            case 'general':
                                return (
                                    <PanelBody title="General Settings">
                                        <ToggleControl
                                            label={__('Show arrows', 'textdomain')}
                                            checked={arrowShow}
                                            onChange={() => setAttributes({ arrowShow: !arrowShow })}
                                        />
                                    </PanelBody>
                                );
                            case 'style':
                                return (
                                    <PanelBody title="Style Settings">
                                    <SelectControl
                                                            label={__('Background color', 'textdomain')}
                                                            value={backgroundColor || 'sand'}
                                                            onChange={handleBackgroundColorChange}
                                                            options={[
                                                                { label: 'Gold', value: 'gold' },
                                                                { label: 'Gray', value: 'gray' },
                                                                { label: 'Sand', value: 'sand' },
                                                                { label: 'Dark', value: 'dark' },
                                                            ]}
                                    />

                                    <SelectControl
                                                            label={__('Text color', 'textdomain')}
                                                            value={textColor || 'dark'}
                                                            onChange={handleTextColorChange}
                                                            options={[
                                                                { label: 'Gold', value: 'gold' },
                                                                { label: 'Gray', value: 'gray' },
                                                                { label: 'Sand', value: 'sand' },
                                                                { label: 'Dark', value: 'dark' },
                                                            ]}
                                     />


                                    <SelectControl
                                                            label={__('Arrow color', 'textdomain')}
                                                            value={arrowColor || 'dark'}
                                                            onChange={handleArrowColorChange}
                                                            options={[
                                                                { label: 'Gold', value: 'gold' },
                                                                { label: 'Gray', value: 'gray' },
                                                                { label: 'Sand', value: 'sand' },
                                                                { label: 'Dark', value: 'dark' },
                                                            ]}
                                     />
                                    </PanelBody>
                                );
                            default:
                                return null;
                        }
                    }}
                </TabPanel>
			</InspectorControls>

			<div {...blockProps}>
				<h3>{__('Select up to 3 posts for slider:', 'textdomain')}</h3>

				{selectedPosts.length >= 3 && (
					<Notice status="warning" isDismissible={false}>
						{__('You can only select up to 3 posts.', 'textdomain')}
					</Notice>
				)}

				<ComboboxControl
					label={__('Search posts', 'textdomain')}
					value=""
					options={posts.map((post) => ({
						label: post.title.rendered,
						value: post.id,
					}))}
					onInputChange={(val) => setSearch(val)}
					onChange={(val) => handleAddPost(val)}
					disabled={selectedPosts.length >= 3}
				/>

				<div>
					<h4>{__('Selected posts:', 'textdomain')}</h4>
					<ul>
						{selectedTitles &&
							selectedTitles.map((post) => (
								<li key={post.id}>
									{post.title.rendered}
									<button
										onClick={() => handleRemovePost(post.id)}
										style={{ marginLeft: '1rem' }}
									>
										{__('Remove', 'textdomain')}
									</button>
								</li>
							))}
					</ul>
				</div>
			</div>
		</>
	);
}
