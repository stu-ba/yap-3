<?php

namespace Yap\Http\Controllers\Auth;

use Yap\Http\Controllers\Controller;

class SwitchController extends Controller
{
    public function toTaiga()
    {
        return redirect()->away(toTaiga('discover'));
    }
}
