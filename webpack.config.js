var config = {
	entry: "./scripts/main.js",
	output: {
		path: __dirname.concat( '/scripts' ),
		filename: "bundle.js"
	},
	module: {
		loaders: [
			{
				test: /\.js/,
				exclude: /node_modules/,
				loader: "babel-loader"
			},
			{
				test: /\.css/,
				loader: "css-loader"
			}
		]
	}
}

module.exports = config;
