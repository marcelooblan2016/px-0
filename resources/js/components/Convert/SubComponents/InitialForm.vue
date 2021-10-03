<template>
    <div>
        <transition name="slide-fade" appear>
            <div v-if="showTransition">
                <validation-observer ref="form" v-slot="{ validate, invalid }">
                    <div class="row justify-content-center">

                            <div class="col-md-8 col-offset-2">
                                <ul class="nav nav-tabs text-center py-2">
                                    <li class="nav-item" v-for="row in convertOptions" :key="row.value" v-show="row.disabled == false || row.disabled == null">
                                        <a class="nav-link text-dark" :class="{'active': convertType == row.value}" aria-current="page" href="#" v-on:click.prevent="setConvertType(row.value)">{{ row.text }}</a>
                                    </li>
                                </ul>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <template v-if="convertType == 'youtube'">
                                                <validation-provider :rules="validationRules" v-slot="{ errors, classes }">
                                                    <input id="input-url" class="form-control" :class="classes" ref="url" placeholder="https://www.youtube.com/watch?v=rZ3S_TNinwc" v-model="url" v-on:keyup.enter="validateRequest(validate)" autocomplete="off" />
                                                    <small class="text-danger">{{ errors.length >= 1 ? errors[0] : urlError }}</small>
                                                </validation-provider>
                                            </template>
                                            <template v-else-if="convertType == 'facebook'">
                                                 <validation-provider :rules="validationRules" v-slot="{ errors, classes }">
                                                    <input id="input-url" class="form-control" :class="classes" ref="url" placeholder="https://www.facebook.com/watch/?v=815660161974823" v-model="url" v-on:keyup.enter="validateRequest(validate)" autocomplete="off" />
                                                    <small class="text-danger">{{ errors.length >= 1 ? errors[0] : urlError }}</small>
                                                </validation-provider>
                                                <div class="alert alert-light mt-2" role="alert">
                                                    <strong>Note:</strong> 
                                                    sure the video is viewable in 
                                                    <i class="fas fa-globe-americas"></i>
                                                    public.
                                                </div>
                                            </template>
                                            <template v-else-if="convertType == 'instagram'">
                                                 <validation-provider :rules="validationRules" v-slot="{ errors, classes }">
                                                    <input id="input-url" class="form-control" :class="classes" ref="url" placeholder="https://www.instagram.com/tv/CUkGmrvg0BR/" v-model="url" v-on:keyup.enter="validateRequest(validate)" autocomplete="off" />
                                                    <small class="text-danger">{{ errors.length >= 1 ? errors[0] : urlError }}</small>
                                                </validation-provider>
                                                <div class="alert alert-light mt-2" role="alert">
                                                    <strong>Note:</strong> 
                                                    sure the video is viewable in 
                                                    <i class="fas fa-globe-americas"></i>
                                                    public.
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-12">
                                        <a href="#">Advanced Options</a>
                                    </div> -->
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
import vSelect from 'vue-select'

export default {
    components: {
        vSelect,
    },
    mixins: [transitionEffect],
    props: {
        requestType: {
            required: false,
            default: null,
        },
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
                {"text": "Facebook", "value": "facebook"},
                {"text": "Instagram", "value": "instagram"},
            ],
            processing: false,
            convertType: 'youtube',
        }
    },

    mounted () {
        this.$nextTick( function () {
            $("#input-url").focus();
        });

        if (this.requestType != null) {
            this.convertType = this.requestType;
        }
    },

    computed: {

        validationRules () {
            if (this.convertType == 'youtube') {
                return 'required|url|youtube';
            }
            else if (this.convertType == 'facebook') {
                return 'required|url|facebook';
            }
            else if (this.convertType == 'instagram') {
                return 'required|url|instagram';
            }

            return 'required';
        }
    },

    methods: {
        setConvertType (type) {
            this.convertType = type;
            this.$emit('convertTypeSet', this.convertType);
        },

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
                this.$emit('historyApiPushState', `convert/` + response.data.external_id);

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