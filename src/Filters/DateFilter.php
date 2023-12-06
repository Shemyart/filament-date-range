<?php

namespace Shemyart\DateRangeFilamentFilter\Filters;

use Carbon\Carbon;
use Filament\Forms\Get;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\BaseFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Shemyart\DateRangeFilamentFilter\Concerns\HasClauses;

class DateFilter extends BaseFilter
{
    use HasClauses;

    const CLAUSE_BETWEEN = 'between';

    protected function setUp(): void
    {
        parent::setUp();

        $this->indicateUsing(function (array $state): array {
            $message = $this->getLabel() . ' ' . $this->clauses()[static::CLAUSE_BETWEEN];
            return [
                $message . ' ' .
                ($state['from'] ? Carbon::parse($state['from'])->format(config('tables.date_format', 'Y-m-d')) : 0) .
                ' ' . __('date-range-filament::clauses.between_and') . ' ' .
                ($state['until'] ? Carbon::parse($state['until'])->format(config('tables.date_format', 'Y-m-d')) : "~")
            ];
        });
    }

    public function clauses(): array
    {
        return [
            static::CLAUSE_BETWEEN => __('date-range-filament::clauses.between'),
        ];
    }

    protected function applyClause(Builder $query, string $column, string $clause, array $data = []): Builder
    {
        return $query
            ->when(
                $data['from'],
                fn (Builder $query, $date): Builder => $query->whereDate($column, '>=', $date),
            )
            ->when(
                $data['until'],
                fn (Builder $query, $date): Builder => $query->whereDate($column, '<=', $date),
            );
    }

    public function fields(): array
    {
        return [
//            DatePicker::make('value')
//                ->hiddenLabel()
//                ->visible(fn (Get $get) => !in_array($get('clause'), [
//                    static::CLAUSE_BETWEEN,
//                    null
//                ])),
            DatePicker::make('from')
                ->label(__('date-range-filament::clauses.from')),
            DatePicker::make('until')
                ->label(__('date-range-filament::clauses.until'))

        ];
    }
}
