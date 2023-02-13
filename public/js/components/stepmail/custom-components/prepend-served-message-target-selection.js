Vue.component ('prepend-served-message-target-selection', {
  template: 
  `<span v-for="(section, key) in data">
    <!-- Exclude dates from label visibility -->
    <span v-show="key != 'dates'" v-for="served in section">
      <span v-for="serve in served">
        <span v-if="Array.isArray(serve.value)">
          <span v-for="value in serve.value">
            {{ value }}
          </span>
        </span>
        <span v-else>
          {{ serve.value }}
        </span>
      </span>
    </span>
  </span>`,
  props: ['data'],
  data() {
    return {
      labels: [],
    }
  },
  mounted() {
    // console.log(this.data)
    this.render()
  },
  methods: {
    render() {
      child = this
      child.labels = []
      if ($.isArray(this.data)) {
          $.each(this.data, function(key, value){
              if ($.isArray(value)) {
                  child.labels = $.merge(value, child.labels)
              } else {
                  child.labels.push(value)
              }
          })
      } else {
          child.labels.push(this.data)
      }
    }
  }
})