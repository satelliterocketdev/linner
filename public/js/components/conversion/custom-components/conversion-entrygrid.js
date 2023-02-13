Vue.component ('conversion-entrygrid', {
    template:
    `<div>
        <div class="card h-100 w-100">
            <div class="card-header">            
                <a-checkbox @change="onChangeCheckbox" :key="id"></a-checkbox>
                <div class="row justify-content-center align-items-center">
                    <b>{{ data.title }}</b>
                </div>
            </div>
            <div class="card-body">
                <table>
                    <tr>
                        <th>{{$t('message.status')}}:</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>{{$t('message.actions')}}:</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>{{$t('message.amount_of_people')}}:</th>
                        <td></td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <div class="row justify-content-end align-items-center">
                    <conversion-details v-bind:data="data" :reload-conversion="reloadConversion"></conversion-details>
                </div>
            </div>
        </div>
    </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    status: 'Status',
                    actions: 'Actions',
                    amount_of_people: 'Amount Of People',
                }
            },
            ja: {
                message: {
                    status: 'ステータス',
                    actions: 'アクション',
                    amount_of_people: '人数',
                }
            }
        }
    },
    props: ['data', 'reloadConversion'],
    data() {
        return {
            defaults: {},
            id: '',
            title: '',
            is_active: 0,
            BtnSuccess: "btn-success small-text",
            BootstrapRed: "btn-danger",
            // preview: this.data.messages[0],
        }
    },
    methods: {
        render() {
            this.id = this.data.id
            this.title = this.data.title
            this.is_active = this.data.is_active
        },
        onChangeCheckbox(d) {
            if (d.target.checked === true) {
                this.$parent.selected.push(this.data.id)
            } else {
                const index = this.$parent.selected.indexOf(this.data.id)
                this.$parent.selected.splice(index, 1);
            }

            this.$parent.disableDelete = (this.$parent.selected.length > 0) ? false : true
        },
    },
    filters: {
        count() {
            return 0
        }
    },
    created() {
        this.render()
    },
    updated() {
        this.render()
    }
});