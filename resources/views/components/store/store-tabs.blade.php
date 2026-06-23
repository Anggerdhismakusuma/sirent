{{-- SI-RENT Store Tab Switcher — PRD section 10.2 --}}
@props(['storeUrl' => '#'])

<nav class="d-flex gap-0" style="font-family:'Mona Sans',sans-serif; border-bottom:2px solid #e2e2e2;">
    {{-- Tab: Items --}}
    <a :href="'{{ $storeUrl }}'"
       class="text-decoration-none px-4 py-2 fw-semibold position-relative"
       style="font-size:16px;"
       :style="activeTab === 'items' ? 'color:#0031e1' : 'color:#6a6a6a'"
       @click.prevent="activeTab = 'items'; window.history.replaceState({}, '', '{{ $storeUrl }}')">
        {{ __('ui.items') }}
        <span x-show="activeTab === 'items'"
              class="position-absolute bottom-0 start-0 w-100"
              style="height:2px; background:#0031e1; border-radius:1px;"></span>
    </a>

    {{-- Tab: About --}}
    <a :href="'{{ $storeUrl }}/about'"
       class="text-decoration-none px-4 py-2 fw-semibold position-relative"
       style="font-size:16px;"
       :style="activeTab === 'about' ? 'color:#0031e1' : 'color:#6a6a6a'"
       @click.prevent="activeTab = 'about'; window.history.replaceState({}, '', '{{ $storeUrl }}/about')">
        {{ __('ui.about_tab') }}
        <span x-show="activeTab === 'about'"
              class="position-absolute bottom-0 start-0 w-100"
              style="height:2px; background:#0031e1; border-radius:1px;"></span>
    </a>

    {{-- Tab: Reviews --}}
    <a :href="'{{ $storeUrl }}/reviews'"
       class="text-decoration-none px-4 py-2 fw-semibold position-relative"
       style="font-size:16px;"
       :style="activeTab === 'reviews' ? 'color:#0031e1' : 'color:#6a6a6a'"
       @click.prevent="activeTab = 'reviews'; window.history.replaceState({}, '', '{{ $storeUrl }}/reviews')">
        {{ __('ui.reviews_tab') }}
        <span x-show="activeTab === 'reviews'"
              class="position-absolute bottom-0 start-0 w-100"
              style="height:2px; background:#0031e1; border-radius:1px;"></span>
    </a>
</nav>
