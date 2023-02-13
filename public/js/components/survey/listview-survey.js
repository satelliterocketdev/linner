Vue.component('listview-survey', {
    template:
        `<div class="bg-white col-sm-12 rounder py-2 mt-2 d-flex align-items-center">
            <p class="col-2 text-left">
                <span v-if="data.type_delivery == 'magazines'">{{$t("message.magazine")}}</span>
                <span v-else="data.type_delivery == 'scenarios'">{{$t("message.step")}}</span>
            </p>
            <p class="col-8 text-left">{{ data.text }}</p>
            <p class="col-1 text-left">{{ data.survey_answers.length }}{{$t("message.human")}}</p>
            <p class="col-1 text-left">
                <button @click="showModal" class="btn btn-success">{{$t("message.view")}}</button>
                <a-modal v-model="visible" @ok="handleOk">
                  <div class="q_box">
                    <p>{{$t('message.question')}}</p>
                    <p>{{ data.text }}</p>
                  </div>
                  <div class="a_box">
                    <p>{{$t('message.anwer_list')}}</p>
                    <ul> 
                        <li class="row" v-for="answer in data.answers">
                            <div class="col-8">{{ answer.label }}</div>
                            <div class="col-2">{{ answer.answers.length }}{{$t('message.count')}}</div>
                            <div class="col-2">{{ (answer.answers.length / data.answer_count)*100 }}%</div>
                        </li>
                    </ul>
                  </div>
                </a-modal>
            </p>
        </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    yen: 'yen',
                    year: 'year',
                    Monthly: 'month',
                    step:'Scenario',
                    magazine:'Magazine',
                    view:'View',
                    question:'Question',
                    anwer_list:'Anwer List',
                    total:'Total',
                    count:'Count',
                    human:''
                }
            },
            ja: {
                message: {
                    yen: '円',
                    year: '年',
                    Monthly: '月度',
                    step:'シナリオ配信',
                    magazine:'一斉配信',
                    view:'表示',
                    question:'質問内容',
                    anwer_list:'回答一覧',
                    total:'全',
                    count:'件',
                    human:'人'
                }
            }
        }
    },
    props: ['data'],
    data() {
        return {
            visible: false,
            RoundedDark: "btn-outline-dark",
        }
    },
    methods: {
      showModal() {
        this.visible = true;
      },
      handleOk(e) {
        console.log(e);
        this.visible = false;
      },
    }
});
