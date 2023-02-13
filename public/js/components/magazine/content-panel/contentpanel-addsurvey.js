Vue.component ('contentpanel-addsurvey', {
    template: 
    `<div>
    <survey-questionnaire></survey-questionnaire>
    <div class="row pt-3">
        {{$t('message.introduction')}}
    </div>
    <div class="row pb-3">
        <textarea class="form-control" maxlength="1000" rows="3" style="resize: none"></textarea>
    </div>
    <div class="row">
        {{$t('message.select_limit')}}
    </div>
    <div class="row justify-content-left pb-3">
        <button class="btn rounded-blue m-1">{{$t('message.option1')}}</button>
        <button class="btn rounded-blue m-1">{{$t('message.option2')}}</button>
        <button class="btn rounded-blue m-1">{{$t('message.option3')}}</button>
        <button class="btn rounded-blue m-1">{{$t('message.option4')}}</button>
    </div>
    <div class="row">
        {{$t('message.notice_text')}}
    </div>
    <div class="row pb-3">
        <textarea class="form-control" maxlength="1000" rows="3" style="resize: none"></textarea>
    </div>
    </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    questionnaire_contents: 'Questionnaire contents',
                    introduction: 'Introduction message',
                    select_limit: 'Select Limit',
                    option1: 'Once a month for each option',
                    option2: 'One for each panel',
                    option3: 'No limit',
                    option4: 'One for all carousels',
                    notice_text: 'Notice text'
                }
            },
            ja: {
                message: {
                    questionnaire_contents: 'アンケート内容',
                    introduction: '直前導入メッセージ（任意）',
                    select_limit: '選択制限',
                    option1: '各選択肢に月１回',
                    option2: '各パネル毎に一つ',
                    option3: '制限なし',
                    option4: '全カルーセルで一つ',
                    notice_text: '通知文面'
                }
            }
        }
    },
    props: [],
    data() {
        return {}
    },
});