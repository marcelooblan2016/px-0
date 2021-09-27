<template>
    <div>
        <transition name="slide-fade" appear>
            <div v-if="showTransition">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <h1>
                            {{ cTitle }}
                            <div>
                                {{ cDuration }} &bullet; {{ cFileSize }}
                            </div>
                            <br/>
                            <div class="text-center">
                                <template v-if="convertRequestItemStatus == 0">
                                    <h4 class="text-muted py-2">
                                        <i class="fas fa-hourglass-half fa-spin"></i>
                                        Please wait...
                                    </h4>
                                </template>
                                <template v-else-if="convertRequestItemStatus == 2 || convertRequestItemStatus == 1">
                                    <form :id="formId" method="POST" :action="downloadUrl">
                                        <input type="hidden" name="_token" :value="tokenValue" />
                                        <h4>
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-success btn-block" v-on:click.prevent="downloadNow">
                                                    <i class="fas fa-download"></i>
                                                    Download Now
                                                </button>
                                            </div>
                                        </h4>
                                    </form>
                                </template>
                                <template v-else>
                                    <h5>
                                        <div class="alert alert-light text-danger" role="alert">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            An error occurred, please try again or come back later.
                                        </div>
                                    </h5>
                                </template>
                            </div>
                        </h1>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
<script>
import transitionEffect from 'mixins/transitionEffect';
export default {
    mixins: [transitionEffect],
    props: {
        convertRequestItem: {
            type: [Object, Array]
        }
    },

    data () {

        return {
            convertRequestItemStatus: 0,
            processing: false,
            downloadUrl: null,
            tokenValue: null,

        };
    },

    mounted () {
        this.checkAvailability();
    },

    computed: {
        cTitle () {

            return _.get(this.convertRequestItem, 'details.title');
        },
        
        cDuration () {

            return _.get(this.convertRequestItem, 'details.mapped_details.duration_formatted');
        },

        cFileSize () {
            
            return _.get(this.convertRequestItem, 'details.download_details.size_plus');
        },

        formId () {

            return [
                "form",
                this.convertRequestItem.file_id,
                this.convertRequestItem.file_type
            ].join("-");
        }
    },

    methods: {
        checkAvailability () {
            this.processing = true;
            let parameters = {};
            let fileId = this.convertRequestItem.file_id;

            window.axios.get(`/download/${fileId}/check`, parameters)
            .then(response => {
                let convertRequestItem = response.data;
                this.convertRequestItemStatus = convertRequestItem.status;

                if (this.convertRequestItemStatus == 0) {
                    let vueParent = this;
                    setTimeout(function(){
                        vueParent.checkAvailability();
                    }, 5000);
                }
                else if (this.convertRequestItemStatus == 2 || this.convertRequestItemStatus == 1) {
                    this.downloadUrl = convertRequestItem.url;
                    this.$nextTick( () => ( this.downloadNow () ));
                }
                else {}

            }).catch(error => {
                console.log(error);

            }).finally(() => {
                this.processing = false
            })
        },
        
        downloadNow () {
            if (this.downloadUrl == null) return false;
            let token = document.head.querySelector('meta[name="csrf-token"]');
            this.tokenValue = token.content;

            this.$nextTick( function () {
                $("#" + this.formId).submit();
            });
        }
    }
}
</script>