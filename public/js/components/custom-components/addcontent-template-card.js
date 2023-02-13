Vue.component ('addcontent-template-card', {
    template: 
    `<div @click="templateSelected">
        <div class="ml-1 mt-1">
            {{ data.title }}
        </div>
        <div class="card">
            <div class="card-body">
                <div class="scrollable">
                    <p class="card-text">{{ data.formatted_message | truncate }}</p>
<!--                    <img v-for="attachment in data.attachments" :src="attachment.featured_url" :height="attachment.type=='emoji' ? 26 : 69" />-->
                </div>
            </div>
        </div>
    </div>`,
    props: ['data'],
    data() {
        return {
        }
    },
    methods: {
        templateSelected() {
            this.$emit('selectedTemplate', this.data)
        }
    },
    filters: {
        truncate(input) {
            if (input.length > 180) {
                return input.substring(0, 180) + '...'
            } else {
                return input
            }
        }
    }
});