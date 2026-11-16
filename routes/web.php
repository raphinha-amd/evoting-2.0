<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboarController;
use App\Http\Controllers\KandidatController;
use App\Http\Controllers\PemiluController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UkmController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/login');

Route::get('/helloworld', function () {
    return 'helloworld';
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'indexLogin')->name('view.login');
    Route::post('/login', 'login')->name('post.login');
    Route::post('/logout', 'logout')->name('post.logout')->middleware('auth');
});

Route::controller(ProfileController::class)->group(function () {
    Route::get('/profile/me', 'profile')->name('profile')->middleware('auth');
    Route::post('/update/profile/me', 'updateProfile')->name('profile.update')->middleware('auth');
    Route::post('/update-image/profile/me', 'updateProfileImage')->name('profile.update.image')->middleware('auth');
});

Route::group(['middleware' => ['auth', 'role:admin'], 'prefix' => 'admin'], function () {
    Route::get('/dashboard', [DashboarController::class, 'dashboard'])->name('admin.dashboard');
    Route::controller(\App\Http\Controllers\SettingController::class)->group(function () {
        Route::get('/settings', 'index')->name('admin.settings.index');
        Route::delete('/settings/nonactive-user', 'nonactiveUser')->name('admin.settings.nonactive-user');
        Route::delete('/settings/nonactive-class', 'nonactiveClass')->name('admin.settings.nonactive-class');
    });

    Route::prefix('manage')->group(function () {
        Route::prefix('prodi')->controller(ProdiController::class)->group(function () {
            Route::get('/', 'manageProdi')->name('admin.manage.prodi');
            Route::post('/add', 'addProdi')->name('admin.manage.prodi.add');
            Route::post('/{id}/update', 'updateProdi')->name('admin.manage.prodi.update');
            Route::delete('/{id}/delete', 'deleteProdi')->name('admin.manage.prodi.delete');

            Route::get('/data', 'data')->name('admin.prodi.data');
            Route::get('/{id}/data', 'dataById')->name('admin.prodi.data.id');
        });

        Route::prefix('roles')->controller(RoleController::class)->group(function () {
            Route::get('/', 'manageRole')->name('admin.manage.roles');
            Route::post('/add', 'addRole')->name('admin.manage.roles.add');
            Route::post('/{id}/update', 'updateRole')->name('admin.manage.roles.update');
            Route::delete('/{id}/delete', 'deleteRole')->name('admin.manage.roles.delete');

            Route::get('/data', 'data')->name('admin.roles.data');
            Route::get('/{id}/data', 'dataById')->name('admin.roles.data.id');
        });

        Route::prefix('ukm')->controller(UkmController::class)->group(function () {
            Route::get('/', 'manageUkm')->name('admin.manage.ukm');
            Route::post('/add', 'addUkm')->name('admin.manage.ukm.add');
            Route::post('/{id}/update', 'updateUkm')->name('admin.manage.ukm.update');
            Route::delete('/{id}/delete', 'deleteUkm')->name('admin.manage.ukm.delete');

            Route::get('/data', 'data')->name('admin.ukm.data');
            Route::get('/{id}/data', 'dataById')->name('admin.ukm.data.id');

            Route::get('/{id}/members/data', 'members')->name('admin.manage.ukm.members.data');
            Route::post('/{id}/members/import', 'importMembers')->name('admin.manage.ukm.members.import');
            Route::post('/{id}/members/add', 'addMemberManual')->name('admin.manage.ukm.members.add');
            Route::delete('/{id}/members/{userId}/remove', 'removeMember')->name('admin.manage.ukm.members.remove');
        });

        Route::prefix('class')->controller(ClassController::class)->group(function () {
            Route::get('/', 'manageClass')->name('admin.manage.class');
            Route::post('/add', 'addClass')->name('admin.manage.class.add');
            Route::post('/import', 'importClass')->name('admin.manage.class.import');
            Route::post('/{id}/update', 'updateClass')->name('admin.manage.class.update');
            Route::delete('/{id}/delete', 'deleteClass')->name('admin.manage.class.delete');

            Route::get('/data', 'data')->name('admin.class.data');
            Route::get('/{id}/data', 'dataById')->name('admin.class.data.id');
        });

        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', 'manageUser')->name('admin.manage.users');
            Route::post('/add', 'addUser')->name('admin.manage.user.add');
            Route::post('/import', 'importUser')->name('admin.manage.user.import');
            Route::get('/export', 'exportUser')->name('admin.manage.user.export');
            Route::post('/{id}/update', 'updateUser')->name('admin.manage.user.update');
            Route::delete('/{username}/delete', 'deleteUser')->name('admin.manage.user.delete');

            Route::get('/data', 'data')->name('admin.user.data');
            Route::get('/data/admin', 'dataAdmin')->name('admin.user.data.admin');
            Route::get('/{id}/data', 'dataById')->name('admin.user.data.id');
        });

        Route::prefix('pemilu')->group(function () {
            Route::controller(PemiluController::class)->group(function () {
                Route::get('', 'managePemilu')->name('admin.manage.pemilu');
                Route::post('/add', 'addPemilu')->name('admin.manage.pemilu.add');
                Route::post('/{slug}/edit', 'updatePemilu')->name('admin.manage.pemilu.edit');
                Route::delete('/{slug}/delete', 'deletePemilu')->name('admin.manage.pemilu.delete');

                Route::get('/data', [PemiluController::class, 'data'])->name('admin.pemilu.data');
                Route::get('/{slug}/data', [PemiluController::class, 'dataBySlug'])->name('admin.pemilu.data.slug');
                Route::get('/{slug}/result/data', [PemiluController::class, 'dataResultVoting'])->name('admin.result.data');

                Route::post('/{slug}/sync', 'syncTotalParticipants')->name('admin.pemilu.data.sync');
            });

            Route::controller(KandidatController::class)->group(function () {
                Route::get('/{slug}/kandidat', 'kandidatPemilu')->name('admin.manage.pemilu.kandidat');
                Route::get('/{slug}/kandidat/export/result', 'exportResultPdf')->name('admin.manage.pemilu.export.result');
                Route::post('/{slug}/kandidat/add', 'addKandidatPemilu')->name('admin.manage.pemilu.kandidat.add');
                Route::post('/{slug}/kandidat/{id}/update', 'updateKandidatPemilu')->name('admin.manage.pemilu.kandidat.update');
                Route::delete('/{slug}/kandidat/{id}/delete', 'deleteKandidatPemilu')->name('admin.manage.pemilu.kandidat.delete');

                Route::get('/{slug}/kandidat/data', 'data')->name('admin.kandidat.data');
                Route::get('/{slug}/kandidat/{id}/data', 'dataById')->name('admin.kandidat.data.id');
            });
        });
    });
});

