Vue.component ('contentpanel-createcarousel', {
    template: 
    `<div>
        <div class="row align-items-center">
            <div v-for="slide in slides" class="col-sm-4 mb-4">
                <div class="card">
                    <addimage-createcarousel :content="slide.image"></addimage-createcarousel>
                    <div class="image-wrap">
                        <img :src="slide.image.url" class="card-img-top" alt="">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><input class="borderless-input form-control p-0" type="text" placeholder="Title" v-model="slide.title" maxlength="40"></h5>
                        <p class="card-text"><textarea class="borderless-input form-control p-0" placeholder="Description" v-model="slide.text" maxlength="60"></textarea></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li v-for="(option, optionKey) in slide.options" class="list-group-item">
                            <div class="row">
                                <div class="col-10"><input class="borderless-input form-control p-0" type="text" placeholder="Option" @click="currentOption = option" v-model="option.label" maxlength="20"></div>
                                <div class="col-2"><div class="float-right"><a class="" @click="deleteOption(slide, optionKey)"><i class="fa fa-minus"></i></a></div></div>
                            </div>
                        </li>
                    </ul>
                    <div v-if="slide.options.length<4" class="card-body text-center">
                        <a href="#" class="card-link" @click="addOption(slide)"><i class="fa fa-plus"></i></a>
                    </div>
                </div>
            </div>
            <div v-if="slides.length<10" class="col-sm-4">
                <div class="d-flex justify-content-center">
                    <button type="button" @click="addSlide" class="btn rounded-white p-2 shadow mb-4"><i class="fa fa-2x fa-plus mx-2"></i></button>
                </div>
            </div>
        </div>

        <div class="row align-items-center">
            <input type="radio" name="type" class="mr-2" v-model="currentOption.type" value="uri">
            Input URL 
        </div>
        <div class="row p-2">
            <input type="text" class="form-control" v-model="currentOption.uri" maxlength="1000" @click="currentOption.type='uri'">
        </div>
        <div class="row align-items-center">
            <input type="radio" name="type" class="mr-2" v-model="currentOption.type" value="message">
            Selection Limit
        </div>
        <div class="row pb-4">
            <button type="button" class="btn rounded-blue m-1"> Option 1</button>
            <button type="button" class="btn rounded-blue m-1"> Option 2</button>
            <button type="button" class="btn rounded-blue m-1"> Option 3</button>
            <button type="button" class="btn rounded-blue m-1"> Option 4</button>
        </div>
        <div class="row">
            Input the text to be shown in notification message to user
        </div>
        <div class="row p-2">
            <input type="text" class="form-control" v-model="currentOption.text" maxlength="300" @click="currentOption.type='message'">
        </div>
    </div>`,
    props: ['data', 'type'],
    data() {
        return {
            uploadUrl: baseUrl + '/upload',
            slides: [{
                title: '',
                text: '',
                image: {
                    url: baseUrl + '/img/starryrhone_vangogh_big.jpg',
                },
                options: [
                    {
                        'type': 'message',
                        'label': '',
                        'text': '',
                    },
                ],
            }],
            currentSlide: {},
            currentOption: {},
            content: {
                content_type: 'carousel',
            },
        }
    },
    mounted() {
        if (this.type == 'Edit') {
            this.slides = this.data.content_message
            if (this.isValidJSON(this.data.content_message)) {
                this.slides = $.parseJSON(this.data.content_message)
            }
        }

        try {
            this.currentSlide = this.slides[0]
            this.currentOption = this.currentSlide.options[0]
        } catch(e) {
            this.slides = [{
                title: '',
                text: '',
                image: {
                    url: baseUrl + '/img/starryrhone_vangogh_big.jpg',
                },
                options: [
                    {
                        'type': 'message',
                        'label': '',
                        'text': '',
                    },
                ],
            }]
            this.currentSlide = this.slides[0]
            this.currentOption = this.currentSlide.options[0]
        }

        this.content.content_message = this.slides
        Object.assign(this.data, this.content)
    },
    methods: {
        isValidJSON(str) {
            try {
                $.parseJSON(str)
            } catch (e) {
                return false
            }
            return true
        },
        updateSlide() {
            tmp = this.slides
            this.slides = null
            this.slides = tmp
        },
        addOption(slide) {
            self = this
            key = this.slides.indexOf(slide)
            this.slides[key].options.push({
                'type': 'message',
                'label': '',
                'text': '',
            })
        },
        deleteOption(slide, option) {
            key = this.slides.indexOf(slide)
            this.slides[key].options.splice(option, 1)
        },
        addSlide() {
            self = this
            this.slides.push({
                title: '',
                text: '',
                image: {
                    url: baseUrl + '/img/starryrhone_vangogh_big.jpg',
                },
                options: [
                    {
                        'type': 'message',
                        'label': '',
                        'text': '',
                    },
                ],
            })
        }
    }
});