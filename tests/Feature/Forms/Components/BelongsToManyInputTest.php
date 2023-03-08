<?php

use Codedor\BelongsToMany\Forms\Components\BelongsToManyInput;
use Codedor\BelongsToMany\Tests\Fixtures\Forms\Livewire;
use Codedor\BelongsToMany\Tests\Fixtures\Models\TestModel;
use Filament\Forms\ComponentContainer;

it('can setup the input', function () {
    $field = BelongsToManyInput::make('tags')
        ->container(ComponentContainer::make(Livewire::make()))
        ->model(TestModel::class);

    expect($field)
        ->relationship->toBe('tags')
        ->getListeners()->toHaveKey('belongs-to-many::fetchItems')
        ->getRelationship()->getTable()->toBe('test_model_test_tag');
});

it('can be sortable', function () {
    $field = BelongsToManyInput::make('tags')
        ->container(ComponentContainer::make(Livewire::make()))
        ->model(TestModel::class)
        ->sortable();

    expect($field)
        ->getSortable()->toBeTrue();
});
