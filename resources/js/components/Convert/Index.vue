<template>
    <div>
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb d-flex justify-content-end">
                        <li class="breadcrumb-item" v-for="(crumb, index) in breadCrumbs" :key="index">
                            <template v-if="crumb.active == true">
                                <span v-html="crumb.text"></span>
                            </template>
                            <template v-else>
                                <a class="text-dark" :href="crumb.value" v-html="crumb.text"></a>
                            </template>
                            
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <hr/>
        <template v-if="convertRequest == null">
            <convert-request-initial-form :convertRequest.sync="convertRequest" @historyApiPushState="historyApiPushState"></convert-request-initial-form>
        </template>
        <template v-else>
            <template v-if="convertRequestItem == null">
                <convert-request-options :convertRequest="convertRequest" :convertRequestItem.sync="convertRequestItem" @historyApiPushState="historyApiPushState"></convert-request-options>
            </template>
            <template v-else>
                <convert-request-item-results :convertRequestItem="convertRequestItem"></convert-request-item-results>
            </template>
        </template>
    </div>
</template>
<script>
import ConvertRequestInitialForm from 'components/Convert/SubComponents/InitialForm'
import ConvertRequestOptions from 'components/Convert/SubComponents/Options'
import ConvertRequestItemResults from 'components/Convert/SubComponents/Results'
export default {
    components: {
        ConvertRequestInitialForm,
        ConvertRequestOptions,
        ConvertRequestItemResults
    },

    props: {
        convertRequestRaw: {
            required: false,
            default: null,
        },
        convertRequestItemRaw: {
            required: false,
            default: null,
        }
    },
    
    data () {
        return {
            convertRequest: null,
            convertRequestItem: null,
        }
    },

    mounted () {
        if (this.convertRequestRaw != null) {
            this.convertRequest = this.convertRequestRaw;
        }

        if (this.convertRequestItemRaw != null) {
            this.convertRequestItem = this.convertRequestItemRaw;
        }
    },

    computed: {
        breadCrumbs () {
            let options = [
                {'text': 'Youtube', 'value': '/convert', 'active': true}
            ];
            
            if (this.convertRequestItem != null) {
                options = [
                    {'text': 'Youtube', 'value': '/convert', 'active': false},
                    {'text': 'Download Options', 'value': `/convert/${this.convertRequest.external_id}`, 'active': false},
                    {'text': `${this.convertRequestItem.file_type} &bullet; ${this.convertRequestItem.quality}`, 'value': `/convert/${this.convertRequest.external_id}/${this.convertRequestItem.id}`, 'active': true}
                ];
            }
            else if (this.convertRequest != null) {
                options = [
                    {'text': 'Youtube', 'value': '/convert', 'active': false},
                    {'text': 'Download Options', 'value': `/convert/${this.convertRequest.external_id}`, 'active': true}
                ];
            }

            return options;
        }
    },

    methods: {
        historyApiPushState (urlId) {
            window.history.pushState(null, null, `/${urlId}`);
        }
    }
}
</script>
<style>
    /* Enter and leave animations can use different */
    /* durations and timing functions.              */
    .slide-fade-enter-active {
        transition: all .3s ease;
    }
    .slide-fade-leave-active {
        transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .slide-fade-enter, .slide-fade-leave-to
    /* .slide-fade-leave-active below version 2.1.8 */ {
        transform: translateX(10px);
        opacity: 0;
    }
    
</style>