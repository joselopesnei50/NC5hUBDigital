<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\AgentRun;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnalysisCompleted
{
    use Dispatchable, SerializesModels;

    /**
     * @param AgentRun $run A execução que acabou de ser concluída com sucesso.
     */
    public function __construct(
        public AgentRun $run
    ) {
    }
}
