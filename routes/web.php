<?php

use Illuminate\Http\Request;

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

/**
 * Public Routes
 */
Route::get('/', function () {
    return view('welcome');
})->middleware('guest');





//THE ROUTE BELOW FOR PRICING IS NOT VALID AND THUS COMMENTED OFF, THE SUBSCRIPTION PLANS ARE LOADED FROM THE DATABASE AND NOT ON THE CLIENT
//SINCE THEY ARE LOADED FROM THE DATABASE IT AUTOMATICALLY LINKS TO THE PAYMENT ROUTE WITH THE CORRECT PLAN DATA
//THE CORRECT PRICING ROUTES HAS BEEN SPECIFIED UNDER THE USER ROUTES
//TO GET THE PRICING DATA RUN THE COMMAND php artisan db:seed --class=bugfix
//Route::get('/pricing', function () { return view('pricing'); });


Route::get('/pricing', "SubscriptionController@showSubscriptions")->name('subscriptions'); //THIS IS THE CORRECT ROUTE

//Email subscription
Route::post('/submailinglist', 'MailSubscriptionController@mailStore');


//Guest Routes
// Guest Onboarding
// Route::get('guest/create_estimate', 'GuestController@step1')->middleware('guest');
// Route::post('guest/project/create', 'GuestController@createproject')->middleware('guest');
// Route::get('guest/create/estimate', 'GuestController@estimatecreate')->middleware('guest');
// Route::post('guest/save/estimate', 'GuestController@estimatesave')->middleware('guest');
// Route::get('/guest/create_project/', function () { return view('createproject'); });
Route::get('guest/create/step1', 'GuestController@step1')->middleware('guest');
Route::post('guest/save/step1', 'GuestController@createstep1')->middleware('guest');
Route::get('guest/create/step2', 'GuestController@createstep2')->middleware('guest');
Route::post('/guest/save/step2', 'GuestController@savestep2')->middleware('guest');
Route::get('/guest/create/step3', 'GuestController@createstep3')->middleware('guest');
Route::post('/guest/save/step3', 'GuestController@savestep3')->middleware('guest');
Route::get('/guest/create/step4', 'GuestController@createstep4')->middleware('guest');
Route::post('/guest/save/step4', 'GuestController@savestep4')->middleware('guest');

Route::get('/guest/invoice/review', 'InvoiceController@review');

/* Track Project */
Route::get('/guest/track/', 'ProjectController@acceptproject');
Route::post('/guest/track/project', 'ProjectController@selectproject');
Route::get('/guest/track/{trackCode}', 'ProjectController@showproject');

//Doc view pdf
Route::get('/guest/track/{trackCode}/dynamic_pdf','ProjectController@dynamicPDF');
//Route::get('/guest/track/{id}/dynamic_pdf','DynamicPDFController@index')->middleware('guest');



Route::get('/guest/contact', function () {
    return view('guests/contact_support');
});
Route::post('/guest/process_contact_form', "GuestController@process_contact_form");

// Utility Routes
Route::get('/countries', 'DataController@countries');
Route::get('/states/{id}', 'DataController@states');
Route::get('/currencies', 'DataController@currencies');

// Authentication Routes
Auth::routes(['verify' => true]);
Route::post('/password/create', 'AuthController@create_password')->name('password.create');
// =========================== Not necessary - has been handled by Auth::routes() above
Route::get('/passwordresetconfirmation', function () {
    return view('passwordresetconfirmation');
});
Route::get('/passwordresetmessage', function () {
    return view('passwordresetmessage');
});
Route::get('/passwordreset', function () {
    return view('passwordreset');
});
Route::get('/password/changed', function() {
    return view('passwordchanged');
});
// =========================================
// Generate PDF from Invoice
Route::get('/invoice/pdf', function() {
    $pdf = PDF::loadView('invoice_view_pdf');
    return $pdf->download('lancers_invoice.pdf');
});

// Client create password
Route::get('/email/client', function(Request $request) {
    if ($request->query('email')) {
        return view('auth.passwords.create')->withEmail($request->query('email'));
    }
    abort(404);
});

/**
 * Protected Routes
 */
