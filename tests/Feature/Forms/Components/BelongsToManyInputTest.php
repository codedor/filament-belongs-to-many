<?php

use Codedor\BelongsToMany\Forms\Components\BelongsToManyInput;
use Codedor\BelongsToMany\Tests\Fixtures\Forms\Livewire;
use Codedor\BelongsToMany\Tests\Fixtures\Models\TestModel;

it('can setup the input', function () {
    $field = BelongsToManyInput::make('tags')
        ->model(TestModel::class);

    expect($field)
        ->relationship->toBe('tags')
        ->getRelationship()->getTable()->toBe('test_model_test_tag');
});

it('can be sortable', function () {
    $field = BelongsToManyInput::make('tags')
        ->model(TestModel::class)
        ->sortable();

    expect($field)
        ->getSortable()->toBeTrue();
});
