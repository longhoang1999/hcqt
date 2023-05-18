<?php

require_once 'web_builder.php';
use Cartalyst\Sentinel\Native\Facades\Sentinel;
// Route::get("create-role", function(){
//     $role = Sentinel::getRoleRepository()->createModel()->create([
//         'name' => 'ns_kiemtra',
//         'slug' => 'ns_kiemtra',
//     ]);
// });

// Route::get("delete-role", function(){
//     $arr = [];
//     $users = DB::table("users")->select("id")->get();
//     foreach($users as $us){
//         array_push($arr, $us->id);
//     }
//     foreach($arr as $value){
//         if($value != "1"){
//             $user = Sentinel::findById($value);
//             $role = Sentinel::findRoleByName('User');
//             $role->users()->attach($user); 
//         }
        
//     }
// });
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
Route::pattern('slug', '[a-z0-9- _]+');

Route::group(
    ['prefix' => 'admin', 'namespace' => 'Admin'],
    function () {

        // Error pages should be shown without requiring login
        Route::get(
            '404',
            function () {
                return view('admin/404');
            }
        );
        Route::get(
            '500',
            function () {
                return view('admin/500');
            }
        );
        // Lock screen
        Route::get('{id}/lockscreen', 'LockscreenController@show')->name('lockscreen');
        Route::post('{id}/lockscreen', 'LockscreenController@check')->name('lockscreen');
        // All basic routes defined here
        Route::get('login', 'AuthController@getSignin')->name('login');
        Route::get('signin', 'AuthController@getSignin')->name('signin');
        Route::post('signin', 'AuthController@postSignin')->name('postSignin');
        // link login token
        Route::get('signin-token', 'AuthController@getLoginToken')->name('getLoginToken');

        Route::post('signup', 'AuthController@postSignup')->name('admin.signup');
        Route::post('forgot-password', 'AuthController@postForgotPassword')->name('forgot-password');
        Route::get(
            'login2',
            function () {
                return view('admin/login2');
            }
        );


        // Register2
        Route::get(
            'register2',
            function () {
                return view('admin/register2');
            }
        );
        Route::post('register2', 'AuthController@postRegister2')->name('register2');

        // Forgot Password Confirmation
        //    Route::get('forgot-password/{userId}/{passwordResetCode}', 'AuthController@getForgotPasswordConfirm')->name('forgot-password-confirm');
        //    Route::post('forgot-password/{userId}/{passwordResetCode}', 'AuthController@getForgotPasswordConfirm');

        // Logout
        Route::get('logout', 'AuthController@getLogout')->name('admin.logout');

        // Account Activation
        Route::get('activate/{userId}/{activationCode}', 'AuthController@getActivate')->name('activate');
    }
);


