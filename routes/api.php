<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\GetWeatherController;
use App\Models\Folder;
use App\Models\User;
use App\Mail\NotifyMail;
use App\Notifications\ConfirmBill;
use Illuminate\Mail;
use Illuminate\Support\Facades\Mail as FacadesMail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);

Route::get('/test', [FolderController::class, 'test']);

Route::group(['prefix' => 'user'], function(){
    Route::get('/info', [UserController::class, 'userInfo']);
});

Route::group(['prefix' => 'folder'], function () {
    Route::post('/add', [FolderController::class, 'addFolder']);
    Route::post('/edit', [FolderController::class, 'editFolder']);
    Route::post('/delete', [FolderController::class, 'deleteFolder']);
});

Route::group(['prefix' => 'set'], function () {
    Route::post('/add', [SetController::class, 'addSet']);
    Route::post('/edit/{id}', [SetController::class, 'editSet']);
    Route::post('/delete', [SetController::class, 'deleteSet']);
    Route::get('/completed', [SetController::class, 'completedSets']);
    Route::get('/created', [SetController::class, 'createdSets']);
    Route::get('/all', [SetController::class, 'allSets']);
    Route::get('/no-folder', [SetController::class, 'noFolderSets']);
});

Route::group(['prefix' => 'card'], function () {
    Route::post('/add', [CardController::class, 'addCard']);
    //Route::post('/update', [CardController::class, 'updateCard']);
    Route::post('/delete/{id}', [CardController::class, 'deleteCard']);
});

Route::group(['prefix' => 'cart'], function () {
    Route::get('/get', [SetController::class, 'getCart']);
    Route::post('/buy', [UserController::class, 'buy']);
});

Route::get('/recentSets', [UserController::class, 'recentSets']);
Route::get('/listSetsByTime', [UserController::class, 'listSetsByTime']);
Route::post('/createOrUpdateSet', [SetController::class, 'createOrUpdateSet']);
Route::post('/setToFolder', [SetController::class, 'setToFolder']);
Route::get('/listFolders', [FolderController::class, 'listFolders']);
Route::post('/createOrUpdateFolder', [FolderController::class, 'createOrUpdateFolder']);
Route::get('/setDetail', [SetController::class, 'setDetail']);
Route::get('/folderDetail', [FolderController::class, 'folderDetail']);
Route::get('/multipleChoiceGame', [SetController::class, 'multipleChoiceGame']);
Route::get('/fillBlankGame', [SetController::class, 'fillBlankGame']);


Route::get('send-mail', [MailController::class, 'sendEmail']);
Route::get('getWeather', [GetWeatherController::class, 'getWeather']);
Route::get('search', [SetController::class, 'search']);
Route::get('completedSets', [SetController::class, 'completedSets']);
Route::post('updateCard', [CardController::class, 'updateCard']);

Route::get('notification', [UserController::class, 'getNoti']);
Route::get('markAsRead', [UserController::class, 'markAsRead']);

Route::get('testNotify', function () {
    //User::find(4)->notify(new ConfirmBill("anh yeu em nhieu lam"));
    $user = User::find(4);
    $data['notifications'] = $user->notifications;
    return view('welcome', $data);
});


Route::get('count', function () {
    return count(User::find(4)->notifications);
});


Route::post('import', [UserController::class, 'importExcel']);
Route::get('getInfo', [UserController::class, 'getInfo']);
