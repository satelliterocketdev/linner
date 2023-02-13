Vue.component('prev-next', {
    template:
    `<div class="pagination">
        <a :href="'?page='+prevPage" class="prev" v-if="currentPage > 1" @click.prevent="onPrev">&lt; 前へ</a>
        <div class="total">ページ{{currentPage}}/{{totalPage}}</div>
        <a :href="'?page='+nextPage" class="next" v-if="currentPage < totalPage" @click.prevent="onNext">次へ &gt;</a>
      </div>`,
    i18n: {
        messages: {
            en: {},
            ja: {}
        }
    },
    props: ['page','data','perPage'],
    data() {
        return {
            currentPage: this.page,
            totalPage: Math.ceil(this.data.length / this.perPage)
        }
    },
    computed: {
        prevPage() {
          return Math.max(this.currentPage - 1, 1);
        },
        nextPage() {
          return Math.min(this.currentPage + 1, this.totalPage);
        }
    },
    methods: {
        onPrev() {
            this.currentPage = this.prevPage
            this.$emit("change", this.currentPage)
        },
        onNext() {
            this.currentPage = this.nextPage
            this.$emit("change", this.currentPage)
        }
    }
});
