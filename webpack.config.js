var path = require("path");
var webpack = require("webpack");
module.exports = {
    entry: {
        "admin-dashboard" : "./app/views/admin/dashboard.js",
        "admin-shipments" : "./app/views/admin/shipments.js",
        "admin-gls-tracking" : "./app/views/admin/gls-tracking.js",
        "dashboard" : "./app/views/dashboard.js",
        "vue": "./app/vue"
    },
    debug: true,
    output: {
        filename: "./admin/assets/js/[name].js"
    },
    resolve: {
        root: [path.join(__dirname, "admin/vendor/assets")],
        alias: {
            "md5$": __dirname + "/admin/vendor/assets/js-md5/js/md5.min.js",
            "vue$": __dirname + "/admin/vendor/assets/vue/dist/vue.js",
            "vue-form": __dirname + "/admin/vendor/assets/vue-form/src/index.js",
            "vue-resource$": __dirname + "/admin/vendor/assets/vue-resource/dist/vue-resource.min.js"
        }
    },
    externals: {
        "UIkit": "UIkit"
    },
    module: {
        loaders: [
            { test: /\.vue$/, loader: "vue" },
            { test: /\.json/, loader: "json" },
            { test: /\.html$/, loader: "html" }
        ]
    },
    plugins: [
        new webpack.ResolverPlugin(
            new webpack.ResolverPlugin.DirectoryDescriptionFilePlugin("bower.json", ["main"])
        )
    ]
};
