<template>
    <div>
        <transition name="slide-fade" appear>
            <div v-if="showTransition">
                <div class="row">
                    <div class="col-md-8">
                        <img class="img-fluid rounded" :src="convertRequestThumbnail" />

                        <div class="download-option py-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Video &bullet; Mp4</h5>
                                    <div class="card mb-1">
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <a href="#" class="btn btn-success">
                                                    <strong>1080p</strong>
                                                    Download (100 mb)
                                                </a>

                                                <a href="#" class="btn btn-success">
                                                    <strong>720p</strong>
                                                    Download (80 mb)
                                                </a>

                                                <a href="#" class="btn btn-success">
                                                    <strong>480p</strong>
                                                    Download (60 mb)
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Audio &bullet; Mp3</h5>
                                    <div class="card mb-1">
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <a href="#" class="btn btn-success">Download (30 mb)</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="py-2">
                            <h4 class="text-start">
                                {{ convertRequestTitle }}
                                -
                                <span class="text-muted">{{ convertRequestDuration }}</span>
                            </h4>
                        </div>
                        <hr/>
                        <div class="text-start" v-html="convertRequestDescription"></div>
                        <div class="mb-2">
                            <hr/>
                            <a type="button" class="btn btn-light btn-sm border border-1" v-on:click="seeMore">
                                <template v-if="isSeeMore == false">
                                    See More
                                </template>
                                <template v-else>
                                    See Less
                                </template>
                            </a>
                        </div>
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
            required: true,
        },
        convertRequest: {
            required: true,
        }
    },
    data () {
        return {
            isSeeMore: false,
        }
    },

    mounted () {

    },

    computed: {
        convertRequestTitle () {

            return _.get(this.convertRequest, 'mapped_details.title')
        },

        convertRequestDuration () {

            return _.get(this.convertRequest, 'mapped_details.duration_formatted')
        },

        convertRequestDescription () {

            let description = _.get(this.convertRequest, 'mapped_details.description');
            if (this.isSeeMore == false) {
                description = description.substring(0, 200) + "...";
            }
            
            return description;
        },

        convertRequestThumbnail () {

            return _.get(this.convertRequest, 'mapped_details.thumbnail')
        }
    },

    methods: {
        seeMore () {
            this.isSeeMore = this.isSeeMore == false ? true : false;
        }
    }
}
</script>