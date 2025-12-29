<?php

use Illuminate\Support\Carbon;

function formatDate($date){
    return Carbon::parse($date)->setTimezone(session('accountInfo.timezone'))->format('d M Y');
}

function formatTime($date){
    return Carbon::parse($date)->setTimezone(session('accountInfo.timezone'))->format('g:i A');
}

function formatDateAndTime($date){
    return Carbon::parse($date)->setTimezone(session('accountInfo.timezone'))->format('d M Y g:i A');
}

function formatMoney(){

}
function maskPhone(string $phone): string
{
    $length = strlen($phone);

    if ($length <= 8) {
        return str_repeat('*', $length);
    }

    return substr($phone, 0, 4)
        . str_repeat('*', $length - 8)
        . substr($phone, -4);
}

