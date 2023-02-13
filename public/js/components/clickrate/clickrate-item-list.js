Vue.component('clickrate-item-list', {
    template: `
    <div class="d-flex align-items-center">
        <div class="mr-3">
            <a-checkbox @change="onChangeCheckbox" :checked="item.checked" :value="item"></a-checkbox>
        </div>
        <div class="row flex-fill no-gutters align-items-center wordbreak-all font-size-table">
            <div class="col-4 col-md-2 border-right py-2 py-sm-3 pr-1 my-1">{{ item.title }}</div>
            <div class="col-1 text-center small-text pl-1">{{ item.send_count }}</div>
            <div class="col-1 text-center small-text">{{ item.access_count }}</div>
            <div class="col-3 px-1 ">
                <input v-model="item.url" type="text" class="form-control" @focus="$event.srcElement.select()" readonly>
            </div>
            <div class="col-3 px-1 ">
                <input v-model="item.redirect_url" type="text" class="form-control" @focus="$event.srcElement.select()" readonly>
            </div>
            <div class="col-md-2 py-2 py-sm-3 d-flex justify-content-end text-center">
                <div>
                    <slot name="detailButton" :item="item"></slot>
                    <slot name="editButton" :item="item"></slot>
                </div>
            </div>
        </div>
    </div>`,
    props: {
        item: {
            type: Object,
            required: true,
        }
    },
    data() {
        return {
        }
    },
    methods: {
        onChangeCheckbox(event) {
            this.$emit('row-check-changed', event);
        },
    },
});