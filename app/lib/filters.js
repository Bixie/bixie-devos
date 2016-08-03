module.exports = function (Vue) {

    Vue.filter('nrquotes', function (quoted_string) {
        return String(quoted_string || '').replace(new RegExp('"', 'g'), '');
    });

    Vue.filter('date', function (date_string, format) {
        var formatted;
        try {
            formatted = new Date(date_string.replace(' ', 'T')).toLocaleDateString();
        } catch (e) {
            formatted = date_string
        }
        return formatted;
    });

    Vue.filter('datetime', function (date_string, format) {
        var formatted, date;
        try {
            date = new Date(date_string.replace(' ', 'T'));
            formatted = date.toLocaleDateString() + ' ' +  date.toLocaleTimeString();
        } catch (e) {
            formatted = date_string
        }
        return formatted;
    });

};
