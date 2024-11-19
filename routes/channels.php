<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('export-completed.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('import.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
