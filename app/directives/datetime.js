module.exports = {

    _settings: {
        shortDayNames: [
            'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
        ],
        longDayNames: [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        ],
        shortMonthNames: [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ],
        longMonthNames: [
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
        ],
        refreshRate: 60000,
        pastMask: '%mmm %d, %yyyy',
        futureMask: '%mmm %d, %yyyy',
        masks: [
            {
                // within a minute
                distance: 60,
                mask: 'just now'
            },
            {
                // within 2 minutes
                distance: 120,
                mask: '1 minute ago'
            },
            {
                // within 1 hour
                distance: 3600,
                mask: '%xm minutes ago'
            },
            {
                // within 2 hours
                distance: 7200,
                mask: '1 hour ago'
            },
            {
                // within 24 hours
                distance: 86400,
                mask: '%xh hours ago'
            },
            {
                // within 48 hours
                distance: 172800,
                mask: '1 day ago'
            },
            {
                // within 30 days
                distance: 2592000,
                mask: '%xd days ago'
            }
        ]
    },

    update: function (value) {
        var el = jQuery(this.el),
            datetime = this.parse(value);

        el.text(this.datetimeInWords(datetime));
    },

    parse: function (iso8601) {
        var s = jQuery.trim(iso8601);
        s = s.replace(/\.\d\d\d+/,'');
        s = s.replace(/-/,'/').replace(/-/,'/');
        s = s.replace(/T/,' ').replace(/Z/,' UTC');
        s = s.replace(/([\+\-]\d\d)\:?(\d\d)/,' $1$2');

        var datetime = new Date(s);
        return isNaN(datetime) ? null : datetime;
    },

    datetimeInWords: function (datetime) {

        var distanceSeconds = (new Date().getTime() - datetime.getTime()) / 1000;

        if (distanceSeconds < 0) {
            return stringFromMask(datetime, this._settings.futureMask);
        }

        for (var i = 0, length = this._settings.masks.length; i < length; ++i) {
            if (distanceSeconds < this._settings.masks[i].distance) {
                return this.stringFromMask(datetime, this._settings.masks[i].mask);
            }
        }

        return this.stringFromMask(datetime, this._settings.pastMask);
    },

    stringFromMask: function (datetime, mask) {
        var re = /\%(d{1,4}|m{1,4}|yy(?:yy)?|[HhMsTt]{1,2}|x[smhdy]|[S])/g;
        var distanceSeconds = (new Date().getTime() - datetime.getTime()) / 1000;
        var distanceMinutes = distanceSeconds / 60;
        var distanceHours = distanceMinutes / 60;
        var distanceDays = distanceHours / 24;
        var distanceYears = distanceDays / 365;
        var $this = this;

        return mask.replace(re, function(match) {
            switch (match.substr(1, match.length)) {
                case 'd':
                return datetime.getDate();
                case 'dd':
                return pad(datetime.getDate());
                case 'ddd':
                return $this._settings.shortDayNames[datetime.getDay()];
                case 'dddd':
                return $this._settings.longDayNames[datetime.getDay()];
                case 'm':
                return datetime.getMonth() + 1;
                case 'mm':
                return pad(datetime.getMonth() + 1);
                case 'mmm':
                return $this._settings.shortMonthNames[datetime.getMonth()];
                case 'mmmm':
                return $this._settings.longMonthNames[datetime.getMonth()];
                case 'yy':
                return String(datetime.getFullYear()).slice(2);
                case 'yyyy':
                return datetime.getFullYear();
                case 'h':
                return datetime.getHours() % 12 || 12;
                case 'hh':
                return pad(datetime.getHours() % 12 || 12);
                case 'H':
                return datetime.getHours();
                case 'HH':
                return pad(datetime.getHours());
                case 'M':
                return datetime.getMinutes();
                case 'MM':
                return pad(datetime.getMinutes());
                case 's':
                return datetime.getSeconds();
                case 'ss':
                return pad(datetime.getSeconds());
                case 't':
                return datetime.getHours() < 12 ? 'a' : 'p';
                case 'tt':
                return datetime.getHours() < 12 ? 'am' : 'pm';
                case 'T':
                return datetime.getHours() < 12 ? 'A' : 'P';
                case 'TT':
                return datetime.getHours() < 12 ? 'AM' : 'PM';
                case 'S':
                return ['th', 'st', 'nd', 'rd'][datetime.getDate() % 10 > 3 ? 0 : (datetime.getDate() % 100 - datetime.getDate() % 10 != 10) * datetime.getDate() % 10];
                case 'xs':
                return Math.round(distanceSeconds);
                case 'xm':
                return Math.round(distanceMinutes);
                case 'xh':
                return Math.round(distanceHours);
                case 'xd':
                return Math.floor(distanceDays);
                case 'xy':
                return Math.floor(distanceYears);
                default:
                return match;
            }
        });
    },

    pad: function(val, len) {
        val = String(val);
        len = len || 2;
        while (val.length < len) {
          val = '0' + val;
        }
        return val;
    }

};