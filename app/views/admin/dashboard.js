
window.Dashboard = module.exports = {

    data: function () {
        return {
            saving: false,
            sections: [],
            data: window.$data,
            config: window.$config
        };
    },

    created: function () {
        //sections
        var sections = [];
        _.forIn(this.$options.components, function (component, name) {
            var options = component.options || {};
            if (options.section) {
                sections.push(_.extend({name: name, priority: 0}, options.section));
            }
        });
        this.$set('sections', _.sortBy(sections, 'priority'));


    },

    ready: function () {

    },

    methods: {
        load: function () {
            this.$http.get('&p=/drukkerij/edit', {
                drukkerijID: this.data.drukkerijID
            }, function (data) {
                this.$set('drukkerij', data.drukkerij);
                this.$set('afwerkprijzen', _.groupBy(_.sortByOrder(data.afwerkprijzen, 'afwerksoort'), 'afwerksoort'));
                this.$nextTick(function () {
                    UIkit.switcher(this.$$.machineTabs).show(UIkit.$(this.$$.machineTabs).find('li:first'));
                    UIkit.switcher(this.$$.papierprijzenTabs).show(UIkit.$(this.$$.papierprijzenTabs).find('li:first'));
                    UIkit.switcher(this.$$.afwerkprijzenTabs).show(UIkit.$(this.$$.afwerkprijzenTabs).find('li:first'));
                    UIkit.accordion('.uk-accordion', {showfirst: false, toggle: '.bps-accordion-toggle', containers: '.uk-panel'});
                }.bind(this));
            }, function (data) {
                console.warn(data);
            });
        },

        saveDrukkerij: function () {
            this.saving = true;
            this.$http.post('&p=/drukkerij/save', {
                drukkerijData: this.drukkerij
            }, function (data) {
                UIkit.notify(this.$trans('Drukkerij opgeslagen'), 'success');
                this.$set('drukkerij', data.drukkerij);
                this.saving = false;
            }, function (data) {
                console.warn(data);
                this.saving = false;
            });
        },

    },
    components: {
        'dv-settings': require('../../components/ui/settings.vue')
    }

};

UIkit.$(function () {

    new Vue(module.exports).$mount('#devos-dashboard');

});
