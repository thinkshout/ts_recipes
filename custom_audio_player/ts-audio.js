// Create a new Audio object for each song and attach is as a property of the element
			// Also add accessibility elements
			Array.prototype.forEach.call(document.querySelectorAll('[data-audio-clip]'), function (audioClip) {

				// Create a new Audio object for the audio clip.
				audioClip.audio = new Audio(audioClip.href);

				// Add a11y attributes
				audioClip.setAttribute('role', 'button');

				// Default `aria-pressed` to false on page load
				audioClip.setAttribute('aria-pressed', 'false');

				// Reset the styles to the play button once track is complete
				audioClip.audio.addEventListener('ended', function () {
					audioClip.setAttribute('aria-pressed', 'false');
				});

				audioClip.addEventListener('click', function (event) {

					// Prevent link default
					event.preventDefault();

					// If the item is already playing, hit pause
					if (event.target.getAttribute('aria-pressed') === 'true') {
						event.target.audio.pause();
						event.target.setAttribute('aria-pressed', 'false');
						return;
					}

					// Play the audio
					event.target.audio.play();
					event.target.setAttribute('aria-pressed', 'true');

				}, false);
			});
