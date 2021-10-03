<template>
    <div>
        <transition name="slide-fade" appear>
            <div v-if="showTransition">
                <div class="row">
                    <div class="col-md-8">
                        <img src="https://instagram.fceb2-1.fna.fbcdn.net/v/t51.2885-15/e35/244129387_616372966408237_2087322593076326709_n.jpg?_nc_ht=instagram.fceb2-1.fna.fbcdn.net&_nc_cat=101&_nc_ohc=Cm6Zm-W4ViYAX-jCi__&edm=ADECaisBAAAA&ccb=7-4&oh=0cb7ad39d81bfb4b95e26a06ea2f82ad&oe=615BEEA6&_nc_sid=ca78b6" />
                        <template v-if="convertRequestThumbnail != null">
                            <img class="img-fluid rounded img-thumbnail" :src="convertRequestThumbnail" />
                        </template>
                        <template v-else>
                            <img class="img-fluid rounded img-thumbnail" :src="defaultThumbnail" />
                        </template>

                        <div class="download-option py-2">
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <h5>Video &bullet; Mp4</h5>
                                    <div class="card mb-1">
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <template v-for="rowVideo in availableDownloadOptionsVideo">
                                                    <a href="#" class="btn btn-success" v-on:click.prevent="convertItem(rowVideo.id)">
                                                        <template v-if="rowVideo.quality != null">
                                                            <strong>{{ rowVideo.quality }}</strong>
                                                            &bullet;
                                                        </template>
                                                        <strong v-show="rowVideo.quality_term != null" class="ucwords">
                                                            <small>{{ rowVideo.resolution }}</small>
                                                            {{ rowVideo.quality_term }} quality
                                                        </strong>

                                                        Download 
                                                        <template v-if="rowVideo.size_plus != null">
                                                            ({{ rowVideo.size_plus }})
                                                        </template>
                                                    </a>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" v-if="availableDownloadOptionsAudio.length >= 1">
                                    <h5>Audio &bullet; Mp3</h5>
                                    <div class="card mb-1">
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <template v-for="rowAudio in availableDownloadOptionsAudio" v-on:click.prevent="convertItem(rowAudio.id)">
                                                    <a href="#" class="btn btn-success" v-on:click.prevent="convertItem(rowAudio.id)">
                                                        <strong class="ucwords">
                                                            Good Quality
                                                        </strong>
                                                        Download 
                                                        <template v-if="rowAudio.size_plus != null">
                                                            ({{ rowAudio.size_plus }})
                                                        </template>
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
                                <template v-if="convertRequestDuration != null">
                                    -
                                    <span class="text-muted">{{ convertRequestDuration }}</span>
                                </template>
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
            defaultThumbnail: "/images/no-image.png"
        }
    },

    mounted () {
        this.$emit('convertTypeSet', {'request_type': this.convertRequest.type, 'no_history_api': true});
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
                '360p',
                '370p',
                '480p',
                '540p',
                '720p',
                '1080p'
            ];
            
            let availableDownloadOptionsFiltered = availableDownloadOptions.filter ( (row) => row.type == 'video' && validQualities.includes(row.quality))
                .map( function (row) {
                    switch(row.quality) {
                        case '360p':
                        case '370p':
                        case '480p':
                        case '540p':
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
            
            if (availableDownloadOptionsFiltered.length < 1) {
               availableDownloadOptionsFiltered = availableDownloadOptions.map( function (row) {
                   row['quality'] = null;
                   row['quality_term'] = 'best';
                   return row;
                })
                .filter( (row) => ( row.type == 'video') );
            }
        
            let duplicates = [];
            let downloadOptions = [];
            for (let index in availableDownloadOptionsFiltered) {
                let row = availableDownloadOptionsFiltered[index];
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