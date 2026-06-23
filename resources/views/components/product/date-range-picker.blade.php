{{-- SI-RENT Date Range Picker — Single Flatpickr Range --}}
@props(['blockedDates' => [], 'productId' => null])

<div x-data="dateRangePicker(@json($blockedDates))">
    {{-- Single unified date range box --}}
    <div class="position-relative">
        <div class="bg-white rounded-3 border d-flex align-items-center px-3"
             style="height:60px; border-color: var(--border-default, #c9c9c9); cursor:pointer;"
             @click="$refs.rangePicker._flatpickr.open()">
            <div class="flex-grow-1">
                <div class="fw-medium" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary, #000);"
                     x-text="rangeText || '{{ __('ui.select_date') }}'"></div>
                <div class="text-muted" style="font-family:'Mona Sans',sans-serif; font-size:11px;" x-show="!rangeText">
                    {{ __('ui.start_date') }} — {{ __('ui.return_date') }}
                </div>
            </div>
            <i class="bi bi-calendar-range flex-shrink-0 ms-2" style="font-size:20px; color: var(--primary-blue, #0031e1);"></i>
        </div>

        <input x-ref="rangePicker" type="text" class="position-absolute top-0 start-0 w-100 h-100"
               style="opacity:0; pointer-events:none; z-index:-1;"
               data-flatpickr
               data-mode="range">
    </div>

    {{-- Hidden inputs to keep compatibility with existing bookingCard --}}
    <input type="hidden" x-model="startDateText">
    <input type="hidden" x-model="returnDateText">
</div>

@pushOnce('scripts')
<script>
    function dateRangePicker(blockedDates) {
        return {
            startDateText: '',
            returnDateText: '',
            rangeText: '',
            blockedDates: blockedDates,
            picker: null,

            init() {
                const blocked = this.blockedDates.map(d => d.blocked_date || d);

                this.picker = flatpickr(this.$refs.rangePicker, {
                    mode: 'range',
                    minDate: 'today',
                    disable: blocked,
                    dateFormat: 'd F Y',
                    showMonths: 2,
                    onReady: (selectedDates, dateStr, instance) => {
                        instance.set('disable', blocked);
                    },
                    onChange: (selectedDates, dateStr, instance) => {
                        if (selectedDates.length === 2) {
                            this.startDateText = instance.formatDate(selectedDates[0], 'd F Y');
                            this.returnDateText = instance.formatDate(selectedDates[1], 'd F Y');
                            this.rangeText = this.startDateText + ' — ' + this.returnDateText;
                        } else if (selectedDates.length === 1) {
                            this.startDateText = instance.formatDate(selectedDates[0], 'd F Y');
                            this.returnDateText = '';
                            this.rangeText = this.startDateText + ' — ...';
                        } else {
                            this.startDateText = '';
                            this.returnDateText = '';
                            this.rangeText = '';
                        }

                        if (selectedDates.length === 2) {
                            this.$dispatch('dates-selected', {
                                startDateText: this.startDateText,
                                returnDateText: this.returnDateText,
                            });
                        }
                    },
                });
            },

            dispatchChange() {
                // Event already dispatched in onChange
            },

            get selectedRange() {
                return {
                    start: this.startDateText,
                    end: this.returnDateText,
                };
            },
        };
    }
</script>
@endPushOnce
