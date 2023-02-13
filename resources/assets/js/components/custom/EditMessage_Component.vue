<template>
<div class="container">
    <div class="row p-2">
        <div class="col border align-items-center p-2" style="max-width: 150px">
            Lorem ipsum
            <button class="btn btn-block  btn-outline-dark" @click="option = 'option1'" >Test 1</button>
            <button class="btn btn-block  btn-outline-dark" @click="option = 'option2'">Test 2</button>
        </div>
        <transition name="slide-fade">
            <div v-if="show" class="col border align-items-center p-2">
                <div v-if="option == 'option1'">
                    <div class="row align-items-center p-1">
                        <input type="number" class="form-control ml-2 mr-2" style="width: 10%">
                        Message number
                    </div>
                    <div class="row m-1 justify-content-between align-items-center">
                        <div class="col">
                            <div class="row justify-content-center align-items-center border rounded p-1">
                                1
                            </div>
                        </div>
                        <i class="fas fa-arrow-right m-1"></i>
                        <div class="col">
                            <div class="row justify-content-center align-items-center border rounded p-1">
                                2
                            </div>
                        </div>
                        <i class="fas fa-arrow-right m-1"></i>
                    <div class="col">
                            <div class="row justify-content-center align-items-center border rounded p-1">
                                3
                            </div>
                        </div>
                        <i class="fas fa-arrow-right m-1"></i>
                        <div class="col">
                            <div class="row justify-content-center align-items-center border rounded p-1">
                                4
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="option == 'option2'">
                    <div class="row justify-content-between align-items-center p-3">
                        <div class="col border rounded">
                            <div class="row justify-content-center align-items-center p-2">
                                TEST 1
                            </div>
                        </div>
                        <i class="fas fa-arrow-right m-1"></i>
                        <div class="col border rounded">
                            <div class="row justify-content-center align-items-center p-2">
                                TEST 2
                            </div>
                        </div>
                        <i class="fas fa-arrow-right m-1"></i>
                        <div class="col">
                            <div class="row justify-content-center align-items-center p-2">
                                <a-popover trigger="click" v-model="visible">
                                    <!-- <a @click="hide" slot="content">Close</a> -->
                                    <template slot="content">
                                        <div class="row p-1">
                                            <input type="date" class="form-control">
                                        </div>
                                        <div class="row justify-content-between align-items-center p-1">
                                            <input type="time" class="form-control" style="width: 25%">
                                            <span>Hour</span>
                                            <input type="time" class="form-control" style="width: 25%">
                                            <span>Minutes</span>
                                        </div>  
                                    </template>
                                    <a-button>Lorem ipsum</a-button>
                                </a-popover>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
        <button @click="show = !show"><i class="fas fa-arrow-right"></i></button>
    </div>
</div>
</template>

<script>
export default {
    name: "EditMessageComponent",
    data() {
        return {
            visible: false,
            show: false,
            option: 'option1',
        }
    },
    methods: {
        hide () {
            this.visible = false
        }
    },
}
</script>

<style scoped>
.arrow {
    padding: 30px 5px;
}

.vl {
  border-left: 1px solid black;
  height: 100%;
}

/* Enter and leave animations can use different */
/* durations and timing functions.              */
.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  /* transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0); */
  transition: all ease;
}
.slide-fade-enter, .slide-fade-leave-to
/* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(-10px);
  opacity: 0;
}
</style>



