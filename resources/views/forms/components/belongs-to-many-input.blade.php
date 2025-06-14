<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{
        state: $wire.entangle('{{ $getStatePath() }}'),
        search: '',
        page: 1,
        perPage: {{ $getPagination() }},
        items: [],
        selected: [],
        loading: true,
        init () {
            this.state ??= [] // Insure that it uses an array

            $wire.dispatchFormEvent('belongs-to-many::fetchItems', '{{ $getStatePath() }}')
            $wire.$on('belongs-to-many::itemsFetchedFor-{{ $getStatePath() }}', (items) => {
                this.items = [...items[0]]

                this.selected = (Alpine.raw(this.state) || [])
                    .map((id) => this.items.find((item) => item.id === id))

                this.loading = false
            })
            
            $wire.$on('belongs-to-many::resetSelected-{{ $getStatePath() }}', () => {
                this.selected = []
                this.loading = false
            })

            $watch('search', () => this.page = 1)
        },
        updateState () {
            this.state = [...this.selected.map((item) => item.id)]
        },
        reorder (event) {
            const selected = Alpine.raw(this.selected) || []
            const reorderedRow = selected.splice(event.oldIndex, 1)[0]

            selected.splice(event.newIndex, 0, reorderedRow)
            this.selected = selected

            this.updateState()

            // HACK update prevKeys to new sort order
            // https://github.com/alpinejs/alpine/discussions/1635
            $refs.selected_template._x_prevKeys = this.state
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
            } else if (this.page < 1) {
                this.page = 1
            }

            this.updateState()
        },
    }">
        @if (! $isDisabled())
            <div class="flex" x-show="! loading" x-cloak>
                <div class="w-1/2 h-128 border dark:border-white/10 rounded-lg overflow-hidden flex flex-col">
                    <div class="border-b dark:border-white/10 p-2">
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Search..."
                            class="
                                w-full border-none px-3 py-1.5 text-base text-gray-950 outline-none transition
                                duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500
                                disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white
                                dark:placeholder:text-gray-500 dark:disabled:text-gray-400
                                dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6
                                bg-white dark:bg-white/5 dark:focus:ring-primary-500 dark:ring-white/20 duration-75
                                fi-input-wrp flex focus:ring-2 focus:ring-primary-600 ring-1 ring-gray-950/10
                                rounded-lg shadow-sm
                            "
                        >
                    </div>

                    <div class="overflow-y-auto flex-auto">
                        <template x-for="(item, key) in currentPage()" :key="key">
                            <div
                                x-html="item.html"
                                @click="toggle(item)"
                                class="border-b last:border-b-0 dark:border-white/10 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/5"
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
                    class="w-1/2 h-128 border dark:border-white/10 border rounded-lg overflow-y-auto"
                    @if ($getSortable())
                        x-sortable="selected"
                        x-on:end="reorder($event)"
                    @endif
                >
                    <template x-for="(item, key) in selected" :key="key" x-ref="selected_template">
                        <div
                            x-sortable-handle
                            x-sortable-item="item.id"
                            x-html="item.html"
                            @click="toggle(item)"
                            class="border-b dark:border-white/10 last:border-b-0 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/5"
                        ></div>
                    </template>
                </div>
            </div>
        @else
            <div class="flex" x-show="! loading" x-cloak>
                <div class="w-1/2 max-h-128 border dark:border-white/10 border rounded-lg overflow-y-auto">
                    <template x-for="(item, key) in selected" :key="key">
                        <div
                            x-html="item.html"
                            class="border-b dark:border-white/10 last:border-b-0"
                        ></div>
                    </template>
                </div>
            </div>
        @endif

        <template x-if="loading">
            <div class="w-1/2 h-128 border dark:border-white/10 rounded-lg overflow-hidden flex justify-center items-center">
                <x-filament::loading-indicator
                    class="w-10 h-10"
                />
            </div>
        </template>
    </div>
</x-dynamic-component>
