let mix = require( 'laravel-mix' );

mix.options( {
	processCssUrls: false,
	cssNano: {
		discardComments: {
			removeAll: true,
		},
	},
	manifest: false,
	terser: {
		extractComments: false,
		terserOptions: {
			compress: {
				drop_console: true
			},
			output: {
				comments: false,
			},
		}
	}
} );

// Docs chat
mix.js( [ 'assets-src/js/RatingPetitionPopup.jsx' ], 'assets/dist/RatingPetitionPopup.js' );

// Theme CSS,
mix.sass( 'assets-src/scss/RatingPetitionPopup.scss', 'assets/dist/RatingPetitionPopup.css' );
