<?php

namespace App\Http\Controllers\Admin\Hub;

use App\Agent\Contracts\LlmDriver;
use App\Http\Controllers\Admin\Hub\Concerns\RetargetsAsAdminHub;
use App\Http\Controllers\GoogleBusinessController as BaseController;
use Illuminate\Http\Request;

class GoogleBusinessController extends BaseController
{
    use RetargetsAsAdminHub;

    public function index(Request $request)
    {
        return $this->proxy(fn () => parent::index($request));
    }

    public function redirectToGoogle(Request $request)
    {
        return $this->proxy(fn () => parent::redirectToGoogle($request));
    }

    public function handleGoogleCallback(Request $request)
    {
        return $this->proxy(fn () => parent::handleGoogleCallback($request));
    }

    public function disconnect()
    {
        return $this->proxy(fn () => parent::disconnect());
    }

    public function selectLocation(Request $request)
    {
        return $this->proxy(fn () => parent::selectLocation($request));
    }

    public function storePost(Request $request)
    {
        return $this->proxy(fn () => parent::storePost($request));
    }

    public function destroyPost(Request $request)
    {
        return $this->proxy(fn () => parent::destroyPost($request));
    }

    public function suggestReply(Request $request, LlmDriver $llm)
    {
        return $this->proxy(fn () => parent::suggestReply($request, $llm));
    }

    public function replyReview(Request $request)
    {
        return $this->proxy(fn () => parent::replyReview($request));
    }

    public function exportCsv()
    {
        return $this->proxy(fn () => parent::exportCsv());
    }

    public function exportPdf()
    {
        return $this->proxy(fn () => parent::exportPdf());
    }
}
