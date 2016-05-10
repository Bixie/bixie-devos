<template>

    <div>
        <a v-if="messages.length" class="uk-badge uk-badge-notification" :class="cls"
             :title="label" data-uk-tooltip=""
             v-el:trigger>{{messages.length}}</a>

        <div v-el:modal class="uk-modal">
            <div class="uk-modal-dialog">
                <a class="uk-close uk-modal-close"></a>
                <div>
                    <h3>{{ label }}</h3>
                </div>
                <div class="uk-overflow-container">
                    <ul class="uk-list">
                        <li v-for="message in messages" track-by="$index">{{ message }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


</template>


<script>

    module.exports = {

        props: {
            label: String,
            messages: Array,
            cls: String
        },

        events: {
            'hook:ready': function () {
                if (this.messages.length) {
                    UIkit.modalTrigger(UIkit.$(this.$els.trigger), {target: UIkit.$(this.$els.modal)});
                }
            }
        }

    };

</script>
