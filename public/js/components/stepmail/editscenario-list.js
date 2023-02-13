Vue.component ('editscenario-list', {
    template: 
    `<div>
    <div class="row justify-content-between align-items-center m-1">
        <a-checkbox> </a-checkbox>
        <div class="col-sm-1 border rounded shadow bg-white p-2">
            <div class="row justify-content-center">
                Time
            </div>
            <div class="row justify-content-center">
                12:00
            </div>
        </div>
        <div class="col-sm-10 p-2">
            <div class="row justify-content-between align-items-center border rounded shadow bg-white p-2">
                <span>Lorem ipsum dolor sit amet, consctetur adipiscing elit.</span>
                <select class="outline-select">
                    <option selected value="">Option 1</option>
                    <option value="">Option 2</option>
                    <option value="">Option 3</option>
                </select>
                <edit-message></edit-message>
                <confirmation-test></confirmation-test>
                <button class="btn rounded-grey m-1">Scenario_Preview</button>
            </div>
        </div>
    </div>
    </div>`,
    props: ['data']
});