<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TermoImagem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'termos_imagem';

    protected $fillable = [
        'cliente_id',
        'titulo',
        'projeto_referencia',
        'data_captacao',
        'local_captacao',
        'cedente_nome',
        'cedente_cpf',
        'cedente_rg',
        'cedente_email',
        'cedente_telefone',
        'cedente_endereco',
        'cedente_menor',
        'responsavel_nome',
        'responsavel_cpf',
        'responsavel_parentesco',
        'conteudo',
        'status',
        'signature_token',
        'enviado_em',
        'assinado_em',
        'assinatura_dados',
        'conteudo_hash',
    ];

    protected $casts = [
        'data_captacao' => 'date',
        'cedente_menor' => 'boolean',
        'enviado_em'    => 'datetime',
        'assinado_em'   => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ensureSignatureToken(): string
    {
        if (!$this->signature_token) {
            $this->signature_token = Str::random(48);
            $this->save();
        }

        return $this->signature_token;
    }

    public function publicSignUrl(): string
    {
        return route('public.termo-imagem.show', $this->ensureSignatureToken());
    }

    public function isAssinado(): bool
    {
        return $this->status === 'assinado';
    }

    public function getAssinaturaArrayAttribute(): array
    {
        if (!$this->assinatura_dados) {
            return [];
        }

        if (is_array($this->assinatura_dados)) {
            return $this->assinatura_dados;
        }

        return json_decode($this->assinatura_dados, true) ?: [];
    }

    public function getNomeAssinanteEsperadoAttribute(): string
    {
        return (string) ($this->cedente_menor ? ($this->responsavel_nome ?: $this->cedente_nome) : $this->cedente_nome);
    }

    public function getCpfAssinanteEsperadoAttribute(): string
    {
        return (string) ($this->cedente_menor ? ($this->responsavel_cpf ?: $this->cedente_cpf) : $this->cedente_cpf);
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (!$this->cedente_telefone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $this->cedente_telefone);
        if (!$digits) {
            return null;
        }

        if (!str_starts_with($digits, '55')) {
            $digits = '55' . $digits;
        }

        $nome = $this->nome_assinante_esperado ?: $this->cedente_nome;
        $url = $this->publicSignUrl();
        $msg = "Olá, {$nome}! Segue o link da NC5 Hub Digital para leitura e assinatura do Termo de Autorização de Uso de Imagem e Voz ({$this->titulo}): {$url}";

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($msg);
    }

    public static function defaultTemplate(bool $menor = false): string
    {
        $key = $menor ? 'termo_imagem.template_menor' : 'termo_imagem.template_adulto';

        return (string) config($key, '');
    }

    public static function replacePlaceholders(string $template, array $data): string
    {
        $dataCaptacao = $data['data_captacao'] ?? null;
        if ($dataCaptacao instanceof \DateTimeInterface) {
            $dataCaptacaoStr = $dataCaptacao->format('d/m/Y');
        } elseif (is_string($dataCaptacao) && trim($dataCaptacao) !== '') {
            try {
                $dataCaptacaoStr = \Carbon\Carbon::parse($dataCaptacao)->format('d/m/Y');
            } catch (\Throwable $e) {
                $dataCaptacaoStr = $dataCaptacao;
            }
        } else {
            $dataCaptacaoStr = 'data da captação';
        }

        $replacements = [
            '{{cedente_nome}}'           => $data['cedente_nome'] ?? '—',
            '{{cedente_cpf}}'            => $data['cedente_cpf'] ?? '—',
            '{{cedente_rg}}'             => !empty($data['cedente_rg']) ? $data['cedente_rg'] : 'não informado',
            '{{cedente_endereco}}'       => !empty($data['cedente_endereco']) ? $data['cedente_endereco'] : 'endereço informado no cadastro',
            '{{responsavel_nome}}'       => $data['responsavel_nome'] ?? '—',
            '{{responsavel_cpf}}'        => $data['responsavel_cpf'] ?? '—',
            '{{responsavel_parentesco}}' => $data['responsavel_parentesco'] ?? 'responsável legal',
            '{{projeto}}'                => !empty($data['projeto_referencia']) ? $data['projeto_referencia'] : ($data['titulo'] ?? 'Produção Audiovisual NC5'),
            '{{data_captacao}}'          => $dataCaptacaoStr,
            '{{local}}'                  => !empty($data['local_captacao']) ? $data['local_captacao'] : 'local da gravação',
            '{{cliente}}'                => !empty($data['cliente_nome']) ? $data['cliente_nome'] : 'NC5 Hub Digital',
        ];

        return strtr($template, $replacements);
    }
}
