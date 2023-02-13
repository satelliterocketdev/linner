Vue.component ('crossanalysis-new', {
    template: 
    `<div>
        <div class="border rounded bg-white shadow p-3 my-2">
            <div class="row m-2">
                <span> Lorem ipsum </span>
            </div>
            <div class="row m-2">
                <input type="text" class="borderless-input form-control" placeholder="Title" style="font-size: 26px;">
            </div>
            <hr>
            <div class="row p-4" id="crossanalysis-content">
                <div class="col-sm-3 mx-2">
                    <div class="row">
                        <select class="btn btn-outline-dark btn-block p-4" style="background-color: white; color: black;">
                            <option selected> Test 1</option>
                            <option> Test 2 </option>
                        </select>
                    </div>
                    <div class="row">
                        <button type="button" class="btn btn-outline-dark btn-block"> + </button>
                    </div>
                </div>
                <div class="col-sm-3 mx-2">
                    <div class="row">
                        <div class="btn-group" role="group">
                            <select class="btn btn-outline-dark" style="background-color: white; color: black;">
                                <option selected> Test 1</option>
                                <option> Test 2 </option>
                            </select>
                            <button class="btn btn-outline-dark" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                +
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#">Select from tags</a>
                                <a class="dropdown-item" href="#">Day of friend registration</a>
                                <a class="dropdown-item" href="#">Scenario</a>
                                <a class="dropdown-item" href="#">Conversion</a>
                                <a class="dropdown-item" href="#">Click Rate</a>
                                <a class="dropdown-item" href="#">Survey Questionnaire</a>
                                <a class="dropdown-item" href="#">Affiliate</a>
                            </div>
                        </div>
                    </div>
                    <div class="row border rounded p-4">
                    </div>
                </div>
            </div>
            <div class="footer mt-4">
                <div class="row justify-content-center">
                    <button type="button" class="btn rounded-green mx-1">Register</button>
                    <button type="button" class="btn rounded-red mx-1">Reset</button>
                </div>
            </div>
        </div>
    </div>`,
    data() {
      return {
     
      }
    },
    methods: {
      
    }
});
