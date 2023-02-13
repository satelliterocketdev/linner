<template>
    <div>
        <div class="row">
            <div class="col-sm-8">
                <!-- <div class="row"> -->
                    <!-- <textarea class="form-control" maxlength="1000" rows="7" v-model="message" @keyup="charCount()"></textarea> -->
                    <CreateMessageCKEditor />
                <!-- </div> -->
                <div class="row" style="font-size: 12px">
                    <div class="col-sm-8 align-items-end">
                        <div class="row p-2" >
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="row p-2 justify-content-end">
                            <label>{{totalcharacter}} / {{maxChar}} Characters</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">    
                <!-- <button class="btn btn-block rounded-cyan m-1"><i class="fas fa-images mr-2"></i>Add Content</button> -->
                <AddContentMainMenu v-bind:btnClass="this.RoundedCyanBtn" />
                <div class="rounded border bg-secondary text-center p-4 m-2">
                    <label><i class="far fa-smile-wink mr-2"></i>EMOJI</label>
                    <div class="row rounded border bg-white p-4">
                        STICKERS
                    </div>
                </div> 
            </div>
        </div>
    </div>
</template>

<script>
import AddContentMainMenu from "../modals/AddContent_MainMenu.vue"
import CreateMessageCKEditor from "../custom/CreateMessage_CKEditor.vue"
export default {
    name: "CreateMessage",
    components: {
        AddContentMainMenu,
        CreateMessageCKEditor
    },
    data() {
        return {
            RoundedCyanBtn: "rounded-cyan",
            message: "",
            totalcharacter: 0,   
            maxChar: 1000,
        }
    },
    methods: {
        charCount(){
            this.totalcharacter = this.maxChar - this.message.length;
       }
    }
}
</script>
