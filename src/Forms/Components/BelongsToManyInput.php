<?php

namespace Codedor\BelongsToMany\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class BelongsToManyInput extends Field
{
    protected string $view = 'belongs-to-many-field::forms.components.belongs-to-many-input';

    public string|Closure $displayViewUsing = 'belongs-to-many-field::table-item';

    public string|Closure $relationship;

    public int|Closure $perPage = 10;

    public null|string|Closure $itemLabel = null;

    public Closure $resourceQuery;

    public function setUp(): void
    {
        // Guess the relationship name
        $this->relationship = $this->getName();

        // Register some listeners
        $this->registerListeners([
            'belongs-to-many::fetchItems' => [
                function (BelongsToManyInput $component, string $statePath): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $component->getLivewire()->emit("belongs-to-many::itemsFetchedFor-{$statePath}", [
                        $component->getResourcesForAlpine(),
                    ]);
                },
            ],
        ]);

        // Default to all items
        $this->resourceQuery(function (Builder $query) {
            return $query;
        });

        // Get the selected items
        $this->loadStateFromRelationshipsUsing(static function (self $component): void {
            $relationship = $component->getRelationship();

            $state = $relationship->getResults()
                ->pluck($relationship->getRelatedKeyName())
                ->toArray();

            $component->state($state);
        });

        // Save the newly selected items
        $this->saveRelationshipsUsing(static function (self $component, $state) {
            $component->getRelationship()->sync($state ?? []);
        });

        // Don't save the state as a normal field
        $this->dehydrated(false);
    }

    public function resourceQuery(Closure $callback): self
    {
        $this->resourceQuery = $callback;

        return $this;
    }

    public function getResources(): Collection
    {
        $related = $this->getRelationship()->getRelated();
        $query = $this->evaluate($this->resourceQuery, ['query' => $related->query()]);

        return collect($query->get(), $this->getDisplayUsingView());
    }

    public function getResourcesForAlpine(): Collection
    {
        return $this->getResources()->map(fn ($item) => [
            'id' => $item->id,
            'selected' => in_array($item->id, $this->getState()),
            'html' => view($this->getDisplayUsingView(), [
                'item' => $item,
                'label' => $this->getItemLabelUsing($item),
            ])->render(),
        ]);
    }

    public function relationship(string|Closure $relationship): self
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function getRelationship(): BelongsToMany
    {
        return $this->getModelInstance()->{$this->evaluate($this->relationship)}();
    }

    public function perPage(int|Closure $callback): static
    {
        $this->perPage = $callback;

        return $this;
    }

    public function getPerPage()
    {
        return $this->evaluate($this->perPage);
    }

    public function displayViewUsing(string|Closure $view): self
    {
        $this->displayViewUsing = $view;

        return $this;
    }

    public function getDisplayUsingView(): string
    {
        return $this->evaluate($this->displayViewUsing);
    }

    public function displayLabelUsing(null|string|Closure $view): self
    {
        $this->itemLabel = $view;

        return $this;
    }

    public function getItemLabelUsing($item)
    {
        return $item->{$this->evaluate($this->itemLabel)}
            ?? $item->id;
    }
}
