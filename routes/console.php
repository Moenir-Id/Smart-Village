<?php

use Illuminate\Support\Facades\Schedule;

// Purge foto KTP/KK kadaluarsa setiap hari jam 02.00
Schedule::command('smartvillage:purge-foto')->dailyAt('02:00');
