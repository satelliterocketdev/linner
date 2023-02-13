<template>
    <div class="container-fluid">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="sticker1-tab" data-toggle="tab" href="#nav-sticker1" role="tab" aria-controls="nav-sticker1" aria-selected="true">
                    <img src="/images/sticker/1_1.png" width="25px" height="25px"/>
                </a>
                <a class="nav-item nav-link" id="sticker2-tab" data-toggle="tab" href="#nav-sticker2" role="tab" aria-controls="nav-sticker2" aria-selected="false">
                    <img src="/images/sticker/2_18.png" width="25px" height="25px"/>
                </a>
                <a class="nav-item nav-link" id="sticker3-tab" data-toggle="tab" href="#nav-sticker3" role="tab" aria-controls="nav-sticker3" aria-selected="false">
                    <img src=" /images/sticker/3_180.png" width="25px" height="25px"/>
                </a>
               
            </div>
        </nav>
        <div class="tab-content p-4" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-sticker1" role="tabpanel" aria-labelledby="nav-sticker1-tab">
                <div class="row justify-content-between">
                    <div class="col">
                        <img v-for="(sticker_set1,key1) in sticker_set1" :src="sticker_set1.url" :key="key1" width="85px" height="85px" />
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-sticker2" role="tabpanel" aria-labelledby="nav-sticker2-tab">
                 <div class="row justify-content-between">
                    <div class="col">
                        <img v-for="(sticker_set2,key2) in sticker_set2" :src="sticker_set2.url" :key="key2" width="85px" height="85px" />
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-sticker3" role="tabpanel" aria-labelledby="nav-sticker3-tab">
                <div class="row justify-content-between">
                    <div class="col">
                        <img v-for="(sticker_set3,key3) in sticker_set3" :src="sticker_set3.url" :key="key3" width="85px" height="85px" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AddStampStickers",
    data() {
        return {
            sticker_set1: [
                { url: '/images/sticker/1_1.png'},
                { url: '/images/sticker/1_2.png'},
                { url: '/images/sticker/1_3.png'},
                { url: '/images/sticker/1_4.png'},
                { url: '/images/sticker/1_5.png'},
                { url: '/images/sticker/1_6.png'},
                { url: '/images/sticker/1_7.png'},
                { url: '/images/sticker/1_8.png'},
                { url: '/images/sticker/1_9.png'},
                { url: '/images/sticker/1_10.png'},
                { url: '/images/sticker/1_11.png'},
                { url: '/images/sticker/1_12.png'},
                { url: '/images/sticker/1_13.png'},
                { url: '/images/sticker/1_14.png'},
                { url: '/images/sticker/1_15.png'},
                { url: '/images/sticker/1_16.png'},
                { url: '/images/sticker/1_17.png'},
                { url: '/images/sticker/1_21.png'},
            ],
            sticker_set2: [
                { url: '/images/sticker/2_18.png'},
                { url: '/images/sticker/2_19.png'},
                { url: '/images/sticker/2_20.png'},
                { url: '/images/sticker/2_22.png'},
                { url: '/images/sticker/2_23.png'},
                { url: '/images/sticker/2_24.png'},
                { url: '/images/sticker/2_25.png'},
                { url: '/images/sticker/2_26.png'},
                { url: '/images/sticker/2_27.png'},
                { url: '/images/sticker/2_28.png'},
                { url: '/images/sticker/2_29.png'},
                { url: '/images/sticker/2_30.png'},
                { url: '/images/sticker/2_31.png'},
                { url: '/images/sticker/2_32.png'},
                { url: '/images/sticker/2_33.png'},
                { url: '/images/sticker/2_34.png'},
                { url: '/images/sticker/2_35.png'},
                { url: '/images/sticker/2_36.png'},
            ],
            sticker_set3: [
                { url: '/images/sticker/3_180.png'},
                { url: '/images/sticker/3_181.png'},
                { url: '/images/sticker/3_182.png'},
                { url: '/images/sticker/3_183.png'},
                { url: '/images/sticker/3_184.png'},
                { url: '/images/sticker/3_185.png'},
                { url: '/images/sticker/3_186.png'},
                { url: '/images/sticker/3_187.png'},
                { url: '/images/sticker/3_188.png'},
                { url: '/images/sticker/3_189.png'},
                { url: '/images/sticker/3_190.png'},
                { url: '/images/sticker/3_191.png'},
                { url: '/images/sticker/3_192.png'},
                { url: '/images/sticker/3_193.png'},
                { url: '/images/sticker/3_194.png'},
                { url: '/images/sticker/3_195.png'},
                { url: '/images/sticker/3_196.png'},
                { url: '/images/sticker/3_197.png'},
            ]
        }
    }
}
</script>

<style scoped>
.tab-content {
    border-left: 1px solid #fafafa;
    border-bottom: 1px solid  #fafafa;
    border-right: 1px solid  #fafafa;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
}

.custom-table {
    border-style: none;
    padding: 0px;
}
</style>
