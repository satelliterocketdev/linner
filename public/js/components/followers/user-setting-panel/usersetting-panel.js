Vue.component ('usersetting-panel', {
    template: 
    `<div>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tag-tab" data-toggle="tab" href="#tag" role="tab" aria-controls="tag" aria-selected="true">Tag</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="scenario-tab" data-toggle="tab" href="#scenario" role="tab" aria-controls="scenario" aria-selected="false">Scenario</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="survey-tab" data-toggle="tab" href="#survey" role="tab" aria-controls="survey" aria-selected="false">Survey</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="menu-tab" data-toggle="tab" href="#menu" role="tab" aria-controls="menu" aria-selected="false">Menu</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="template-tab" data-toggle="tab" href="#template" role="tab" aria-controls="template" aria-selected="false">Template</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent" style="border-color: #dee2e6; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-width: 1px ">
        <div class="tab-pane fade show active p-4" id="tag" role="tabpanel" aria-labelledby="tag-tab">
            <div class="row">
                <div class="col-sm-6">
                    <div class="row my-1">
                        <span>Lorem Ipsum</span>
                    </div>
                    <div class="row m-2 p-4 rounded border" style="overflow-y: scroll; max-height:300px">
                        
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row my-1">
                        <span>Lorem Ipsum</span>
                    </div>
                    <div class="row my-1">
                        <input type="text" class="form-control">
                    </div>
                    <div class="row my-1">
                        <button class="btn btn-outline-dark mx-1" style="font-size: 12px">Create Tag</button>
                        <button class="btn btn-secondary mx-1" style="font-size: 12px">Tag Settings</button>
                    </div>
                    <div class="row justify-content-end m-2">
                        <button class="btn rounded-green mx-1">Save Changes</button>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="tab-pane fade show p-4" id="scenario" role="tabpanel" aria-labelledby="scenario-tab">
            <div class="row">
                <div class="col-sm-6">
                    <div class="row my-1">
                        <span>Lorem Ipsum</span>
                    </div>
                    <div class="row m-2 p-4 rounded border" style="overflow-y: scroll; max-height: 00px">
                        
                    </div>
                    <div class="row m-2">
                        <button class="btn btn-outline-dark mx-1" style="font-size: 12px">Create Tag</button>
                        <button class="btn btn-secondary mx-1" style="font-size: 12px">Tag Settings</button>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row my-1">
                        <a-checkbox class="mr-1"></a-checkbox>
                        <span>Send immediately</span>
                    </div>
                    <div class="row my-1">
                        <a-checkbox class="mr-1"></a-checkbox>
                        <span>Start distribution from</span>
                    </div>
                    <div class="row justify-content-center mx-2 my-1">
                        <input type="date" class="form-control mx-1" style="width: 50%;font-size: 12px">
                        <input type="time" class="form-control mx-1" style="width: 25%;font-size: 12px">
                    </div>
                    <div class="row justify-content-end m-2">
                        <button class="btn rounded-green mx-1">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade show p-4" id="survey" role="tabpanel" aria-labelledby="survey-tab">
            <div class="row">
                <div class="col-sm-6">
                    <div class="row my-1">
                        <span>Lorem Ipsum</span>
                    </div>
                    <div class="row m-2 p-4 rounded border" style="overflow-y: scroll; max-height: 00px">
                        
                    </div>
                    <div class="row m-2">
                        <button class="btn btn-outline-dark mx-1" style="font-size: 12px">Create Tag</button>
                        <button class="btn btn-secondary mx-1" style="font-size: 12px">Tag Settings</button>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row my-1">
                        <span>Send a message with a questionnaire</span>
                    </div>
                    <div class="row my-1">
                        <textarea row="10" cols="250"></textarea>
                    </div>
                    <div class="row justify-content-end m-2">
                        <button class="btn rounded-green mx-1">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade show p-4" id="menu" role="tabpanel" aria-labelledby="menu-tab">
            <div class="row">
                <div class="col-sm-6">
                    <div class="row my-1">
                        <span>Lorem Ipsum</span>
                    </div>
                    <div class="row m-2 p-4 rounded border" style="overflow-y: scroll; max-height: 00px">
                        
                    </div>
                    <div class="row m-2">
                        <button class="btn btn-outline-dark mx-1" style="font-size: 12px">Create Tag</button>
                        <button class="btn btn-secondary mx-1" style="font-size: 12px">Tag Settings</button>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row my-1">
                        <a-checkbox class="mr-1"></a-checkbox>
                        <span>Start distribution from</span>
                    </div>
                    <div class="row justify-content-center mx-2 my-1">
                        <input type="date" class="form-control mx-1" style="width: 50%;font-size: 12px">
                        <input type="time" class="form-control mx-1" style="width: 25%;font-size: 12px">
                        <span>To</span>
                    </div>
                    <div class="row justify-content-center mx-2 my-1">
                        <input type="date" class="form-control mx-1" style="width: 50%;font-size: 12px">
                        <input type="time" class="form-control mx-1" style="width: 25%;font-size: 12px">
                        <span>To</span>
                    </div>
                    <div class="row my-1">
                        <a-checkbox class="mr-1"></a-checkbox>
                        <span>Switch to the next menu after the display is complete</span>
                    </div>
                    <div class="row justify-content-end m-2">
                        <button class="btn rounded-green mx-1">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade show p-4" id="template" role="tabpanel" aria-labelledby="template-tab">
            <div class="row">
                <div class="col-sm-6">
                    <div class="row my-1">
                        <span>Lorem Ipsum</span>
                    </div>
                    <div class="row m-2 p-4 rounded border" style="overflow-y: scroll; max-height: 00px">
                        
                    </div>
                    <div class="row m-2">
                        <button class="btn btn-outline-dark mx-1" style="font-size: 12px">Create Tag</button>
                        <button class="btn btn-secondary mx-1" style="font-size: 12px">Tag Settings</button>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row my-1">
                        <a-checkbox class="mr-1"></a-checkbox>
                        <span>Send immediately</span>
                    </div>
                    <div class="row my-1">
                        <a-checkbox class="mr-1"></a-checkbox>
                        <span>Start distribution from</span>
                    </div>
                    <div class="row justify-content-center mx-2 my-1">
                        <input type="date" class="form-control mx-1" style="width: 50%;font-size: 12px">
                        <input type="time" class="form-control mx-1" style="width: 25%;font-size: 12px">
                    </div>
                    <div class="row justify-content-end m-2">
                        <button class="btn rounded-green mx-1">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    </div>`,
});