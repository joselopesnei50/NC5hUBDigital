<?php

namespace App\Http\Controllers\Admin\Hub\Concerns;

use App\Services\SystemCliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

trait RetargetsAsAdminHub
{
    protected function impersonateSystem(): void
    {
        Auth::setUser(SystemCliente::user());
    }

    /**
     * Executa o metodo pai (controller customer) como se fosse o usuario system,
     * depois reescreve a resposta pra apontar pro namespace admin.hub.*
     */
    protected function proxy(callable $parentCall)
    {
        $this->impersonateSystem();
        $response = $parentCall();
        return $this->retarget($response);
    }

    protected function retarget($response)
    {
        if ($response instanceof View) {
            $name = $response->getName();
            if (str_starts_with($name, 'customer.')) {
                $newName = 'admin.hub.' . substr($name, 9);
                if (view()->exists($newName)) {
                    return view($newName, $response->getData());
                }
            }
            return $response;
        }

        if ($response instanceof RedirectResponse) {
            $target = $response->getTargetUrl();
            $mapped = $this->mapCustomerUrlToAdminHub($target);
            if ($mapped) {
                $response->setTargetUrl($mapped);
            }
            return $response;
        }

        return $response;
    }

    protected function mapCustomerUrlToAdminHub(string $url): ?string
    {
        $prefix = url('/area-cliente/');
        if (!str_starts_with($url, $prefix)) {
            return null;
        }
        $path = substr($url, strlen($prefix));
        return url('/admin/hub-nc5/' . $path);
    }
}
