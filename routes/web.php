<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Bs01SchoolComp;
use App\Http\Livewire\Bs02SessionComp;
use App\Http\Livewire\Bs07SubjectComp;
use App\Http\Livewire\Ex25SettingComp;
use App\Http\Livewire\Ex10StudentdbComp;
use App\Http\Livewire\Ex30MarksEntryComp;
use App\Http\Livewire\Ex30MarksEntryComp2;
use App\Http\Livewire\Ex30MarksRegisterComp;
use App\Http\Livewire\Bs11StudentcrComp;
use App\Http\Livewire\Ex30MarksEntryComp3;
use App\Http\Livewire\Ex30MarksRegisterComp2;
use App\Http\Livewire\LcMainLayout;

Route::get('/home', LcMainLayout::class)->name('home');

Route::get('/schools', Bs01SchoolComp::class)->name('schools');
Route::get('/sessions', Bs02SessionComp::class)->name('sessions');
Route::get('/subjects', Bs07SubjectComp::class)->name('subjects');
Route::get('/exam-settings', Ex25SettingComp::class)->name('exam-settings');
Route::get('/students', Ex10StudentdbComp::class)->name('students');
Route::get('/marks-entry', Ex30MarksEntryComp::class)->name('marks-entry');
Route::get('/marks-register', Ex30MarksRegisterComp::class)->name('marks-register');
Route::get('/marks-register2', Ex30MarksRegisterComp2::class)->name('marks-register2');
Route::get('/studentcr', Bs11StudentcrComp::class)->name('studentcr');
Route::get('/marks-entry2', Ex30MarksEntryComp2::class)->name('marks-entry2');
Route::get('/marks-entry3', Ex30MarksEntryComp3::class)->name('marks-entry3');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');





require __DIR__.'/auth.php';
