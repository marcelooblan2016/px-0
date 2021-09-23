<template>
    <div>
        <transition name="slide-fade" appear>
            <div v-if="showTransition">
                <validation-observer ref="form" v-slot="{ validate, invalid }">
                    <div class="row justify-content-center">
                            <div class="col-md-8 col-offset-2">
                                <ul class="nav nav-tabs text-center py-2">
                                    <li class="nav-item" v-for="row in convertOptions" :key="row.value" v-show="row.disabled == false || row.disabled == null">
                                        <a class="nav-link" :class="{'active': convertType == row.value}" aria-current="page" href="#">{{ row.text }}</a>
                                    </li>
                                </ul>
                                <div class="form-group">
                                    <validation-provider :rules="validationRules" v-slot="{ errors, classes }">
                                        <input id="input-url" class="form-control" :class="classes" ref="url" placeholder="https://www.youtube.com/watch?v=rZ3S_TNinwc" v-model="url" v-on:keyup.enter="validateRequest(validate)"/>
                                        <small class="text-danger">{{ errors.length >= 1 ? errors[0] : urlError }}</small>
                                    </validation-provider>
                                </div>
                            </div>
                            <div class="col-md-8 col-offset-2 p-2">
                                <template v-if="processing == true">
                                    <button type="button" disabled class="btn btn-dark" style="width: 150px;">
                                        Processing
                                        <i class="fas fa-cogs fa-spin"></i>
                                    </button>
                                </template>
                                <template v-else>
                                    <button type="button" :disabled="invalid" class="btn btn-dark" style="width: 150px;" v-on:click.prevent="validateRequest(validate)">
                                        Continue
                                        <i class="fas fa-cogs"></i>
                                    </button>
                                </template>
                            </div>
                    </div>
                </validation-observer>
            </div>
        </transition>
    </div>
</template>
<script>
import transitionEffect from 'mixins/transitionEffect';

export default {
    mixins: [transitionEffect],
    props: {
        convertRequest: {
            required: true,
        }
    },
    data () {
        return {
            url: null,
            urlError: null,
            convertOptions: [
                {"text": "Youtube", "value": "youtube"},
                {"text": "Facebook", "value": "facebook", "disabled": true},
                {"text": "Instagram", "value": "instagram", "disabled": true},
            ],
            convertType: 'youtube',
            processing: false,
        }
    },

    mounted () {
        this.$nextTick( function () {
            $("#input-url").focus();
        });
    },

    computed: {
        validationRules () {
            if (this.convertType == 'youtube') {
                return 'required|url|youtube';
            }

            return 'required';
        }
    },

    methods: {
        validateRequest (validate) {
            validate().then(result => {
                if (! result) return;
                
                this.postConvertRequest();
            });
        },

        postConvertRequest () {
            this.urlError = null;
            this.processing = true;
            let url = this.url;
            let parameters = {
                url: url,
                convert_type: this.convertType
            };

            window.axios.post("/convert", parameters)
            .then(response => {
                this.$emit('update:convert-request', response.data);
            }).catch(error => {
                let errors = error.response.data.errors;
                for( let key in errors) {
                    if (key == 'url') {
                        this.urlError = errors['url'][0];
                    }
                }

            }).finally(() => {
                this.processing = false
            })
        }
    }
}
</script>