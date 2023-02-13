Vue.component ('tutorial', {
  template:
    `<div>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :closable="false" :footer="null" :maskClosable="false" :destroyOnClose="true" :zIndex="9999">
            <a-spin :spinning="spinning">
                <div class="spin-content">
                    <div class="d-flex justify-content-center mx-0 mb-3">
                        <div style="font-size: 1.2rem;" v-html="currentData.text"></div>
                    </div>
                    <div class="row d-flex justify-content-around">
                        <a-button v-for="(button, key) in currentData.buttons" :key="button" @click="next">{{ button }}</a-button>
                    </div>
                </div>
            </a-spin>
        </a-modal>
    </div>`,
    props: ['state'],
    data() {
        return {
            data: [],
            currentData: [],
            spinning: true,
            visible: this.$parent.tutorial == 0 ? true : false,
            counter: 0
        }
    },
    mounted() {
        if (this.visible) {
            this.reloadData()
        }
    },
    watch: {
        state: function () {
            if (this.state > 0) {
                this.handleSteps()
            }
        }
    },
    methods: {
        handleOk(e) {
            this.$parent.tutorial = false
        },
        handleSteps()
        {
            if (this.counter >= this.data.length - 1) {
                this.nextStep()
            } else {
                this.nextIndex()
            }
        },
        next(e) {
            let event = e.target.innerText
            switch (event) {
                case '次へ':
                case 'Next':
                    this.handleSteps()
                    break
                case 'スキップ':
                case 'Skip':
                    this.skipTutorial()
                case '閉じる':
                case 'Close':
                    this.finishTutorial()
                    break
            }
        },
        setIndex(index)
        {
            localStorage.setItem('i', index)
        },
        getIndex()
        {
            let index = localStorage.getItem('i')
            if (!index) {
                this.setIndex(0)
                return 0
            }
            return index
        },
        clearIndex()
        {
            localStorage.clear();
        },
        nextIndex() {
            this.currentData = this.data[++this.counter]
            if (this.currentData.text == null) {
                this.visible = false
                return
            }
            this.visible = true
        },
        nextStep() {
            this.setIndex(parseInt(this.getIndex(), 10) + 1)
            this.spinning = true
            newThis = this
            axios.get('tutorial/' + this.getIndex() )
            .then(function(response) {
                if (response.data.url == null) {
                    newThis.visible = false
                    return
                }
                document.location.href = response.data.url
            })
        },
        finishTutorial() {
            this.setIndex(parseInt(this.getIndex(), 10) + 1)
            this.spinning = true
            newThis = this
            axios.get('tutorial/' + this.getIndex())
            .then(function(response) {
                document.location.href = '/'
                newThis.spinning = false
                newThis.clearIndex()
                newThis.visible = false
            })
        },
        reloadData() {
            newThis = this
            axios.get('tutorial/' + this.getIndex() )
            .then(function(response) {
                if (response.data.url == null) {
                    newThis.visible = false
                    return
                }
                if (newThis.isUrlMatching(response.data.url)) {
                    newThis.currentData = response.data.contents[newThis.counter]
                    newThis.data = response.data.contents
                    newThis.spinning = false
                }
            })
        },
        isUrlMatching(url) {
            let lastPart = window.location.href.split("/").pop()
            let compareUrl = '/' + lastPart
            if (url != compareUrl) {
                // 普通はずれないけど、ずれたら最初からチュートリアルを始める
                this.clearIndex()
                document.location.href = '/'
                return false
            }
            return true
        },
        skipTutorial() {
            this.spinning = true
            newThis = this
            axios.post('skiptutorial')
            .then(function(response) {
                newThis.spinning = false
                newThis.visible = false
                newThis.clearIndex()
                document.location.href = '/'
            })
        }
    }
});