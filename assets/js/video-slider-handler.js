class VideoSliderHandlerClass extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				theWidget:    '.video-slider',
				prevArrow:    '.video-slider__arrow--left',
				nextArrow:    '.video-slider__arrow--right',
				videoItems:   '.video-slider__playlist-item',
				displayItems: '.video-slider__display-item',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		return {
			$theWidget:    this.$element.find(selectors.theWidget)[0],
			$prevArrow:    this.$element[0].querySelector(selectors.prevArrow),
			$nextArrow:    this.$element[0].querySelector(selectors.nextArrow),
			$videoItems:   this.$element[0].querySelectorAll(selectors.videoItems),
			$displayItems: this.$element[0].querySelectorAll(selectors.displayItems),
		};
	}

	bindEvents() {
		this.elements.$videoItems.forEach(elm => {
			elm.addEventListener('click', e => {
				const selector = `div[data-index]`;
				const targetIndex = e.target.closest(selector).dataset.index;
				const currentIndex = this.elements.$theWidget.dataset.index;
				this.showDisplayItem(currentIndex, targetIndex);	
			});
		});

		this.elements.$prevArrow.addEventListener('click', () => {
			this.showPrevItem();
		});

		this.elements.$nextArrow.addEventListener('click', () => {
			this.showNextItem();
		});
	}

	showPrevItem() {
		const currentIndex = this.elements.$theWidget.dataset.index;
		const newIndex = currentIndex - 1;
		this.showDisplayItem(currentIndex, newIndex);
	}

	showNextItem() {
		const currentIndex = Number(this.elements.$theWidget.dataset.index) || 0;
		const newIndex = currentIndex + 1;
		this.showDisplayItem(currentIndex, newIndex);
	}

	showDisplayItem(currentIndex, newIndex) {
		const numItems = this.elements.$displayItems.length;
		if (newIndex < 0 || newIndex >= numItems) return;
		if (currentIndex === newIndex) return;

		const currentVisible = this.elements.$displayItems[currentIndex];
		const newVisible = this.elements.$displayItems[newIndex];

		currentVisible.classList.remove('video-slider__display-item--visible');
		currentVisible.classList.add('video-slider__display-item--hidden');
		newVisible.classList.remove('video-slider__display-item--hidden');
		newVisible.classList.add('video-slider__display-item--visible');
		this.elements.$theWidget.dataset.index = newIndex;

		if (this.elements.$videoItems.length > 0) {
			const currentThumbnailBox = this.elements.$videoItems[currentIndex];
			const newThumbnailBox = this.elements.$videoItems[newIndex];
			currentThumbnailBox.classList.remove('video-slider__playlist-item--active');
			newThumbnailBox.classList.add('video-slider__playlist-item--active');
		}

		const prevArrow = this.elements.$prevArrow;
		const nextArrow = this.elements.$nextArrow;

		prevArrow.classList.remove('video-slider__arrow--hidden');
		nextArrow.classList.remove('video-slider__arrow--hidden');

		if (newIndex === 0) {
			prevArrow.classList.add('video-slider__arrow--hidden');
		} else if (newIndex === numItems - 1) {
			nextArrow.classList.add('video-slider__arrow--hidden');
		}
	}

	initEditorListeners() {
		super.initEditorListeners();
		this.editorListeners.push({
			event: 'customVideoSliderWidget:getVideoUrlThumbnail',
			to: elementor.channels.editor,
			callback: (e) => {
				this.setVideoThumbnail(e);
			},
		});

	}

	setVideoThumbnail(e) {
		// e.container.controls.youtube_url;
		const url = e.container.settings.attributes.youtube_url;
		const thumbnailUrl = this.getYoutubeVideoThumbnailByUrl(url, 'max');
		parent.window.$e.run('document/elements/settings', {
			container: e.container,
			settings: {
				thumbnail: {
					url: thumbnailUrl,
				},
			},
			options: {
				external: true
			},
		});

	}

	getYoutubeVideoId(url) {
		const regex = /watch\?v=(.+)$/;
		const id = regex.exec(url);
		if (id) return id[1];
		else return '';
	}

	getYoutubeVideoThumbnailByUrl(url, format) {
		const id = this.getYoutubeVideoId(url);
		if (id) return this.getYoutubeVideoThumbnailById(id, format);
	}

	getYoutubeVideoThumbnailById(id, format) {
		const formats = {
			max: 'maxresdefault',
			standard: 'sddefault',
			medium: 'mddefault',
			high: 'hqdefault',
			'default': 'default',
		};

		if (!formats[format]) format = 'default';
		const url = `http://img.youtube.com/vi/${id}/${formats[format]}.jpg`;
		return url;
	}

	// onInit() { super.onInit(); }
}


jQuery(window).on('elementor/frontend/init', () => {
	elementorFrontend.elementsHandler.attachHandler('video-slider', VideoSliderHandlerClass);
});
