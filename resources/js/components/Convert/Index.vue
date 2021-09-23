<template>
    <div>
        <div class="text-end">
            <p class="text-danger">Breadcrumbs here...</p>
        </div>
        <hr/>
        <template v-if="convertRequest == null">
            <convert-request-initial-form :convertRequest.sync="convertRequest"></convert-request-initial-form>
        </template>
        <template v-else>
            <template v-if="convertRequestItem == null">
                <convert-request-options :convertRequest="convertRequest" :convertRequestItem.sync="convertRequestItem"></convert-request-options>
            </template>
            <template v-else>
                <h1 class="text-danger">DOWNLOAD LINK HERE</h1>
            </template>
        </template>
    </div>
</template>
<script>
import ConvertRequestOptions from 'components/Convert/SubComponents/Options'
import ConvertRequestInitialForm from 'components/Convert/SubComponents/InitialForm'
export default {
    components: {
        ConvertRequestInitialForm,
        ConvertRequestOptions  
    },
    data () {
        return {
            convertRequest: null,
            convertRequestItem: null,
        }
    },

    mounted () {},

    computed: {},

    methods: {
        
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