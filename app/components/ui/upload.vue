<template>
    <div>
        <div v-el:upload-drop class="uk-placeholder uk-text-center">
            <i class="uk-icon-cloud-upload uk-icon-medium uk-text-muted uk-margin-small-right"></i>
            Upload uw logo door het bestand hier in te slepen of <a style="margin-bottom: 5px;" class="uk-form-file">klik hier
            <input v-el:upload-select type="file"></a>.
        </div>

        <div v-el:progressbar class="uk-progress uk-hidden">
            <div class="uk-progress-bar" style="width: 0%;">...</div>
        </div>
    </div>
</template>

<script>

    module.exports = {

        props: ['model', 'config'],

        data: function () {
            return {
                filelimit: false,
                sizelimit: 3217728 //3Mb
            }
        },

        events: {
            'hook:ready' : function () {
                var vm = this;
                var progressbar = UIkit.$(this.$els.progressbar),
                        bar         = progressbar.find('.uk-progress-bar'),
                        settings    = {

                            action: '/index.php?p=%2Fapi%2Fsender%2Fupload&option=com_bix_devos&api=1', // upload url

                            allow : '*.(jpg|jpeg|png)', // allow only images

                            type : 'json',

                            loadstart: function() {
                                bar.css("width", "0%").text("0%");
                                progressbar.removeClass("uk-hidden");
                            },

                            progress: function(percent) {
                                percent = Math.ceil(percent);
                                bar.css("width", percent+"%").text(percent+"%");
                            },

                            before: function (options, files) {
                                if (vm.sizelimit > 0 && files[0].size > vm.sizelimit) {
                                    UIkit.notify('Bestand is te groot. Maximaal toegestane grootte is 3MB.', {status: 'warning'});
                                    return false;
                                }
                            },

                            beforeSend: function (xhr) {
                                console.log(xhr)
                               xhr.setRequestHeader('X-XSRF-TOKEN', vm.config.csrf);
                            },

                            allcomplete: function(response) {

                                bar.css("width", "100%").text("100%");

                                setTimeout(function(){
                                    progressbar.addClass("uk-hidden");
                                }, 250);

                                if (response.error) {
                                    UIkit.notify(response.error, {status: 'danger'});
                                } else {
                                    vm.model = response.path;
                                }


                            }
                        };

                var select = UIkit.uploadSelect(this.$els.uploadSelect, settings),
                    drop   = UIkit.uploadDrop(this.$els.uploadDrop, settings);
            }
        },

        methods: {
        },

    };

</script>