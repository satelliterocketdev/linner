Vue.component('loading', {
    template:
        `<div id="loading">
        <a-modal width="100px" :centered="true" v-model="visible" :closable="false" :footer="null" :maskClosable="false" :destroyOnClose="true" :zIndex="99999">
            <a-spin style="margin: auto; width: 100%;" :spinning="visible">
            </a-spin>
        </a-modal>
    </div>`,
    props: ['visible'],
    data() {
        return {
        }
    },
    methods: {}
});