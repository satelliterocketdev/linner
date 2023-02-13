Vue.component ('prepend-message-target-date-selection', {
    template: 
    `<div>
        <div class="row p-1">
          <span v-if="values.from">{{ formatDate(values.from) }}</span>
          <span v-if="values.to">&nbsp; : &nbsp;</span>
          <span v-if="values.to">{{ formatDate(values.to) }}</span>
        </div>
    </div>`,
    props: ['values'],
    methods: {
      formatDate(date) {
        return moment(date).format('YYYY-MM-DD')
      }
    }
})