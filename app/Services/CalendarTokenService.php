<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CalendarTokenService
{
    public function ensureToken(Model $model): string
    {
        if ($model->calendar_token) {
            return $model->calendar_token;
        }
        return $this->regenerate($model);
    }

    public function regenerate(Model $model): string
    {
        $token = $this->generateUniqueToken(get_class($model));
        $model->update(['calendar_token' => $token]);
        return $token;
    }

    protected function generateUniqueToken(string $modelClass): string
    {
        do {
            $token = Str::random(48);
        } while ($modelClass::where('calendar_token', $token)->exists());
        return $token;
    }
}
