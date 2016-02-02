<template>

    <div class="uk-modal">
        <div class="uk-modal-dialog" :class="classes">
            <template v-if="opened">
                <slot></slot>
            </template>
        </div>
    </div>

</template>


<script>

    module.exports = {

        data: function () {
            return {
                opened: false,
            };
        },

        props: {
            large: Boolean,
            lightbox: Boolean,
            closed: Function,
            modifier: {type: String, default: ''},
            options: Object
        },

        ready: function () {

            var vm = this;

            this.$appendTo('.uk-noconflict');

            this.modal = UIkit.modal(this.$el, _.extend({modal: false}, this.options));
            this.modal.on('hide.uk.modal', function () {

                vm.opened = false;

                if (vm.closed) {
                    vm.closed();
                }
            });

            this.modal.on('show.uk.modal', function () {

                // catch .uk-overflow-container
                setTimeout(function() {
                    vm.modal.resize();
                }, 250)
            });

        },

        computed: {
            classes: function () {
                var classes = this.modifier.split(' ');
                if (this.large) {
                    classes.push('uk-modal-dialog-large');
                }
                if (this.lightbox) {
                    classes.push('uk-modal-dialog-lightbox');
                }
                return classes;
            }
        },

        methods: {

            open: function (data) {
                this.opened = true;
                this.modal.show();
            },

            close: function () {
                this.modal.hide();
            }

        }

    };

</script>
