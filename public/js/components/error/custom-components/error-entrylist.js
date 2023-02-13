Vue.component ('error-entrylist', {
    template: 
    `<div class="bg-white rounded border my-2">
        <div class="row justify-content-between align-items-center p-3">
            <div class="col-sm-1">
                <a-checkbox></a-checkbox>
            </div>
            <div class="col-sm-2 text-center">
                <span>Send Date</span>
            </div>
            <div class="col-sm-4 text-center">
                <span>Preview</span>
            </div>
            <div class="col-sm-1 text-center">
                <span>Target</span>
            </div>
            <div class="col-sm-4 text-center">
                <button class="btn btn-success mx-1 small-text">Edit</button>
                <button class="btn btn-info mx-1 small-text">Resend</button>
                <button class="btn btn-secondary mx-1 small-text">Ignore</button>
            </div>
        </div>
    </div>`,
});