Route::group(['middleware' => 'auth:web'], function() {
    // Auth
    Route::get('/logout', 'AuthController@logout')->name('logout');

    // Dashboard Routes

    Route::get('/dashboard', 'HomeController@index')->name('dashboard')->middleware('auth');
    Route::get('/dashboard/profile', 'ProfileController@index')->name('dashboard-profile');
    Route::get('/dashboard/profile/settings', 'ProfileController@index')->middleware('auth')->name('dashboard-profile');
    Route::get('/dashboard/profile/view', 'ProfileController@userProfileDetails')->name('user-profile');
    Route::post('/dashboard/edit/profile/image', "ProfileController@updateImage")->middleware('auth')->name('Profile-Image');
    Route::get('/dashboard/emails/settings', "emailsettingsController@index")->middleware('auth');



    // Route::get('/dashboard', 'HomeController@index')->name('dashboard');
    // Route::get('/dashboard/profile/view', 'ProfileController@userProfileDetails')->name('user-profile');
    //Route::get('/dashboard/profile', function () { return view('user_profile'); }); This is another alternative to the dashboard/profile
    //Route::get('users/subscribe/{txref}', "SubscriptionController@subscribeUser");
    //Route::put('/users/settings/emails', "emailsettingsController@updateEmailSettings")->middleware('auth')->name('SET-EMAIL');
    //Route::post('/users/edit/profile/company', "ProfileController@editProfile")->middleware('auth')->name('edit-company');
    // Route::post('/users/edit/profile/personal', "ProfileController@editProfileUser")->middleware('auth')->name('edit-profile');
    // User Routes
    Route::post('/users/edit/profile', "ProfileController@editProfile")->middleware('auth')->name('edit-profile');
    Route::put('/users/settings/emails', "emailsettingsController@updateEmailSettings")->middleware('auth')->name('SET-EMAIL');
    Route::post('/users/edit/profile/company', "ProfileController@editProfile")->middleware('auth')->name('edit-company');
    Route::post('/users/edit/profile/personal', "ProfileController@editProfileUser")->middleware('auth')->name('edit-profile');
    Route::post('/users/edit/profile/image', "ProfileController@updateImage")->middleware('auth')->name('Profile-Image');

    Route::get('/users/subscriptions', "SubscriptionController@showSubscriptions")->middleware('auth')->name('subscriptions');
    Route::get('/users/subscriptions/{planId}', "SubscriptionController@subscribeUser")->middleware('auth');
    // Route::get('/users/subscription', "SubscriptionController@showSubscriptions");
    Route::get('users/subscribe/{txref}', "SubscriptionController@subscribeUser");
    Route::get('/users/view/subscriptions', "SubscriptionController@showPlan")->middleware('auth');

    Route::get('/user/notifications', 'NotificationsController@notifications');
    Route::put('/user/notifications/read/{$id}', 'NotificationsController@markAsRead');
    Route::put('/user/notifications/read/all', 'NotificationsController@markAllAsRead');


    // Project Routes
    Route::get('/projects', 'ProjectController@listGet');
    Route::get('/project/status', 'ProjectController@listGet');
    Route::get('/project/track', function(){ return view('trackproject'); });
    Route::get('/edit-project/{id}', 'ProjectController@edit');
    Route::post('/edit-project-save/{id}', 'ProjectController@update');
    Route::get('/view-project/{id}', 'ProjectController@view');
    Route::post('/completed-project/{id}', 'ProjectController@complete');
    Route::post('/pending-project/{id}', 'ProjectController@pending');
     Route::post('/delete-project/{id}', 'ProjectController@delete');
    // Task Routes
    Route::get('/project/tasks', 'TaskController@getAllTasks');
    Route::post('/project/task/create', 'TaskController@store');

    Route::get('/project/collaborators', 'CollaboratorController@getAllCollaborators');
    Route::post('/project/collaborator/create', 'CollaboratorController@store');
    //Crud for Collaborator
    Route::get('/project/collaborator/remove/{id}', 'CollaboratorController@delete');
    Route::get('/project/collaborator/edit/{id}', 'CollaboratorController@edit');
    Route::post('/project/collaborator/update/{id}', 'CollaboratorController@update');
    Route::get('/project/collaborator/view/{id}', 'CollaboratorController@view');


    // Client Routes
    Route::get('/clients', 'ClientController@listGet');
    Route::get('/clients/add', 'ClientController@show');
    Route::post('/client/add', 'ClientController@store');
    Route::get('/client-info', function () {
        return view('client-info');
    });


    Route::get('/clients/{client}/invoices/{invoice}', 'InvoiceController@clientInvoice');
    Route::get('/client/add', function() {
         return view('addclients');
    });

    Route::get('/clients/view/{id}', 'ClientController@viewClient')->name('viewClient');
    Route::get('/clients/{client}/edit', 'ClientController@edit');
    Route::put('/clients/edit', 'ClientController@update');
    Route::delete('/clients/{client}/delete', 'ClientController@delete');


    //Invoice routes
    // Route::resource('invoices', 'InvoiceController');
    Route::post('/invoice/send', 'InvoiceController@sendinvoice');

    Route::get('/invoice/pay/{txref}', 'InvoiceController@pay');
    Route::get('/invoices/{invoice}/getpdf', 'InvoiceController@getPdf');
    // Route::get('/invoice', function () { return view('invoice_view'); });
    Route::get('/invoice_sent', function () {
        return view('invoice_sent');
    });
    Route::post('/invoice/send/{id}', 'InvoiceController@send');
    Route::get('/invoice-view', function () {
        return view('invoice-view');
    });
    Route::get('/client-doc-view', function () {
        return view('client-doc-view');
    });
    Route::get('client-send-invoice-active', function () {
        return view('client-send-invoice-active');
    });
    Route::get('client-send-invoice', function () {
        return view('client-send-invoice');
    });
    Route::get('invoice/remove/{id}', 'InvoiceController@delete');
    Route::get('invoice/edit/{id}', 'InvoiceController@edit');
    Route::post('invoice/update/{id}', 'InvoiceController@update');
    Route::get('user/{id}', function ($id) {
        return 'User ' . $id;
    });


    // Estimate Routes
    Route::get('/estimates', 'EstimateController@index')->middleware('auth');
    Route::post('estimates', 'EstimateController@store')->middleware('auth');
    Route::put('/estimates/{estimate}', 'EstimateController@update')->middleware('auth');
    Route::delete('/estimates/{estimate}', 'EstimateController@destroy')->middleware('auth');
    Route::get('/estimates/{estimate}', 'EstimateController@show')->middleware('auth');
    Route::get('/estimate/create/step1', 'EstimateController@step1');

    // Route::get('/estimate/create/step2', 'EstimateController@step2');
    // Route::get('/estimate/create/step3', 'EstimateController@step3');
    // Route::get('/estimate/create/step4', 'EstimateController@step4');
    // Route::get('/estimate/create/step5', 'EstimateController@step5');
    // Route::get('/estimate/create/step6', 'EstimateController@step6');

    Route::get('/estimate/create/step2', 'EstimateController@step2');

    Route::get('/estimate/create/step3', 'EstimateController@step3');

    Route::get('/estimate/create/step4', 'EstimateController@step4');

    Route::get('/estimate/create/step5', 'EstimateController@step5');


    Route::post('/estimate/create/step2', 'EstimateController@step2');

    Route::post('/estimate/create/step3', 'EstimateController@step3');

    Route::post('/estimate/create/step4', 'EstimateController@step4');

    Route::post('/estimate/create/step5', 'EstimateController@step5');
    Route::post('/estimate/create/step6', 'EstimateController@step6');


    // Route::get('/estimates', 'EstimateController@index')->middleware('auth');
    // Route::post('estimates', 'EstimateController@store')->middleware('auth');
    // Route::get('/estimates/{estimate}', 'EstimateController@show')->middleware('auth');
    // Route::put('/estimates/{estimate}', 'EstimateController@update')->middleware('auth');
    // Route::delete('/estimates/{estimate}', 'EstimateController@destroy')->middleware('auth');

    // Route::get('/estimate/create', 'EstimateController@step1');


    // Task Routes
    Route::get('/tasks', 'TaskController@getAllTasks');
    Route::get('/task/edit/{id}', 'TaskController@edit');
    Route::post('/task/update/{id}', 'TaskController@update');
    Route::get('/task/remove/{id}', 'TaskController@delete');
    Route::get('/tasks/{id}', 'TaskController@getTask');
    Route::post('/tasks', 'TaskController@createTask');
    Route::put('/tasks/{id}', 'TaskController@updateTask');
    Route::delete('/tasks/{id}', 'TaskController@deleteTask');
    Route::put('/tasks/{task}/team', 'TaskController@addTeam');

    // Contract Routes
    Route::get('/contracts', function() {
        return view('contracts');
    });
    Route::post('/contracts/{project_id}/{template_id}', 'ContractControler@store')->name('create.contract');
    Route::put('/contracts/{project_id}/{id}')->name('edit.contract');
    Route::delete('/contracts/{project_id}/{id}')->name('delete.contract');

    // Trasaction/Payment/Subscription Routes
    // Route::post('/pay', 'RaveController@initialize')->name('pay');
    // Route::get('payment/{type}/{ref?}', 'PaymentContoller@create');
    // Route::post('/rave/callback', 'RaveController@callback')->name('callback');
    // Route::resource('transactions', 'TransactionsController');
    // Route::get('/transactions', 'TransactionsController@index');
    Route::resource('transactions', 'TransactionsController');
    Route::get('/transactions', 'TransactionsController@index');
    Route::get('payment/subscription/{type}', 'PaymentContoller@create');
    Route::get('payment/invoice/{ref}', 'PaymentContoller@invoice'); //ref is the timestamp value of the created_at field
    //Invoice routes
    //Route::resource('invoices', 'InvoiceController');
    // Route::post('invoices/send', 'InvoiceController@sendinvoice');
    // Route::get('/invoices', 'InvoiceController@list');
    Route::get('/invoices', 'InvoiceController@listGet');
    Route::get('/invoice/review', 'InvoiceController@review');
    //Route::get('/invoices/{invoice}/getpdf', 'InvoiceController@getPdf');
    //Route::get('/invoice/pay/{txref}', 'InvoiceController@pay');
    Route::get('/invoice', function () {
        return view('invoice_view');
    });
    // Route::get('/invoice_sent', function () {
    //     return view('invoice_sent');
    // });
    // Route::get('/invoice-view', function () {
    //     return view('invoice-view');
    // });
    // Route::get('/client-doc-view', function () {
    //     return view('client-doc-view');
    // });
    
    
    // Route::get('/invoice', function () {
    //     return view('invoice_view');
    // });
    // Route::get('/invoice_sent', function () {
    //     return view('invoice_sent');
    // });
    // Route::get('/invoice-view', function () {
    //     return view('invoice-view');
    // });
    // Route::get('/client-doc-view', function () {
    //     return view('client-doc-view');
    // });



    //Proposals
    Route::get('/proposals', function() {
        return view('proposals');
    });

    //Contact
    Route::get('/contact', function() {
        return view('contact_support');
    });
    Route::post('/process_contact_form', function() {
        return view('process_contact_form');
    });


    // Others

    Route::get('/notifications', 'NotificationsController@notifications');

    Route::get('user/notifications', 'NotificationsController@notifications');
    Route::put('user/notifications/read/{$id}', 'NotificationsController@markAsRead');
    Route::put('user/notifications/read/all', 'NotificationsController@markAllAsRead');

    Route::get('client-active', function() {
        return view('client-active');
    });
    Route::get('client-nav-bar', function() {
        return view('client-nav-bar');
    });
});

    //Invite new user to collaborate . Form submission
    Route::get('project/invite', 'InviteController@invite')->name('invite');
    //Process the form submission and send the invitation
    Route::post('project/invite/send', 'InviteController@process')->name('process');
    //Create account
    Route::get('register/{token}', 'InviteController@register');
    // Accept the invitation. {token} is a required parameter that will be exposed to us in the controller method
    Route::post('accept/{token}', 'InviteController@accept')->name('accept');


    Route::get('/run/{command}', function ($command) {
        $test = \Artisan::call($command);
        dd($test);
    });
