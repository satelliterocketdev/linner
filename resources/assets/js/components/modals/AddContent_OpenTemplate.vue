<template>
<div>
    <button @click="showModal" class="btn flat-button-white">
        <i class="far fa-5x fa-edit"></i>
        <div class="row justify-content-center" style="font-size: 12px">Add Template</div>
    </button>
    <a-modal :centered="true" v-model="visible" :width="700" :footer="null">
        <div class="p-4">
            <div class="row justify-content-center p-2">
                <b>Choose template/s</b>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4 p-2">
                    <AddContentTemplateCard />
                </div>
            </div>

            <div class="footer p-2">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                        </li>
                    </ul>
                </nav>
                <div class="row justify-content-center">
                    <button class="btn rounded-green m-1">Finish/Confirm</button>
                </div>
            </div>
        </div>
    </a-modal>
</div>
</template>

<script>
import AddContentTemplateCard from "../cards/AddContent_Template_Card"
export default {
    name: "AddContentOpenTemplate",  
    components: {
        AddContentTemplateCard,
    },
    data() {
        return {
            visible: false,
        }
    },
     methods: {
        showModal() {
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
    }
}
</script>


