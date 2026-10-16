<?php

namespace App\Http\Controllers\Admin\Hub;

use App\Http\Controllers\Admin\Hub\Concerns\RetargetsAsAdminHub;
use App\Http\Controllers\Customer\AppointmentController as BaseController;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends BaseController
{
    use RetargetsAsAdminHub;

    public function index(Request $request)
    {
        return $this->proxy(fn () => parent::index($request));
    }

    public function create()
    {
        return $this->proxy(fn () => parent::create());
    }

    public function store(Request $request)
    {
        return $this->proxy(fn () => parent::store($request));
    }

    public function edit(Appointment $agenda)
    {
        return $this->proxy(fn () => parent::edit($agenda));
    }

    public function update(Request $request, Appointment $agenda)
    {
        return $this->proxy(fn () => parent::update($request, $agenda));
    }

    public function destroy(Appointment $agenda)
    {
        return $this->proxy(fn () => parent::destroy($agenda));
    }

    public function markCompleted(Appointment $agenda)
    {
        return $this->proxy(fn () => parent::markCompleted($agenda));
    }

    public function markCancelled(Appointment $agenda)
    {
        return $this->proxy(fn () => parent::markCancelled($agenda));
    }
}
