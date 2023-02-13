Vue.component ('prepend-message-target-selection', {
    template: 
    `<div class="row p-1">
        <span v-for="(label, key) in labels">
            <span v-if="key!=0">&nbsp; : &nbsp;</span>{{ label }} {{ key+1 }}
        </span>
    </div>`,
    props: ['values'],
    data() {
        return {
            labels: [],
        }
    },
    mounted() {
        this.render()
    },
    watch: {
        values() {
            this.render()
        }
    },
    methods: {
        render() {
            child = this
            child.labels = []
            if ($.isArray(this.values)) {
                $.each(this.values, function(key, value){
                    if ($.isArray(value)) {
                        child.labels = $.merge(value, child.labels)
                    } else {
                        child.labels.push(value)
                    }
                })
            } else {
                child.labels.push(this.values)
            }
        }
    }
})