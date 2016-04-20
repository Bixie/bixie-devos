
var NinjaPrint = function (vm) {

    var intervalId, tries = 0, maxTries = 100;

    this.enabled = false;
    
    this.zpl = function (printerName, zpl) {
        if (!this.enabled || !printerName) return;
        NinjaPrinter.printZpl(printerName, window.btoa(zpl));
    };

    this.pdf = function (printerName, pdf) {
        if (!this.enabled || !printerName) return;
        NinjaPrinter.printPdf(printerName, pdf);
    };

    this.ready = function () {
        if (typeof NinjaPrinter == 'undefined') {
            tries += 1;

            if (tries > maxTries) {
                window.clearInterval(intervalId);
            }

            return;
        }
        window.clearInterval(intervalId);

        this.enabled = true;
        
        vm.$emit('ninjaprint.enabled');

        NinjaPrinter.on('ninjaprinter.result', function (result) {
            vm.$emit('ninjaprint.result', result);
        });
    };
    
    jQuery(function () {
        intervalId = window.setInterval(this.ready.bind(this), 100);
    }.bind(this));

};

module.exports = function (Vue) {

    Vue.prototype.$ninjaPrint = function (vm) {
        return new NinjaPrint(vm);
    };

};


