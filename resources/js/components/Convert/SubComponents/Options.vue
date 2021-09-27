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
                                                <template v-for="rowVideo in availableDownloadOptionsVideo">
                                                    <a href="#" class="btn btn-success" v-on:click.prevent="convertItem(rowVideo.id)">
                                                        <strong>{{ rowVideo.quality }}</strong>
                                                        &bullet;
                                                        <strong v-show="rowVideo.quality_term != null" class="ucwords">
                                                            {{ rowVideo.quality_term }} quality
                                                        </strong>

                                                        Download ({{ rowVideo.size_plus }})
                                                    </a>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Audio &bullet; Mp3</h5>
                                    <div class="card mb-1">
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <template v-for="rowAudio in availableDownloadOptionsAudio" v-on:click.prevent="convertItem(rowAudio.id)">
                                                    <a href="#" class="btn btn-success" v-on:click.prevent="convertItem(rowAudio.id)">
                                                        <strong class="ucwords">
                                                            Good Quality
                                                        </strong>
                                                        Download ({{ rowAudio.size_plus }})
                                                    </a>
                                                </template>
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
                        <div class="mb-2" v-show="convertRequestDescription != null && convertRequestDescription.length > descriptionMaxLen">
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
            descriptionMaxLen: 200,
            processing: false,
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
                let maxLen = this.descriptionMaxLen;
                if (description.length >= maxLen) {
                    description = description.substring(0, maxLen) + "...";
                }
            }
            
            return description;
        },

        convertRequestThumbnail () {

            return _.get(this.convertRequest, 'mapped_details.thumbnail')
        },

        availableDownloadOptions () {
            
            return _.get(this.convertRequest, 'details.available_download_options');
        },

        availableDownloadOptionsAudio () {
            let availableDownloadOptions = this.availableDownloadOptions;
            if (availableDownloadOptions == null) return false;

            return availableDownloadOptions.filter ( (row) => row.type == 'audio');
        },

        availableDownloadOptionsVideo () {
            let availableDownloadOptions = this.availableDownloadOptions;
            if (availableDownloadOptions == null) return false;

            let validQualities = [
                '480p',
                '720p',
                '1080p'
            ];
            
         
            availableDownloadOptions = availableDownloadOptions.filter ( (row) => row.type == 'video' && validQualities.includes(row.quality))
                .map( function (row) {
                    switch(row.quality) {
                        case '480p':
                            row['quality_term'] = 'low';
                            break;
                        case '720p':
                            row['quality_term'] = 'good';
                            break;
                        case '1080p':
                            row['quality_term'] = 'best';
                            break;
                        default:
                            row['quality_term'] = null;
                            break;
                    }

                    return row;
                });
        
            
            let duplicates = [];
            let downloadOptions = [];
            for (let index in availableDownloadOptions) {
                let row = availableDownloadOptions[index];
                let quality = row['quality'];
                if (!duplicates.includes(quality)) {
                    duplicates.push(quality);
                    downloadOptions.push(row);
                }
            }

            return downloadOptions
        }
    },

    methods: {
        seeMore () {
            this.isSeeMore = this.isSeeMore == false ? true : false;
        },

        convertItem(formatId) {
            let convertRequestId = this.convertRequest.id;
            this.processing = true;
            let parameters = {};

            window.axios.post(`/convert/${convertRequestId}/${formatId}`, parameters)
            .then(response => {
                this.$emit('update:convert-request-item', response.data);
                this.$emit('historyApiPushState', `convert/` + this.convertRequest.external_id + `/` + response.data.id);
            }).catch(error => {
                console.log(error);

            }).finally(() => {
                this.processing = false
            })

        }
    }
}
</script>
<style scoped>
.ucwords {
    text-transform: capitalize;
}
</style>