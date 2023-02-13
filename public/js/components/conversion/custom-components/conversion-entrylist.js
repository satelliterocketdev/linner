Vue.component ('conversion-entrylist', {
    //<!-- TODO: ステータス、アクション、人数未定 -->
    template: 
    `<div class="bg-white rounded border my-2 px-1 px-sm-3">
        <div class="row no-gutters justify-content-between align-items-center font-size-table wordbreak-all">
            <div class="col-1 py-2 py-sm-4">
                <a-checkbox @change="onChangeCheckbox" :checked="conv.checked" :value="conv"></a-checkbox>
            </div>
            
            <div class="col-2 py-2 py-sm-4 border-right pr-1">
                <span>{{ conv.title }}</span>
            </div>
            <div class="col-2 col-sm-1 py-2 py-sm-4 text-center pl-1">
                <span v-if="conv.is_active == 1">{{ $t('message.active') }}</span>
                <span v-else>{{ $t('message.inactive') }}</span>
            </div>
            <div class="col-2 py-2 py-sm-4 text-left">
                <div v-if="conv.actions.tags.length > 0">
                    {{ $t('message.caption_action_tag') }}
                    <template v-for="(tag, index) in conv.actions.tags" >
                        <template v-if="index!=0">,</template>
                        {{tag}}
                    </template>
                </div>
                <div v-if="conv.actions.scenarios.length > 0">
                    {{ $t('message.caption_action_scenario') }}
                    <template v-for="(scenario, index) in conv.actions.scenarios" >
                        <template v-if="index!=0">,</template>
                        {{scenario}}
                    </template>
                </div>
            </div>
            <div class="col-1 py-2 py-sm-4 text-center">
                <span>{{ $t('message.unit_access_count', { count: conv.access_count }) }}</span>
            </div>
            <div class="col-2 px-1 py-2 py-sm-4">
                <input v-model="conv.url" type="text" class="form-control" @focus="$event.srcElement.select()" readonly>
            </div>
            <div class="col-2 px-1 py-2 py-sm-4 d-flex text-center align-items-center">
                <input v-model="conv.tag_code" type="text" class="form-control" @focus="$event.srcElement.select()" readonly>
            </div>
            <div class="col-sm-1 py-2 py-sm-4 text-right align-items-right">
                <slot name="editButton" :conversion="conv"></slot>
            </div>
        </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
          en: { 
            message: { 
                caption_action_tag: 'Tag :',
                caption_action_scenario: 'Scenario :',
                unit_access_count: '{count}',
                active: 'Active',
                inactive: 'Inactive',
            } 
          },
          ja: {
            message: { 
                caption_action_tag: 'タグ :',
                caption_action_scenario: 'シナリオ :',
                unit_access_count: '{count}人',
                active: '有効',
                inactive: '無効',
            }
          }
        }
    },
    props: {
        conv: {
            type: Object,
            required: true,
        }
    },
    watch: {
        conv: {
            handler(){
            }
            ,immediate: true
        },
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