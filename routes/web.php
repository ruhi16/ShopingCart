<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Bs01SchoolComp;
use App\Http\Livewire\Bs02SessionComp;
use App\Http\Livewire\Bs07SubjectComp;
use App\Http\Livewire\Ex25SettingComp;
use App\Http\Livewire\Ex10StudentdbComp;
use App\Http\Livewire\LcMainLayout;

Route::get('/home', LcMainLayout::class)->name('home');

Route::get('/schools', Bs01SchoolComp::class)->name('schools');
Route::get('/sessions', Bs02SessionComp::class)->name('sessions');
Route::get('/subjects', Bs07SubjectComp::class)->name('subjects');
Route::get('/exam-settings', Ex25SettingComp::class)->name('exam-settings');
Route::get('/students', Ex10StudentdbComp::class)->name('students');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');





require __DIR__.'/auth.php';
