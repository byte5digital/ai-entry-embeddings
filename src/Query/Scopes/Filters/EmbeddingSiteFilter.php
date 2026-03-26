<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Query\Scopes\Filters;

use Byte5\AiEntryEmbeddings\Models\EntryEmbedding;
use Illuminate\Database\Eloquent\Builder;
use Statamic\Facades\Site;
use Statamic\Query\Scopes\Filter;

class EmbeddingSiteFilter extends Filter
{
    /** @var bool */
    protected $pinned = true;

    public static function title(): string
    {
        return __('Site');
    }

    /** @return array<string, array<string, mixed>> */
    public function fieldItems(): array
    {
        $options = $this->options();

        return [
            'site' => [
                'display' => __('Site'),
                'type' => 'select',
                'options' => $options,
            ],
        ];
    }

    /** @return array<string, string> */
    public function autoApply(): array
    {
        return [

        ];
    }

    /**
     * @param  Builder<EntryEmbedding>  $query
     * @param  array<string, string>  $values
     */
    public function apply($query, $values): void
    {
        $query->where('site_handle', $values['site']);
    }

    /** @param  array<string, string>  $values */
    public function badge($values): string
    {
        $site = Site::get($values['site']);

        return __('Site').': '.__($site->name());
    }

    /** @param  string  $key */
    public function visibleTo($key): bool
    {
        return $key === 'embeddings';
    }

    /** @return array<string, string> */
    private function options(): array
    {
        return Site::authorized()
            ->mapWithKeys(fn ($site): array => [$site->handle() => __($site->name())])
            ->all();
    }
}
