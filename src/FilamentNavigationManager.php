<?php

namespace RyanChandler\FilamentNavigation;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Ruer\Shop\Models\Category;
use RyanChandler\FilamentNavigation\Models\Navigation;

class FilamentNavigationManager
{
    use Macroable;

    protected array $itemTypes = [];

    public function addItemType(string $name, array | Closure $fields = []): static
    {
        $this->itemTypes[Str::slug($name)] = [
            'name' => $name,
            'fields' => $fields,
        ];

        return $this;
    }

    public function get(string $handle): ?Navigation
    {
        return Navigation::firstWhere('handle', $handle);
    }

    public function getItemTypes(): array
    {
        $this->itemTypes = [
            [
                'name' => 'Category Link',
                'fields' => [
                    Select::make('category_id')
                        ->searchable()
                        ->options(function () {
                            return Category::pluck('name', 'id');
                        }),
                ],
            ],
            [
                'name' => 'Internal Link',
                'fields' => [
                    TextInput::make('link'),
                ],
            ],
            [
                'name' => 'Text',
                'fields' => [],
            ],
        ];

        return array_merge([
            'external-link' => [
                'name' => __('filament-navigation::filament-navigation.attributes.external-link'),
                'fields' => [
                    TextInput::make('url')
                        ->label(__('filament-navigation::filament-navigation.attributes.url'))
                        ->required(),
                    Select::make('target')
                        ->label(__('filament-navigation::filament-navigation.attributes.target'))
                        ->options([
                            '' => __('filament-navigation::filament-navigation.select-options.same-tab'),
                            '_blank' => __('filament-navigation::filament-navigation.select-options.new-tab'),
                        ])
                        ->default(''),
                ],
            ],
        ], $this->itemTypes);
    }
}
