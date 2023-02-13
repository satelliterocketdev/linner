<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['guest']], function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Registration Routes
    |--------------------------------------------------------------------------
    */
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@sendMail');
    //メールボックスから
    Route::get('register/{token}', 'Auth\RegisterController@register')->name('confirm');

    /*
    * 疑似的に作成
    */
    Route::get('register_complete', 'Auth\RegisterController@registerComplete');
    
    /*
    |--------------------------------------------------------------------------
    | Password Reset Routes
    |--------------------------------------------------------------------------
    */
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get("/forgotemail", function () {
        return View::make('auth.passwords.forgot_email');
    })->name('forgot.email');

    /*
    * アンケート回答用
    */
    Route::get('survey/answer', 'SurveyController@answer');

});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', ['as' => 'dashboard', 'uses' => 'DashboardController@index']);
    Route::get('/list', 'DashboardController@list');
    Route::get('login-offcial-admin', ['as'=>'login-offcial-admin','uses'=>'LoginAdminOfficialController@index']);

    // Notification
    Route::get('/notifications', 'NotificationController@index');
    Route::get('/notifications/lists', 'NotificationController@lists');
//    Route::get('/notifications/all_lists', 'NotificationController@allLists');
    Route::get('/notifications/page', 'NotificationController@page');
    Route::get('/notifications/{id}', 'NotificationController@show');
    Route::resource('notifications', 'NotificationController', [
        'except' => ['create', 'edit']
    ]);

    Route::get('stepmail/lists', 'StepMailController@lists');

    Route::group(['middleware' => ['can:'.App\Role::ROLE_SCENARIO_DISTRIBUTION_EDITABLE]], function () {
        Route::get('stepmail/draft_messages', 'StepMailController@draftMessages');
        Route::get('stepmail/message/{generated_id}', 'StepMailController@view');
        Route::get('stepmail/all-account', 'StepMailController@listsAllAccount'); // 外部: 全員配信メッセージターゲットのシナリオ選択肢に使用
        Route::post('stepmail/draft_messages', 'StepMailController@saveDraftMessage');
        Route::post('stepmail/copy', 'StepMailController@copy');
        Route::post('stepmail/activity', 'StepMailController@changeActivity');
        Route::delete('stepmail/message/{message_ids}', 'StepMailController@destroyMessage');
        Route::delete('stepmail/draft_message/{id}', 'StepMailController@deleteDraftMessage');
        Route::resource('stepmail', 'StepMailController', [
            'except' => ['create', 'edit']
        ]);
    });

    Route::resource('stepmail', 'StepMailController', [
        'except' => ['create', 'edit', 'store', 'update','destroy']
    ]);

    Route::get('account/line', 'AccountInfoController@line');

    Route::get('upload/lists/{type}', 'UploadController@lists');

    Route::post('upload/image', 'UploadController@store');
    Route::post('upload/file', 'UploadController@store');
    Route::post('upload/delete', 'UploadController@destroy');

    Route::get('source/lists', 'SourceController@lists');
    Route::resource('source', 'SourceController');

    Route::group(['middleware' => ['can:'.App\Role::ROLE_CONVERSION_AVAILABLE]], function () {
        Route::get('conversion/lists', 'ConversionController@lists');
        Route::get('conversion/generate_token', 'ConversionController@generateToken');
        Route::get('conversion/edit/{id}', 'ConversionController@edit');
        Route::post('conversion/start', 'ConversionController@start');
        Route::post('conversion/stop', 'ConversionController@stop');
        Route::resource('conversion', 'ConversionController');
        Route::get('conversion', ['as' => 'conversion', 'uses' => 'ConversionController@index']);
    });

    Route::get('magazine/lists', 'MagazineController@lists');

    Route::group(['middleware' => ['can:'.App\Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE]], function () {
        // Magazine
        Route::post('magazine/copy', 'MagazineController@copy');
        Route::post('magazine/schedule', 'MagazineController@schedule');
        Route::get('magazine/draft_messages', 'MagazineController@draftMessages');
        Route::post('magazine/draft_messages', 'MagazineController@saveDraftMessage');
        Route::post('magazine/template', 'MagazineController@saveTemplate');
        Route::delete('magazine/draft_message/{id}', 'MagazineController@deleteDraftMessage');
        Route::resource('magazine', 'MagazineController', [
            'except' => ['create', 'edit']
        ]);
        Route::post('magazine/store_many', 'MagazineController@storeMany');
    });

    Route::resource('magazine', 'MagazineController', [
        'except' => ['create', 'edit', 'store', 'update','destroy']
    ]);

    Route::get('setting', ['as' => 'setting', 'uses' => 'SettingController@index']);
    Route::get('crossanalysis', ['as' => 'crossanalysis', 'uses' => 'CrossAnalysisController@index']);
    Route::get('error', ['as' => 'error', 'uses' => 'ErrorController@index']);
    Route::get('transmittedmedia', ['as' => 'transmittedmedia', 'uses' => 'TransmittedMediaController@index']);

    Route::get('survey/lists', 'SurveyController@lists');
    Route::resource('survey', 'SurveyController', [
        'except' => ['create', 'edit']
    ]);

    Route::group(['middleware' => ['can:'.App\Role::ROLE_FRIEND_INFORMATION_MANAGEMENT_AVAILABLE]], function () {
        Route::get('follower/lists', 'FollowerController@lists');
        Route::get('followers', 'FollowerController@followersList');

        //followers
        Route::get('follower', ['as' => 'followers', 'uses'=>'FollowerController@index']);
        Route::get('follower/user-info/{followerId}', 'FollowerController@userInfo');
        Route::put('follower/user-info/{followerId}', 'FollowerController@update');
        Route::post('follower/add-tags', 'FollowerController@addTags');
        Route::post('follower/add-scenarios', 'FollowerController@addScenarios');
        Route::post('follower/add-rich-menu', 'FollowerController@addRichMenu');
        Route::post('follower/block', 'FollowerController@block');
    });

    Route::get('talk', ['as' => 'talk', 'uses' => 'TalkController@index']);
    Route::get('talk/list', ['as' => 'talk', 'uses' => 'TalkController@list']);
    Route::get('talk/{followerId?}', ['as' => 'talk', 'uses' => 'TalkController@index']);
    Route::get('talk/message/{followerId}', ['as' => 'talk', 'uses' => 'TalkController@message']);
    Route::post('talk/mark-unread', ['as' => 'talk', 'uses' => 'TalkController@markUnread']);
    Route::post('talk/mark-read', ['as' => 'talk', 'uses' => 'TalkController@markRead']);
    Route::post('talk/mark-supported/none', ['as' => 'talk', 'uses' => 'TalkController@markSupportedNone']);
    Route::post('talk/mark-supported/required', ['as' => 'talk', 'uses' => 'TalkController@markSupportedRequired']);
    Route::post('talk/delete', ['as' => 'talk', 'uses' => 'TalkController@delete']);
    Route::get('talk/download/{attachmentsId}', 'TalkController@downloadAttachment')->name('talk.download');
    Route::post('talk/sendMessage/{messageType}', 'TalkController@sendMessage');

    Route::get('template/lists', 'TemplateController@lists');

    // template
    Route::group(['middleware' => ['can:'.App\Role::ROLE_TEMPLATE_EDITING_IS_POSSIBLE]], function () {
        Route::post('template/copy', 'TemplateController@copy');
        Route::resource('template', 'TemplateController', [
            'except' => ['create', 'edit']
        ]);
    });

    Route::resource('template', 'TemplateController', [
        'except' => ['create', 'edit', 'store', 'update','destroy']
    ]);

    Route::group(['middleware' => ['can:'.App\Role::ROLE_URL_CLICK_MEASUREMENT_AVAILABLE]], function () {
        Route::get('clickrate/show_detail/{id}', 'ClickrateController@showDetail');
        Route::get('clickrate/lists', 'ClickrateController@lists');
        Route::get('clickrate/generate_token', 'ClickrateController@generateToken');
        Route::post('clickrate/batch-delete', 'ClickrateController@batchDelete');
        Route::resource('clickrate', 'ClickrateController');
        Route::get('clickrate', ['as' => 'clickrate', 'uses' => 'ClickrateController@index']);
    });

    //tags routes
    // Route::get('tag', ['as' => 'tags', 'uses'=> 'TagsManagamentController@index']);
    // Route::get('tag/list', 'TagsManagamentController@lists');
    // Route::get('tag/{id}/edit', 'TagsManagamentController@edit');
    // Route::post('tag', 'TagsManagamentController@store');

    Route::group(['middleware' => ['can:'.App\Role::ROLE_TAG_MANAGEMENT_AVAILABLE]], function () {
        //tags routes
        Route::get('tag', ['as' => 'tags', 'uses'=> 'TagsManagamentController@index']);
        Route::get('tag/folders/{folderId?}', 'TagsManagamentController@folders');
        Route::post('tag/folders', 'TagsManagamentController@storeFolder');
        Route::put('tag/folders/{folderId}', 'TagsManagamentController@updateFolder');
        Route::post('tag/batch-delete', 'TagsManagamentController@batchDelete');
        Route::get('tag/info/{tagId}', 'TagsManagamentController@tagInfo');
        Route::put('tag/{tagId}', 'TagsManagamentController@updateTag');
        // 他機能でも利用中のルーティング
        Route::post('tag', 'TagsManagamentController@store');//タグ管理、外部: 新規タグ作成確定アクション
        Route::get('tag/list', 'TagsManagamentController@lists');//外部: メッセージアクション系のタグ選択肢に使用
        Route::get('tag/taglist', 'TagsManagamentController@tags');// 外部: メッセージターゲット系のタグ選択肢に使用
        Route::get('tag/all-account', 'TagsManagamentController@listsAllAccount');// 外部: 全員配信メッセージターゲット系のタグ選択肢に使用
    });

    //account info
    Route::get('/accountinfo', 'AccountInfoController@index')->name("accountinfo");
    Route::get('accountinfo/list', 'AccountInfoController@lists');
    Route::delete('accountinfo/{id}', 'AccountInfoController@destroy');
    Route::get('accountinfo/{id}', 'AccountInfoController@edit');
    Route::put('/accountinfo/{id}', 'AccountInfoController@userUpdate');

    Route::post('accountinfo/edit/secret', 'AccountInfoController@updateSecret');
    Route::post('accountinfo/edit/access_token', 'AccountInfoController@updateAccessToken');
    Route::post('accountinfo/edit/secret/{id}', 'AccountInfoController@updateSecret');
    Route::post('accountinfo/edit/access_token/{id}', 'AccountInfoController@updateAccessToken');
    Route::post('accountinfo/edit/name', 'AccountInfoController@updateName');
    Route::post('accountinfo/edit/followlink', 'AccountInfoController@updateFollowLink');
    Route::post('accountinfo/edit/image', 'AccountInfoController@updateProfileImage');
    Route::post('addaccount', 'AccountInfoController@registerAddAccount')->name("addaccount");
    Route::get('changeaccount/{account_id}', 'AccountInfoController@changeAccount');

    // Route::get('newaccount', 'AccountInfoController@newAccount');
    // Route::post('newaccount', 'AccountInfoController@registerNewAccount')->name("newaccount");
    //Route::get('newaccountcompleted', 'AccountInfoController@accountRegisterSuccess');
    Route::post('accountinfo/user/addUser', 'AccountInfoController@registerAddUser');
    Route::get('accountinfo/user/roles', 'AccountInfoController@getRoles');

    // Line Request
    Route::get('newaccount', 'LineRequestController@index');
    Route::post('newaccount', 'LineRequestController@newaccount')->name('newaccount');
    Route::get('newaccountcompleted', 'LineRequestController@accountRegisterSuccess');

    // rich menu
    Route::get('richmenu', 'RichMenuController@index');
    Route::post('richmenu', 'RichMenuController@store');
    Route::get('richmenu/type/{menu_type}', 'RichMenuController@actionList');
    Route::get('richmenu/list', 'RichMenuController@lists');
    Route::get('richmenu/edit/{id}', 'RichMenuController@edit');
    Route::post('richmenu/edit/{id}', 'RichMenuController@update');
    Route::post('richmenu/copy', 'RichMenuController@copy');
    Route::post('richmenu/activity/{id}', 'RichMenuController@updateActivity');
    Route::delete('richmenu/delete/{ids}', 'RichMenuController@destroy');


    Route::get('plan', 'PlanController@index')->name('plan');
    Route::get('plan/list', 'PlanController@list');
    Route::post('plan/register', 'PlanController@register')->name('plan_register');
    Route::post('plan/update', 'PlanController@update')->name('plan_update');
    Route::get('plan/detail', 'PlanController@detail');
    Route::get('plan/detail_list', 'PlanController@detailList');
    Route::get('plan/my_data', 'PlanController@myData');
    
    // Password Change
    Route::get('password', 'PasswordController@index')->name('password');
    Route::post('password', 'PasswordController@update');
    
    // inqueries
    Route::get('inqueries/lists', 'InqueriesController@lists');
    Route::resource('inqueries', 'InqueriesController');

    Route::get('paymentmethod', 'PaymentMethodController@index')->name("paymentmethod");
    Route::get('paymentmethod/redo/{id}', 'PaymentMethodController@redo');
    Route::get('/paymentmethod/result/{amount}/{id}', 'PaymentMethodController@result');
    Route::get('paymentmethod/fails', function () {
        return view('payment_method_fails');
    });

    // settlement
    Route::get('settlement', 'SettlementController@index');
    Route::get('settlement/lists', 'SettlementController@lists');
    Route::get('settlement/get_latest', 'SettlementController@getLatest');
    Route::get('settlement/pdf/{id}/{client}', 'SettlementController@downloadPdf');

    //payment information TODO:不要なら削除する予定
    //Route::get('paymentinformation', ['as' => 'paymentinformation', 'uses' => 'PaymentInformationController@index']);

    // Line@account manage
    Route::get('line_accounts/list', 'LineAccountsController@lists');
    Route::delete('line_accounts/delete/{id}', 'LineAccountsController@destroy');
    Route::put('line_accounts/select/{id}', 'LineAccountsController@selectAccount');
    Route::resource('line_accounts', 'LineAccountsController');
    
    Route::get('auto_answer_setting/lists', 'AutoAnswerSettingController@lists');
    
    Route::group(['middleware' => ['can:'.App\Role::ROLE_AUTOMATIC_RESPONSE_EDITABLE]], function () {
        // auto answer setting
        Route::post('auto_answer_setting/copy', 'AutoAnswerSettingController@copy');
        Route::post('auto_answer_setting/change_enable', 'AutoAnswerSettingController@changeEnable');
        Route::resource('auto_answer_setting', 'AutoAnswerSettingController', [
            'except' => ['create', 'edit']
        ]);
    });

    Route::resource('auto_answer_setting', 'AutoAnswerSettingController', [
        'except' => ['create', 'edit', 'store', 'update','destroy']
    ]);

    // -- MOTHER ACCOUNT -- START

    Route::get('accounts_analysis', 'MotherAccount\AccountsController@analysisIndex')->name('accounts_analysis.index');
    Route::get('accounts_analysisList', 'MotherAccount\AccountsController@analysisList');

    Route::get('accounts/list', 'MotherAccount\AccountsController@list');
    Route::get('friends/list', 'MotherAccount\FriendsController@list');
    Route::get('deliveries/list', 'MotherAccount\DeliveriesController@list');

    Route::post('deliveries/cancel_schedules/{id}', 'MotherAccount\DeliveriesController@cancelSchedules');
    Route::post('friends/visibility', 'MotherAccount\FriendsController@changeVisibility');

    Route::resources([
        'accounts' => 'MotherAccount\AccountsController',
        'friends' => 'MotherAccount\FriendsController',
        'deliveries' => 'MotherAccount\DeliveriesController'
    ]);

    // Route::get('blocks', 'MotherAccountController@blocksIndex');
    // Route::get('all_account_delivery/accountslist', 'AllAccountDeliveryController@accounts');
    // Route::get('all_account_delivery/taglist', 'AllAccountDeliveryController@tags');
    // Route::post('all_account_delivery/store', 'AllAccountDeliveryController@store');
    // Route::post('all_account_delivery/copy', 'AllAccountDeliveryController@copy');
    // Route::post('all_account_delivery/{ids}', 'AllAccountDeliveryController@deleteSchedule');

    // -- MOTHER ACCOUNT -- END
    Route::get('invitation/lists', 'InvitationEmailController@lists');
    Route::post('send_invitation/{id}', 'InvitationEmailController@sendEmails');
    Route::resource('invitation', 'InvitationEmailController');

    Route::get('tutorial/{index}', 'TutorialController@getCurrentContents');
    Route::post('skiptutorial', 'TutorialController@skipTutorial');
});
Route::post('sendtestmsg', 'LineBotController@sendMsg');
Route::post('/link', 'AccountInfoController@link');
Route::post('/line/bot/callback/{webhook_token}', 'LineBotController@lineHooks');

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
if (App::environment() == 'local') {
    Route::get('/line_api_debug', 'LineApiDebugController@show');
}

// URLクリック　計測タグ用
Route::get('urlclick', 'UrlClickController@index')->name('urlclick.route');

// コンバージョン　入り口用
Route::get('cv/{token}', 'ConversionTrackingController@prepare')->name('conversion.route');
// コンバージョン　計測タグ用
Route::get('cv', 'ConversionTrackingController@impression')->name('conversion.imagetag');

// クリック計測　計測用入り口
Route::get('click/{token}', 'ClickrateMeasurementController@index')->name('clickrate.route');

// HTTPステータスコードを引数に、該当するエラーページを表示させる
Route::get('error/{code}', function ($code) {
    abort($code);
});
