<template>

    <ul class="uk-list bps-selectlist uk-margin-top-remove">
        <li v-repeat="option: options"
            v-class="uk-active: selected(option.value)">
            <div class="uk-panel">
                {{{option.text}}}
                <a v-on="click: toggle(option.value)" class="uk-position-cover"></a>
            </div>
        </li>
    </ul>

</template>


<script>

    module.exports = {

        props: {
            values: Array,
            options: Array
        },

        methods: {
            selected: function (value) {
                return this.values.indexOf(value) > -1;
            },
            toggle: function (value) {
                if (this.selected(value)) {
                    this.values.$remove(value);
                } else {
                    this.values.push(value);
                }
            }
        }

    };

</script>
