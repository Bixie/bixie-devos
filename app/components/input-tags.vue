<template>

    <div>
        <ul v-if="style == 'list'" class="uk-list uk-list-line">
            <li v-for="tag in tags">
                <a class="uk-float-right uk-close" @click.prevent="removeTag(tag)"></a>
                {{ tag }}
            </li>
        </ul>
        <div v-else class="uk-flex uk-flex-wrap" data-uk-margin="">
            <div v-for="tag in tags" class="uk-badge uk-margin-small-right">
                <a class="uk-float-right uk-close" @click.prevent="removeTag(tag)"></a>
                {{ tag }}
            </div>
        </div>

        <div class="uk-flex uk-flex-middle uk-margin">
            <div v-show="existing.length">
                <div class="uk-position-relative" data-uk-dropdown="">
                    <button type="button" class="uk-button uk-button-small">{{ 'Bestaande' }}</button>

                    <div class="uk-dropdown uk-dropdown-small">
                        <ul class="uk-nav uk-nav-dropdown">
                            <li v-for="tag in existing"><a @click.prevent="addTag(tag)">{{ tag }}</a></li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="uk-flex-item-1 uk-margin-small-left">
                <div class="uk-form-password">
                    <input type="text" class="uk-width-1-1" v-model="newtag">
                    <a class="uk-form-password-toggle" @click.prevent="addTag()"><i
                            class="uk-icon-check uk-icon-hover"></i></a>
                </div>
            </div>

        </div>
    </div>


</template>

<script>

    module.exports = {

        props: {
            'tags': Array,
            'existing': Array,
            'style': {type: String, default: 'tags'}
        },

        data: function () {
            return {
                'newtag': ''
            };
        },

        methods: {

            addTag: function(tag) {
                this.tags.push(tag || this.newtag);
                this.$nextTick(function () {
                    UIkit.$html.trigger('resize'); //todo why no check.display or changed.dom???
                });
                this.newtag = '';
            },

            removeTag: function(tag) {
                this.tags.$remove(tag)
            }

        }

    };

</script>
