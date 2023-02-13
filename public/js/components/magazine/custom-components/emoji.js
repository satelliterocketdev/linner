Vue.component('emoji', {
    template:
    `<div class="m-2 p-2 bg-secondary rounded">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="emoji1-tab" data-toggle="tab" href="#emoji1" role="tab" aria-controls="emoji1" aria-selected="true">Emoji1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="emoji2-tab" data-toggle="tab" href="#emoji2" role="tab" aria-controls="emoji2" aria-selected="false">Emoji2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="emoji3-tab" data-toggle="tab" href="#emoji3" role="tab" aria-controls="emoji3" aria-selected="false">Emoji3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="emoji4-tab" data-toggle="tab" href="#emoji4" role="tab" aria-controls="emoji4" aria-selected="false">Emoji4</a>
            </li>
        </ul>
        <div class="tab-content d-flex bg-white" id="myTabContent" style="border-color: #dee2e6; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-width: 1px ">
            <div class="tab-pane fade show active p-4" id="emoji1" role="tabpanel" aria-labelledby="emoji1-tab">
                <div class="row justify-content-center">
                    <div v-for="emoji in emojis.smiley" class="mr-1">
                        <img :src="emoji.url" :data-type="emoji.type" @click="selectEmoji(emoji)">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show p-4" id="emoji2" role="tabpanel" aria-labelledby="emoji1-tab">
                <div class="row justify-content-center">
                    <div v-for="emoji in emojis.activities" class="mr-1">
                        <img :src="emoji.url" :data-type="emoji.type" @click="selectEmoji(emoji)">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show p-4" id="emoji3" role="tabpanel" aria-labelledby="emoji1-tab">
                <div class="row justify-content-center">
                    <div v-for="emoji in emojis.objects" class="mr-1">
                        <img :src="emoji.url" :data-type="emoji.type" @click="selectEmoji(emoji)">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show p-4" id="emoji4" role="tabpanel" aria-labelledby="emoji1-tab">
                <div class="row justify-content-center">
                    <div v-for="emoji in emojis.monochromes" class="mr-1">
                        <img :src="emoji.url" :data-type="emoji.type" @click="selectEmoji(emoji)">
                    </div>
                </div>
            </div>
        </div>
    </div>`,
    props: ['write', 'attachment'],
    data() {
        return {
            emojis: {
                'smiley': [],
                'activities': [],
                'objects': [],
                'monochromes': [],
            },
        }
    },
    created() {
        let self = this
        axios.get("upload/lists/emoji")
            .then(function (response) {
                $.each(response.data, (key, data) => {
                    let tab = data.tab
                    switch (tab) {
                        case 'smiley':
                            self.emojis.smiley.push(data)
                            break
                        case 'activities':
                            self.emojis.activities.push(data)
                            break
                        case 'objects':
                            self.emojis.objects.push(data)
                            break
                        case 'monochromes':
                            self.emojis.monochromes.push(data)
                            break
                    }
                })
            })
    },
    methods: {
        selectEmoji(emoji) {
            this.write('<img src="' + emoji.url + '" data-type="' + emoji.type + '" />')
            // this.attachment.push(emoji)
        },
    },
});
