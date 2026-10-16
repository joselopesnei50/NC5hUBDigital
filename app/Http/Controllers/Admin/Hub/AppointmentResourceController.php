<?php

namespace App\Http\Controllers\Admin\Hub;

use App\Http\Controllers\Admin\Hub\Concerns\RetargetsAsAdminHub;
use App\Http\Controllers\Customer\AppointmentResourceController as BaseController;
use App\Models\AppointmentResource;
use Illuminate\Http\Request;

class AppointmentResourceController extends BaseController
{
    use RetargetsAsAdminHub;

    public function index()
    {
        return $this->proxy(fn () => parent::index());
    }

    public function create()
    {
        return $this->proxy(fn () => parent::create());
    }

    public function store(Request $request)
    {
        return $this->proxy(fn () => parent::store($request));
    }

    public function edit(AppointmentResource $recurso)
    {
        return $this->proxy(fn () => parent::edit($recurso));
    }

    public function update(Request $request, AppointmentResource $recurso)
    {
        return $this->proxy(fn () => parent::update($request, $recurso));
    }

    public function destroy(AppointmentResource $recurso)
    {
        return $this->proxy(fn () => parent::destroy($recurso));
    }
}
