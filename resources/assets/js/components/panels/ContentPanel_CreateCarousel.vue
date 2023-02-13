<template>
<div>
    <div id="createcarousel-card-container" class="row p-2">
        <CreateCarouselCard />
    </div>
    <div class="row align-items-center">
        <input type="radio" class="mr-2">
        Input URL 
    </div>
    <div class="row p-2">
        <input type="text" class="form-control">
    </div>
    <div class="row align-items-center">
        <input type="radio" class="mr-2">
        Lorem Ipsum 
    </div>
    <div class="row pb-4">
        <button class="btn rounded-blue m-1"> Option 1</button>
        <button class="btn rounded-blue m-1"> Option 2</button>
        <button class="btn rounded-blue m-1"> Option 3</button>
        <button class="btn rounded-blue m-1"> Option 4</button>
    </div>
    <div class="row">
        Input the text to be shown in notification message to user
    </div>
    <div class="row p-2">
        <input type="text" class="form-control">
    </div>
</div>
</template>

<script>
import CreateCarouselCard from "../cards/CreateCarousel_Card.vue"
export default {
    name: "CreateCarousel",
    components: {
        CreateCarouselCard,
    }
}
</script>

<style scoped>
input[type=radio] {
    transform: scale(1.5)}
</style>

