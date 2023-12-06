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

    protected function setUp(): void
    {
        parent::setUp();
        $this->indicateUsing(function (array $state): array {
            if(isset($state['from']) || isset($state['until'])){
                $message = $this->getLabel() . ' ' . 'between';
                return [
                    $message . ' ' .
                    ($state['from'] ? Carbon::parse($state['from'])->format(config('tables.date_format', 'Y-m-d')) : "all time") .
                    ' ' . __('date-range-filament::clauses.between_and') . ' ' .
                    ($state['until'] ? Carbon::parse($state['until'])->format(config('tables.date_format', 'Y-m-d')) : "now")
                ];
            }else{
                return [];
            }
        });
    }

    public function clauses(): array
    {
        return [];
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
            DatePicker::make('from')
                ->label(__('date-range-filament::clauses.from')),
            DatePicker::make('until')
                ->label(__('date-range-filament::clauses.until'))
        ];
    }
}
