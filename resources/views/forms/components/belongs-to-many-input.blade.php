<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{
        state: $wire.entangle('{{ $getStatePath() }}').defer,
        search: '',
        page: 1,
        perPage: {{ $getPerPage() }},
        items: null,
        selected: [],
        init () {
            this.state ??= [] // Insure that it uses an array

            $wire.dispatchFormEvent('belongs-to-many::fetchItems', '{{ $getStatePath() }}')
            $wire.on('belongs-to-many::itemsFetchedFor-{{ $getStatePath() }}', (items) => {
                this.items = [...items[0]]
                this.selected = Alpine.raw(this.state).map((id) => this.items.find((item) => item.id === id))
            })

            $watch('search', () => this.page = 1)
        },
        updateState () {
            this.state = [...this.selected.map((item) => item.id)]
        },
        reorder (event) {
            const selected = Alpine.raw(this.selected)

            const reorderedRow = selected.splice(event.oldIndex, 1)[0]
            selected.splice(event.newIndex, 0, reorderedRow)

            this.selected = selected

            this.updateState()
        },
        currentPage () {
            return this.unselected()
                .slice((this.page - 1) * this.perPage, this.page * this.perPage)
        },
        unselected () {
            return this.items
                .filter((item) => item.html.toLowerCase().includes(this.search.toLowerCase()))
                .filter((item) => ! this.selected.includes(item))
        },
        maxPage () {
            return Math.ceil(this.unselected().length / this.perPage)
        },
        toggle (item) {
            if (this.selected.includes(item)) {
                this.selected = this.selected.filter((selection) => selection.id !== item.id)
            } else {
                this.selected.push(item)
            }

            if (this.page > this.maxPage()) {
                this.page = this.maxPage()
            }

            this.updateState()
        },
    }">
        <template x-if="items !== null">
            <div class="flex">
                <div class="w-1/2 h-128 border rounded-lg overflow-hidden flex flex-col" wire:ignore>
                    <div class="border-b p-2">
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Search..."
                            class="
                                block transition duration-75 rounded-lg shadow-sm
                                focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600
                                disabled:opacity-70 border-gray-300 w-full
                            "
                        >
                    </div>

                    <div class="overflow-y-auto flex-auto">
                        <template x-for="item in currentPage()" :key="item.id">
                            <div
                                x-html="item.html"
                                @click="toggle(item)"
                                class="border-b last:border-b-0 cursor-pointer"
                            ></div>
                        </template>
                    </div>

                    <div
                        class="flex justify-center items-center border-t"
                        x-show="unselected().length > perPage"
                        x-cloak
                    >
                        <button
                            type="button"
                            class="btn btn-sm btn-ghost w-1/2 flex justify-center p-4 opacity-75 hover:opacity-100 disabled:opacity-10"
                            :disabled="page === 1"
                            @click="page = 1"
                        >
                            <x-heroicon-s-chevron-double-left class="w-6 h-6" />
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-ghost w-1/2 flex justify-center p-4 opacity-75 hover:opacity-100 disabled:opacity-10"
                            :disabled="page === 1"
                            @click="page -= 1"
                        >
                            <x-heroicon-s-chevron-left class="w-6 h-6" />
                        </button>

                        <div class="text-gray-500 p-4 flex justify-center">
                            <span x-text="page"></span>
                            /
                            <span x-text="maxPage()"></span>
                        </div>

                        <button
                            type="button"
                            class="btn btn-sm btn-ghost w-1/2 flex justify-center p-4 opacity-75 hover:opacity-100 disabled:opacity-10"
                            :disabled="page === maxPage()"
                            @click="page += 1"
                        >
                            <x-heroicon-s-chevron-right class="w-6 h-6" />
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-ghost w-1/2 flex justify-center p-4 opacity-75 hover:opacity-100 disabled:opacity-10"
                            :disabled="page === maxPage()"
                            @click="page = maxPage()"
                        >
                            <x-heroicon-s-chevron-double-right class="w-6 h-6" />
                        </button>
                    </div>
                </div>

                <div class="mx-4 flex items-center opacity-50">
                    <div class="flex flex-col">
                        <x-heroicon-o-arrow-left class="w-6 h-6" />
                        <x-heroicon-o-arrow-right class="w-6 h-6" />
                    </div>
                </div>

                <div
                    class="w-1/2 h-128 border rounded-lg overflow-y-auto"
                    wire:ignore
                    x-sortable="state"
                    x-on:end="reorder($event)"
                >
                    <template x-for="(item, key) in selected" :key="key">
                        <div
                            x-sortable-handle
                            x-sortable-item="item.id"
                            x-html="item.html"
                            @click="toggle(item)"
                            class="border-b last:border-b-0 cursor-pointer"
                        ></div>
                    </template>
                </div>
            </div>
        </template>

        <template x-if="items === null">
            <div class="w-1/2 h-128 border rounded-lg overflow-hidden flex justify-center items-center">
                <x-filament-support::loading-indicator
                    class="w-10 h-10"
                />
            </div>
        </template>
    </div>
</x-dynamic-component>
