var path = require("path");
var webpack = require("webpack");
var assets = __dirname + '/admin/vendor/assets';
module.exports = {
    entry: {
        "admin-dashboard" : "./app/views/admin/dashboard.js",
        "admin-shipments" : "./app/views/admin/shipments.js",
        "admin-shipments-sendcloud" : "./app/views/admin/shipments-sendcloud.js",
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
            // "vue$": assets + "/vue/dist/vue.js",
            "vue-form$": assets + "/vue-form/dist/vue-form.common.js",
            "vue-intl$": assets + "/vue-intl/dist/vue-intl.common.js",
            "vue-resource$": assets + "/vue-resource/dist/vue-resource.common.js",
            "JSONStorage$": assets + "/JSONStorage/storage.js"
        }
    },
    externals: {
        "UIkit": "UIkit"
    },
    module: {
        loaders: [
            { test: /\.vue$/, loader: "vue" },
            { test: /\.json$/, loader: "json" },
            { test: /\.html$/, loader: "vue-html" }
        ]
    },
    plugins: [
        new webpack.ResolverPlugin(
            new webpack.ResolverPlugin.DirectoryDescriptionFilePlugin("bower.json", ["main"])
        )
    ]
};
