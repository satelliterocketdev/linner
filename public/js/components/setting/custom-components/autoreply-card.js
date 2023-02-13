Vue.component ('autoreply-card', {
    template: 
    `<div>
        <div class="card shadow p-3" style="font-size: 12px">
            <div class="d-flex justify-content-center" style="font-size: 16px">
                <span>Auto Reply [1] when adding a friend</span>
            </div>
            <div class="d-flex justify-content-center py-1">
                <span>During delivery</span>
            </div>
            <div class="d-flex justify-content-center py-1">
                <textarea class="form-control" rows="6" cols="50" ></textarea>
            </div>
            <div class="d-flex justify-content-center py-1">
                <button class="btn btn-secondary" style="font-size: 12px">Click to show preview</button>
            </div>

            <div class="d-flex justify-content-start py-1">
                <span>Delivered: </span>
                <span> 0 people</span>
            </div>
            <div class="d-flex justify-content-start py-1">
                <span>Delivery Condition</span>
            </div>
            <div class="d-flex justify-content-start py-1">
                <autoreply-friendmessagetarget></autoreply-friendmessagetarget>
                <span> Person who joined 9:00 to 12:59</span>
            </div>
            <div class="footer">
                <div class="d-flex justify-content-end">
                    <button class="btn mx-1 btn-success small-text">Edit</button>
                    <button class="btn mx-1 btn-info small-text" @click="copyScenario">Copy</button>
                    <confirmation-test></confirmation-test>
                </div>
            </div>
        </div>
    </div>`,
    methods: {
        copyScenario() {
            self = this
            axios.post('stepmail/copy', { id: this.data.id })
            .then(function(response){
                self.reloadScenario()
            })
        }
    }
});