Route::group(['middleware' => ['auth'], 'prefix' => 'user'], function () {
    Route::get('/dashboard', [DashboarController::class, 'dashboard'])->name('user.dashboard');

    Route::get('/pemilu/{slug}/kandidat/{id}/data', [KandidatController::class, 'dataById'])->name('user.kandidat.data');
    Route::get('/event/pemilu/{slug}/join', [DashboarController::class, 'joinPemilu'])->name('user.pemilu.join')->middleware('verify.pemilu.password');
    Route::post('/event/pemilu/{slug}/kandidat/{id}/vote', [DashboarController::class, 'votePemilu'])->name('user.pemilu.vote');
    Route::post('/pemilu/{slug}/verify-password/join', [DashboarController::class, 'verifyPasswordJoin'])->name('user.pemilu.verify-password.join');
});

Route::get('/copyright', function () {
    $data['member'] = [
        [
            'name' => 'narashaaaaa',
            'role' => 'Bantuan Teknis (DevOps)',
            'img' => null,
            'instagram' => 'https://www.instagram.com/md.hilallsluvyaa',
            'website' => 'https://naramizaru.github.io',
            'github' => 'https://github.com/NaraMizaru',
        ],
        [
            'name' => 'Raffi Ahmad',
            'role' => 'Ketua Divisi Teknis Penyelenggara',
            'img' => 'assets/img/member/raffi.jpg',
            'instagram' => 'https://www.instagram.com/_rapinha/',
            'website' => 'https://raphinha-amd.github.io/',
            'github' => 'https://github.com/raphinha-amd',
        ],
        [
            'name' => 'Farhan Dika Alsani',
            'role' => 'Bantuan Teknis (Pengembang Sistem)',
            'img' => 'assets/img/member/farhan.jpg',
            'instagram' => 'https://www.instagram.com/farahanaya_',
            'website' => 'https://uiahan.github.io/kkanva-portofolio',
            'github' => 'https://github.com/uiahan',
        ],
    ];

    return view('copyright')->with($data);
})->name('copyright');
