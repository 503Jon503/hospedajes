<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('reservas:expirar')->daily();