Route::group(
    ['prefix' => 'admin', 'middleware' => 'operator', 'as' => 'admin.'],
    function () {
        // GUI Crud Generator
        Route::get('generator_builder', 'JoshController@builder')->name('generator_builder');
        Route::get('field_template', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@fieldTemplate');
        Route::post('generator_builder/generate', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@generate');
        // Model checking
        Route::post('modelCheck', 'ModelcheckController@modelCheck');

        // Dashboard / Index
        Route::get('/', 'JoshController@showHome')->name('dashboard');
        // crop demo
        Route::post('crop_demo', 'JoshController@cropDemo')->name('crop_demo');
        //Log viewer routes
        Route::get('log_viewers', 'Admin\LogViewerController@index')->name('log-viewers');
        Route::get('log_viewers/logs', 'Admin\LogViewerController@listLogs')->name('log_viewers.logs');
        Route::delete('log_viewers/logs/delete', 'Admin\LogViewerController@delete')->name('log_viewers.logs.delete');
        Route::get('log_viewers/logs/{date}', 'Admin\LogViewerController@show')->name('log_viewers.logs.show');
        Route::get('log_viewers/logs/{date}/download', 'Admin\LogViewerController@download')->name('log_viewers.logs.download');
        Route::get('log_viewers/logs/{date}/{level}', 'Admin\LogViewerController@showByLevel')->name('log_viewers.logs.filter');
        Route::get('log_viewers/logs/{date}/{level}/search', 'Admin\LogViewerController@search')->name('log_viewers.logs.search');
        Route::get('log_viewers/logcheck', 'Admin\LogViewerController@logCheck')->name('log-viewers.logcheck');
        //end Log viewer
        // Activity log
        Route::get('activity_log/data', 'JoshController@activityLogData')->name('activity_log.data');
        //    Route::get('/', 'JoshController@index')->name('index');
    }
);

Route::get('test', function() {
    // Sentinel::registerAndActivate(array(
    //     'email'     => 'admin@admin.com',
    //     'password'  => '123'
    // ));
    $user = Sentinel::findById(4918);
    Sentinel::login($user);

    dd(Sentinel::getUser()->id);
});

Route::get('create-role-bgh', function() {
    $Perstr = "1|2|3|4|5|6|7|11|12|23|24|25|26|27|28|29|30|31|32|33|34|38|39|47|48|49|50|51|52|53|56|75|76|77|78";
    $role = Sentinel::getRoleRepository()->createModel()->create([
        'name'  => 'bgh',
        'slug'  => 'bgh',
    ]);
    $roles = DB::table("roles")->where("name", "bgh")->update([
        'perStr'    => $Perstr
    ]);

});

Route::get('create-role-bpth', function() {
    $Perstr = "1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|47|48|49|50|51|52|53|75|76|77|78|";
    $role = Sentinel::getRoleRepository()->createModel()->create([
        'name'  => 'bpth',
        'slug'  => 'bpth',
    ]);
    $roles = DB::table("roles")->where("name", "bpth")->update([
        'perStr'    => $Perstr
    ]);
});
Route::get('create-role-tdv', function() {
    $Perstr = "1|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|40|41|42|43|44|45|46|47|48|49|50|51|52|53|55|58|59|75|76|77|78|";
    $role = Sentinel::getRoleRepository()->createModel()->create([
        'name'  => 'tdv',
        'slug'  => 'tdv',
    ]);
    $roles = DB::table("roles")->where("name", "tdv")->update([
        'perStr'    => $Perstr
    ]);
});
Route::get('create-role-nv', function() {
    $Perstr = "1|13|14|15|18|19|20|24|25|26|27|28|29|30|31|32|33|34|47|48|49|50|51|52|53|59|75|76|";
    $role = Sentinel::getRoleRepository()->createModel()->create([
        'name'  => 'nv',
        'slug'  => 'nv',
    ]);
    $roles = DB::table("roles")->where("name", "nv")->update([
        'perStr'    => $Perstr
    ]);
});
Route::get('create-role-thuthu', function() {
    $Perstr = "1|13|14|15|18|19|20|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|46|47|48|49|50|51|52|53|57|59|60|61|62|63|64|65|66|67|68|69|70|71|72|73|74|75|76|";
    $role = Sentinel::getRoleRepository()->createModel()->create([
        'name'  => 'thuthu',
        'slug'  => 'thuthu',
    ]);
    $roles = DB::table("roles")->where("name", "thuthu")->update([
        'perStr'    => $Perstr
    ]);
});
Route::get('create-role-xlvb', function() {
    $Perstr = "1|13|14|15|18|19|20|24|25|26|27|28|29|30|31|32|33|34|38|39|40|41|42|43|44|45|47|48|48|50|51|52|53|54|59|75|76|";
    $role = Sentinel::getRoleRepository()->createModel()->create([
        'name'  => 'xlvb',
        'slug'  => 'xlvb',
    ]);
    $roles = DB::table("roles")->where("name", "xlvb")->update([
        'perStr'    => $Perstr
    ]);
});

Route::group(
    ['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'operator', 'as' => 'admin.'],
    function () {

        // User Management
        Route::group(
            ['middleware' => 'admin'],
            function () {
                Route::get('data', 'UsersController@data')->name('users.data');
                Route::get('{user}/delete', 'UsersController@destroy')->name('users.delete');
                Route::get('{user}/confirm-delete', 'UsersController@getModalDelete')->name('users.confirm-delete');
                Route::get('{user}/restore', 'UsersController@getRestore')->name('restore.user');
                //        Route::post('{user}/passwordreset', 'UsersController@passwordreset')->name('passwordreset');
                Route::post('passwordreset', 'UsersController@passwordreset')->name('passwordreset');   
                Route::resource('users', 'UsersController');     
                Route::post('change-pass', 'UsersController@changePass')->name('changePass'); 
                Route::post('update-user', 'UsersController@updateUser')->name('updateUser');       
            }            
        );
        
        
        
        /************
     * bulk import
    ****************************/
        Route::get('bulk_import_users', 'UsersController@import');
        Route::post('bulk_import_users', 'UsersController@importInsert');
        /****************
     bulk download
    **************************/
        Route::get('download_users/{type}', 'UsersController@downloadExcel');

        Route::get('deleted_users', ['before' => 'Sentinel', 'uses' => 'UsersController@getDeletedUsers'])->name('deleted_users');

        // Email System
        Route::group(
            ['prefix' => 'emails'],
            function () {
                Route::get('compose', 'EmailController@create');
                Route::post('compose', 'EmailController@store');
                Route::get('inbox', 'EmailController@inbox');
                Route::get('sent', 'EmailController@sent');
                Route::get('{email}', ['as' => 'emails.show', 'uses' => 'EmailController@show']);
                Route::get('{email}/reply', ['as' => 'emails.reply', 'uses' => 'EmailController@reply']);
                Route::get('{email}/forward', ['as' => 'emails.forward', 'uses' => 'EmailController@forward']);
            }
        );
        Route::resource('emails', 'EmailController');

        // Role Management
        Route::group(
            ['prefix' => 'roles'],
            function () {
                Route::get('{group}/delete', 'RolesController@destroy')->name('roles.delete');
                Route::get('{group}/confirm-delete', 'RolesController@getModalDelete')->name('roles.confirm-delete');
                Route::get('{group}/restore', 'RolesController@getRestore')->name('roles.restore');
            }
        );
        Route::resource('roles', 'RolesController');
       
        // Route for Project
        Route::group(
            ['namespace' => 'Project', 'middleware' => ['super_check:admin']],
            function () {
                // Route for Lịch công tác
                Route::group(
                    ['prefix' => 'lich-cong-tac', 'as' => 'lichcongtac.', 'namespace' => 'Lichcongtac'],
                    function () {
                        // Route for Lịch công tác tuần
                        Route::group(['prefix' => 'lich-cong-tac-tuan', 'as' => 'lichcongtactuan.',
                            'namespace' => 'Lichcongtactuan'],
                            function () {
                                Route::get('index', 'LichcongtactuanController@index')->name('index');
                                
                            }
                        );
                    }
                );

            }
        );

        



        

        Route::get(
            'crop_demo',
            function () {
                return redirect('admin/imagecropping');
            }
        );


        /* laravel example routes */
        // Charts
        Route::get('laravel_charts', 'ChartsController@index')->name('laravel_charts');
        Route::get('database_charts', 'ChartsController@databaseCharts')->name('database_charts');

        // datatables
        Route::get('datatables', 'DataTablesController@index')->name('index');
        Route::get('datatables/data', 'DataTablesController@data')->name('datatables.data');

        // datatables
        Route::get('jtable/index', 'JtableController@index')->name('index');
        Route::post('jtable/store', 'JtableController@store')->name('store');
        Route::post('jtable/update', 'JtableController@update')->name('update');
        Route::post('jtable/delete', 'JtableController@destroy')->name('delete');



        // SelectFilter
        Route::get('selectfilter', 'SelectFilterController@index')->name('selectfilter');
        Route::get('selectfilter/find', 'SelectFilterController@filter')->name('selectfilter.find');
        Route::post('selectfilter/store', 'SelectFilterController@store')->name('selectfilter.store');

        // editable datatables
        Route::get('editable_datatables', 'EditableDataTablesController@index')->name('index');
        Route::get('editable_datatables/data', 'EditableDataTablesController@data')->name('editable_datatables.data');
        Route::post('editable_datatables/create', 'EditableDataTablesController@store')->name('store');
        Route::post('editable_datatables/{id}/update', 'EditableDataTablesController@update')->name('update');
        Route::get('editable_datatables/{id}/delete', 'EditableDataTablesController@destroy')->name('editable_datatables.delete');

        //    # custom datatables
        Route::get('custom_datatables', 'CustomDataTablesController@index')->name('index');
        Route::get('custom_datatables/sliderData', 'CustomDataTablesController@sliderData')->name('custom_datatables.sliderData');
        Route::get('custom_datatables/radioData', 'CustomDataTablesController@radioData')->name('custom_datatables.radioData');
        Route::get('custom_datatables/selectData', 'CustomDataTablesController@selectData')->name('custom_datatables.selectData');
        Route::get('custom_datatables/buttonData', 'CustomDataTablesController@buttonData')->name('custom_datatables.buttonData');
        Route::get('custom_datatables/totalData', 'CustomDataTablesController@totalData')->name('custom_datatables.totalData');

        //tasks section
        Route::post('task/create', 'TaskController@store')->name('store');
        Route::get('task/data', 'TaskController@data')->name('data');
        Route::post('task/{task}/edit', 'TaskController@update')->name('update');
        Route::post('task/{task}/delete', 'TaskController@delete')->name('delete');
    }
);



// Remaining pages will be called from below controller method
// in real world scenario, you may be required to define all routes manually

Route::group(
    ['prefix' => 'admin', 'middleware' => 'admin'],
    function () {
        Route::get('{name?}', 'JoshController@showView');
    }
);


Route::get('login', 'FrontEndController@getLogin')->name('login');
Route::post('login', 'FrontEndController@postLogin')->name('login');
Route::get('register', 'FrontEndController@getRegister')->name('register');
Route::post('register', 'FrontEndController@postRegister')->name('register');
Route::get('activate/{userId}/{activationCode}', 'FrontEndController@getActivate')->name('activate');
Route::get('forgot-password', 'FrontEndController@getForgotPassword')->name('forgot-password');
Route::post('forgot-password', 'FrontEndController@postForgotPassword');

// Social Logins
Route::get('facebook', 'Admin\FacebookAuthController@redirectToProvider');
Route::get('facebook/callback', 'Admin\FacebookAuthController@handleProviderCallback');

Route::get('linkedin', 'Admin\LinkedinAuthController@redirectToProvider');
Route::get('linkedin/callback', 'Admin\LinkedinAuthController@handleProviderCallback');

Route::get('google', 'Admin\GoogleAuthController@redirectToProvider');
Route::get('google/callback', 'Admin\GoogleAuthController@handleProviderCallback');


// Forgot Password Confirmation
Route::post('forgot-password/{userId}/{passwordResetCode}', 'FrontEndController@postForgotPasswordConfirm');
Route::get('forgot-password/{userId}/{passwordResetCode}', 'FrontEndController@getForgotPasswordConfirm')->name('forgot-password-confirm');
// My account display and update details
Route::group(
    ['middleware' => 'user'],
    function () {
        Route::put('my-account', 'FrontEndController@update');
        Route::get('my-account', 'FrontEndController@myAccount')->name('my-account');
    }
);
// Email System
Route::group(
    ['prefix' => 'user_emails'],
    function () {
        Route::get('compose', 'UsersEmailController@create');
        Route::post('compose', 'UsersEmailController@store');
        Route::get('inbox', 'UsersEmailController@inbox');
        Route::get('sent', 'UsersEmailController@sent');
        Route::get('{email}', ['as' => 'user_emails.show', 'uses' => 'UsersEmailController@show']);
        Route::get('{email}/reply', ['as' => 'user_emails.reply', 'uses' => 'UsersEmailController@reply']);
        Route::get('{email}/forward', ['as' => 'user_emails.forward', 'uses' => 'UsersEmailController@forward']);
    }
);
Route::resource('user_emails', 'UsersEmailController');
Route::get('logout', 'FrontEndController@getLogout')->name('logout');
// contact form
Route::post('contact', 'FrontEndController@postContact')->name('contact');

// frontend views
Route::get(
    '/',
    ['as' => 'home', function () {
        //return view('index');
        return redirect('admin');
    }]
);

Route::get('checkduplicate', 'Admin\DuplicateController@checkduplicate')->name('checkduplicate');

Route::get('blog', 'BlogController@index')->name('blog');
Route::get('blog/{slug}/tag', 'BlogController@getBlogTag');
Route::get('blogitem/{slug?}', 'BlogController@getBlog');
Route::post('blogitem/{blog}/comment', 'BlogController@storeComment');

//news
Route::get('news', 'NewsController@index')->name('news');
Route::get('news/{news}', 'NewsController@show')->name('news.show');

Route::get('{name?}', 'FrontEndController@showFrontEndView');
// End of frontend views
