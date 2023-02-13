<!--HEADER TABLIST CONTAINER-->
<div class="row align-items-center justify-content-start"> 
    <div class="col-sm-12 m-2">
        <ul class="nav nav-tabs" id="dmr_main_tab" role="tablist"> <!-- Tab header -->
            <li class="nav-item">
                <a class="nav-link active" id="text-tab" data-toggle="tab" href="#text" role="tab" aria-controls="text" aria-selected="true">{{__("Text")}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="stamp-tab" data-toggle="tab" href="#stamp" role="tab" aria-controls="stamp" aria-selected="false">{{__("Stamp")}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="image-tab" data-toggle="tab" href="#image" role="tab" aria-controls="image" aria-selected="false">{{__("Image")}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="question-tab" data-toggle="tab" href="#question" role="tab" aria-controls="question" aria-selected="false">{{__("A_question")}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="buttoncarousel-tab" data-toggle="tab" href="#buttoncarousel" role="tab" aria-controls="buttoncarousel" aria-selected="false">{{__("ButtonCarousel")}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="locationinfo-tab" data-toggle="tab" href="#locationinfo" role="tab" aria-controls="locationinfo" aria-selected="false">{{__("Location_information")}} </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="introduction-tab" data-toggle="tab" href="#introduction" role="tab" aria-controls="introduction" aria-selected="false">{{__("Introduction")}} </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="voice-tab" data-toggle="tab" href="#voice" role="tab" aria-controls="voice" aria-selected="false">{{__("Voice")}} </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="video-tab" data-toggle="tab" href="#video" role="tab" aria-controls="video" aria-selected="false">{{__("A_video")}}</a>
            </li>
        </ul><!--end Tab header -->
        
        <div class="tab-content" id="myTabContent" style="border-color: #dee2e6; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-width: 1px ">           
            <!--TEXT-->
            <div class="tab-pane fade show active p-4" id="text" role="tabpanel" aria-labelledby="text-tab">
                <div class="row align-items-start justify-content-start p-2">
                    <div class="col-sm-10">
                        <div class="row align-items-center">    
                            <b>Body</b>
                            <span class="badge badge-danger m-1">{{__("Required")}}</span>
                            <span class="badge badge-secondary m-1">[name] {{__("Correspondence")}}</span>
                            <span class="badge badge-secondary m-1">[{{__("Friend_info")}}] {{__("Correspondence")}}</span>
                            <span class="badge badge-secondary m-1">{{__("Shortened")}} URL {{__("Correspondence")}}</span>
                        </div>
                        <div class="row justify-content-end">
                            <textarea data-length=9500 class="form-control char-textarea" rows="5" name="body_area" id="body_area"></textarea>
                            <span class="char-count pr-2">9500</span> chars remaining
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <b>{{__("Tool")}}</b>
                        <button class="btn btn-primary btn-block m-1 shadow" style="font-size: 8px" type="button">PDF {{__("Upload_when_the")}}</button>
                        <div>{{__("Text_intro2")}}</div>
                    </div>
                </div>
                <div class="row p-2">{{__("Text_intro1")}}</div>
                <div class="row p-2 align-items-center justify-content-between">
                    <button class="btn btn-primary m-1 shadow">{{__("Perform_URL_setting")}}</button>    
                    <div><label><input type="checkbox" class="mr-2">{{__("Do_not_abbreviate_URL_in_this_message")}}</label></div>
                </div>
            </div>
            
            <!--STAMP-->             
            <div class="tab-pane fade p-4" id="stamp" role="tabpanel" aria-labelledby="stamp-tab">
                <div class="row align-items-center justify-content-start p-4">
                                    ...
                </div>
            </div>
            
            <!--IMAGE-->
            <div class="tab-pane fade p-4" id="image" role="tabpanel" aria-labelledby="image-tab">
                @include('tabpanels.distribution_message_registration_tab.image_tab')
            </div>
            
            <!--QUESTION-->
            <div class="tab-pane fade p-4" id="question" role="tabpanel" aria-labelledby="question-tab">
                @include('tabpanels.distribution_message_registration_tab.question_tab')
            </div>

            <!--BUTTON - CAROUSEL-->
            <div class="tab-pane fade p-4" id="buttoncarousel" role="tabpanel" aria-labelledby="buttoncarousel-tab">
                @include('tabpanels.distribution_message_registration_tab.button_carousel_tab')
            </div>

            <!--LOCATION INFORMATION-->
            <div class="tab-pane fade p-4" id="locationinfo" role="tabpanel" aria-labelledby="locationinfo-tab"> 
                @include('tabpanels.distribution_message_registration_tab.location_info_tab')
            </div>

            <!--INTRODUCTION-->
            <div class="tab-pane fade p-4" id="introduction" role="tabpanel" aria-labelledby="introduction-tab"> 
                @include('tabpanels.distribution_message_registration_tab.introduction_tab')
            </div>

            <div class="tab-pane fade p-4" id="voice" role="tabpanel" aria-labelledby="voice-tab"> 
                @include('tabpanels.distribution_message_registration_tab.voice_tab')
            </div>

            <div class="tab-pane fade p-4" id="video" role="tabpanel" aria-labelledby="video-tab">
                @include('tabpanels.distribution_message_registration_tab.video_tab')
            </div>

        </div>
    </div>
</div>

<script type='text/javascript' src="{{ asset('js/dmr_tabpanel.js') }}"></script>
<!-- <script type='text/javascript' src="{{ asset('js/distribution_message_reg.js') }}"></script> -->
    