<template>
    <div>
        <div class="row justify-content-center" style="font-size: 18px">
            <b><input class="borderless-input form-control" type="text" placeholder="Title"></b>
        </div>
        <div class="row justify-content-center p-2">
            <input type="text" class="form-control p-2" style="width: 50%">
        </div>
        <div class="row justify-content-center">
            <button class="btn rounded-green m-1">Search and Pin to Address</button>
        </div>
        <div class="row justify-content-center m-2">
            <GmapMap
                :center="{lat:10, lng:10}"
                :zoom="7"
                map-type-id="terrain"
                style="width: 800px; height: 300px"
                >
                <GmapMarker
                    :key="index"
                    v-for="(m, index) in markers"
                    :position="m.position"
                    :clickable="true"
                    :draggable="true"
                    @click="center=m.position"
                />
            </GmapMap>
        </div>
    </div>
</template>

<script>
export default {
    name: "AddMap",
}
</script>

<style scoped>
::-webkit-input-placeholder {
   text-align: center;
}

:-moz-placeholder { /* Firefox 18- */
   text-align: center;  
}

::-moz-placeholder {  /* Firefox 19+ */
   text-align: center;  
}

:-ms-input-placeholder {  
   text-align: center; 
}
</style>

