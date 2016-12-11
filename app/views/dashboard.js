
window.Dashboard = module.exports = {

    el: '#devos-dashboard',

    data: function () {
        return {
            data: window.$data,
            config: window.$config,
            currentView: 'dashboard'
        };
    },

    created: function () {
        UIkit.$win.on('hashchange', function () {
            this.checkView()
        }.bind(this));
        this.checkView();
    },

    ready: function () {
        //this.$nextTick(function () {
        //    UIkit.init(UIkit.$(this.$el));
        //});
    },

    methods: {
        checkView: function () {
            var hash = location.hash.replace('#', '');
            if (hash && hash !== this.currentView) {
                this.setView(hash);
            }
        },
        setView: function (view) {
            if (this.$options.components[view]) {
                this.currentView = view;
                window.history.pushState({}, '', this.config.current + '#' + view)
            } else {
                UIkit.notify('Component niet gevonden', 'danger');
            }
        }
    },

    components: {
        'dashboard': require('../components/pages/dashboard.vue'),
        'verzendingen': require('../components/pages/verzendingen.vue'),
        'verzendingen-sendcloud': require('../components/pages/verzendingen-sendcloud.vue'),
        'addressen': require('../components/pages/addressen.vue'),
        'afzenders': require('../components/pages/afzenders.vue')
    }

};

UIkit.$(function () {

    new Vue(module.exports);

});
