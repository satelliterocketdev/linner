<template>
    <div>
        <div class="card" >
            <img class="card-img-top card-img-container" src="/images/starryrhone_vangogh_big.jpg">
            <div class="card-body">
                <div class="row">
                    <b><h5>Title</h5></b>
                </div>
                <div class="row justify-content-center align-items-center">
                    Option 1
                    <button class="btn rounded-red m-1 text-right"> - </button>
                </div>
                <div class="row justify-content-center align-items-center">
                    Option 2
                    <button class="btn rounded-red m-1 text-right"> - </button>
                </div>
                <div class="row justify-content-center align-items-center">
                    Option 3
                    <button class="btn rounded-red m-1 text-right"> - </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "CreateCarouselCard"    
}
</script>

<style scoped>
.card-img-container {
    height: 150px;
    width: 300px;
    min-height: 75px;
    min-width: 150px;
    overflow: auto;
}
</style>

