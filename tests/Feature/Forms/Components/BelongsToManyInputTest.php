<?php

use Codedor\BelongsToMany\Forms\Components\BelongsToManyInput;
use Codedor\BelongsToMany\Tests\Fixtures\Livewire;
use Filament\Forms\ComponentContainer;

it('can setup the input', function () {
    $field = BelongsToManyInput::make('name')
        ->container(ComponentContainer::make(Livewire::make()));

    expect($field)
        ->relationship->toBe('name');
});
