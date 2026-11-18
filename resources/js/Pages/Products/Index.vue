<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    categoriesWithProducts: {
        type: Array,
        default: () => [],
    },
    specialOffers: {
        type: Array,
        default: () => [],
    },
    sliders: {
        type: Array,
        default: () => [],
    },
    offerBanners: {
        type: Array,
        default: () => [],
    },
    filters: Object,
});

const page = usePage();

const selectedCategory = ref(props.filters?.category || '');
const selectedSort = ref(props.filters?.sort || 'latest');
const selectedBrand = ref(props.filters?.brand || '');
const onSaleOnly = ref(!!props.filters?.on_sale);
const isCategorySidebarOpen = ref(true);
const hoveredHeroCategory = ref(null);

// Rich categories tree from shared Inertia props (with children, brands, preview_products)
const richCategories = computed(() => {
    if (page.props.app?.categories?.length) {
        return page.props.app.categories;
    }
    return props.categories || [];
});

// Find active category object (root or child)
const activeCategoryObj = computed(() => {
    if (!selectedCategory.value) return null;
    for (const root of richCategories.value) {
        if (root.slug === selectedCategory.value) return root;
        if (root.children?.length) {
            const foundChild = root.children.find(c => c.slug === selectedCategory.value);
            if (foundChild) {
                return { ...foundChild, parent: root };
            }
        }
    }
    const flatFound = (props.categories || []).find(c => c.slug === selectedCategory.value);
    return flatFound || null;
});

// Parent category to show in sidebar when viewing a category/subcategory
const sidebarCategoryTree = computed(() => {
    if (!activeCategoryObj.value) return null;
    if (activeCategoryObj.value.parent) {
        return activeCategoryObj.value.parent;
    }
    return activeCategoryObj.value;
});

watch(() => props.filters, (newFilters) => {
    selectedCategory.value = newFilters?.category || '';
    selectedSort.value = newFilters?.sort || 'latest';
    selectedBrand.value = newFilters?.brand || '';
    onSaleOnly.value = !!newFilters?.on_sale;
    minPriceInput.value = newFilters?.min_price || '';
    maxPriceInput.value = newFilters?.max_price || '';
}, { deep: true });

const currentSlide = ref(0);
let timer = null;

const startTimer = () => {
    if (props.sliders && props.sliders.length > 1) {
        stopTimer();
        timer = setInterval(nextSlide, 4500);
    }
};

const stopTimer = () => {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
};

const nextSlide = () => {
    if (!props.sliders || props.sliders.length <= 1) return;
    currentSlide.value = (currentSlide.value + 1) % props.sliders.length;
};

const prevSlide = () => {
    if (!props.sliders || props.sliders.length <= 1) return;
    currentSlide.value = (currentSlide.value - 1 + props.sliders.length) % props.sliders.length;
};

const goToSlide = (index) => {
    currentSlide.value = index;
    startTimer();
};

// Wishlist state persisted in localStorage
const wishlistIds = ref([]);

const toggleWishlist = (productId) => {
    const idx = wishlistIds.value.indexOf(productId);
    if (idx > -1) {
        wishlistIds.value.splice(idx, 1);
    } else {
        wishlistIds.value.push(productId);
    }
    try {
        localStorage.setItem('aq_wishlist', JSON.stringify(wishlistIds.value));
    } catch (e) {}
};

onMounted(() => {
    startTimer();
    try {
        const saved = localStorage.getItem('aq_wishlist');
        if (saved) wishlistIds.value = JSON.parse(saved);
    } catch (e) {}
});

onUnmounted(() => {
    stopTimer();
});

// Category carousel scrolling
const categoryScrollContainer = ref(null);
const scrollCategory = (direction) => {
    if (categoryScrollContainer.value) {
        const offset = direction === 'left' ? -260 : 260;
        categoryScrollContainer.value.scrollBy({ left: offset, behavior: 'smooth' });
    }
};

const minPriceInput = ref(props.filters?.min_price || '');
const maxPriceInput = ref(props.filters?.max_price || '');

const buildFilterQuery = (overrides = {}) => {
    const query = {
        category: selectedCategory.value || undefined,
        brand: selectedBrand.value || undefined,
        on_sale: onSaleOnly.value ? 1 : undefined,
        sort: selectedSort.value !== 'latest' ? selectedSort.value : undefined,
        search: props.filters?.search || undefined,
        min_price: minPriceInput.value || undefined,
        max_price: maxPriceInput.value || undefined,
        ...overrides,
    };
    Object.keys(query).forEach(k => {
        if (query[k] === undefined || query[k] === '' || query[k] === false) {
            delete query[k];
        }
    });
    return query;
};

const applyFilter = (categorySlug) => {
    hoveredHeroCategory.value = null;
    selectedCategory.value = categorySlug;
    selectedBrand.value = '';
    router.get('/products', buildFilterQuery({ category: categorySlug || undefined, brand: undefined }), {
        preserveState: true,
        preserveScroll: true,
        only: ['products', 'filters', 'categoriesWithProducts', 'specialOffers'],
        showProgress: false,
    });
};

const applyOfferFilter = (enable = true) => {
    onSaleOnly.value = enable;
    router.get('/products', buildFilterQuery({ on_sale: enable ? 1 : undefined }), {
        preserveState: true,
        preserveScroll: false,
        only: ['products', 'filters'],
        showProgress: false,
    });
};

const applyBrandFilter = (brand) => {
    selectedBrand.value = brand;
    router.get('/products', buildFilterQuery({ brand: brand || undefined }), {
        preserveState: true,
        preserveScroll: true,
        only: ['products', 'filters'],
        showProgress: false,
    });
};

const applySort = () => {
    router.get('/products', buildFilterQuery({ sort: selectedSort.value }), {
        preserveState: true,
        preserveScroll: true,
        only: ['products', 'filters'],
        showProgress: false,
    });
};

const applyPriceFilter = () => {
    router.get('/products', buildFilterQuery(), {
        preserveState: true,
        preserveScroll: true,
        only: ['products', 'filters'],
        showProgress: false,
    });
};

const clearAllFilters = () => {
    selectedCategory.value = '';
    selectedBrand.value = '';
    onSaleOnly.value = false;
    minPriceInput.value = '';
    maxPriceInput.value = '';
    router.get('/products', {}, {
        preserveState: true,
        preserveScroll: false,
    });
};

const clearPriceFilter = () => {
    minPriceInput.value = '';
    maxPriceInput.value = '';
    applyPriceFilter();
};

const addingProductId = ref(null);
const justAddedProductId = ref(null);

const addToCart = (productId) => {
    justAddedProductId.value = productId;
    setTimeout(() => {
        if (justAddedProductId.value === productId) {
            justAddedProductId.value = null;
        }
    }, 1500);

    router.post(`/cart/add/${productId}`, { quantity: 1 }, {
        preserveScroll: true,
        preserveState: true,
        only: ['cart', 'flash'],
        showProgress: false,
    });
};

const getDiscountPercent = (product) => {
    const price = Number(product?.price || 0);
    const sale = Number(product?.sale_price || 0);
    if (price > 0 && sale > 0 && sale < price) {
        return Math.round(((price - sale) / price) * 100);
    }
    return 0;
};

// Fallback category icons map
const categoryIcons = {
    'phone': '📱',
    'mobile': '📱',
    'laptop': '💻',
    'computer': '🖥️',
    'component': '⚙️',
    'audio': '🎧',
    'headphone': '🎧',
    'monitor': '🖥️',
    'display': '🖥️',
    'watch': '⌚',
    'accessorie': '⌚',
    'tablet': '📲',
    'ipad': '📲',
    'camera': '📷',
    'drone': '🛸',
    'gaming': '🎮',
    'game': '🎮',
    'gear': '🎮',
    'smart-home': '🏠',
    'tv': '📺',
    'television': '📺',
    'book': '📚',
    'boi': '📚',
    'beauty': '🧴',
    'grocer': '🛒',
    'oil': '🫒',
    'honey': '🍯',
    'attar': '🌸',
    'dates': '🌴',
    'fashion': '👔',
    'stationery': '✏️',
    'audiobook': '🎧',
    'ebook': '📱',
    'electronic': '⚡',
};

const getCategoryIcon = (cat) => {
    if (cat?.icon) return cat.icon;
    const str = `${cat?.slug || ''} ${cat?.name || ''}`.toLowerCase();
    for (const key in categoryIcons) {
        if (str.includes(key)) return categoryIcons[key];
    }
    return '🛍️';
};

const getCategoryBanners = (catId) => {
    if (!props.offerBanners) return [];
    return props.offerBanners.filter(b => b.after_category_id === catId);
};

const unassignedOfferBanners = computed(() => {
    if (!props.offerBanners) return [];
    return props.offerBanners.filter(b => !b.after_category_id);
});
</script>

<template>
    <AppLayout>
        <Head title="আল-আকসা মার্ট — বিশ্বস্ত অনলাইন শপ" />

        <!-- 1. HERO BANNER / SLIDER SECTION (With Left Category Sidebar + Hover Mega Submenu Flyout) -->
        <section v-if="!selectedCategory && !onSaleOnly && !filters?.search" class="mb-10">
            <div
                class="flex flex-col lg:flex-row gap-4 lg:gap-5 items-stretch relative"
                @mouseleave="hoveredHeroCategory = null"
            >
                <!-- Left: Category Sidebar + Hover Mega Submenu Flyout -->
                <div class="hidden lg:flex flex-col w-64 lg:w-[270px] flex-shrink-0 bg-white border border-gray-200 shadow-xs rounded-none h-[380px] xl:h-[420px] relative z-30">
                    <div class="flex-1 overflow-y-auto divide-y divide-gray-100 scrollbar-thin scrollbar-thumb-gray-200 hover:scrollbar-thumb-gray-300">
                        <div
                            v-for="cat in richCategories"
                            :key="cat.id"
                            @mouseenter="hoveredHeroCategory = cat"
                        >
                            <button
                                @click="applyFilter(cat.slug)"
                                class="w-full flex items-center justify-between px-4 py-2.5 hover:bg-emerald-50/70 hover:text-[#0d3b37] transition group text-[13px] font-medium text-gray-700 text-left cursor-pointer"
                                :class="{ 'bg-emerald-50/80 text-[#0d3b37] font-semibold': hoveredHeroCategory?.id === cat.id }"
                            >
                                <div class="flex items-center gap-3 truncate">
                                    <span class="text-base flex-shrink-0 group-hover:scale-110 transition-transform duration-150">
                                        {{ getCategoryIcon(cat) }}
                                    </span>
                                    <span class="truncate group-hover:font-semibold">{{ cat.name }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span v-if="cat.children?.length" class="text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full font-semibold">
                                        {{ cat.children.length }} সাব
                                    </span>
                                    <span class="text-gray-400 group-hover:text-[#0d3b37] group-hover:translate-x-0.5 transition-transform text-sm font-semibold">
                                        ›
                                    </span>
                                </div>
                            </button>
                        </div>

                        <!-- Special Offers Quick Item in Hero Sidebar -->
                        <button
                            @mouseenter="hoveredHeroCategory = null"
                            @click="applyOfferFilter(true)"
                            class="w-full flex items-center justify-between px-4 py-2.5 bg-rose-50/60 hover:bg-rose-100/70 text-rose-700 transition group text-[13px] font-bold text-left cursor-pointer"
                        >
                            <div class="flex items-center gap-3 truncate">
                                <span class="text-base">🔥</span>
                                <span class="truncate">বিশেষ অফার (Flash Deals)</span>
                            </div>
                            <span v-if="$page.props.app?.offers_count" class="bg-rose-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">
                                {{ $page.props.app.offers_count }}
                            </span>
                        </button>

                        <!-- Fallback if no categories -->
                        <div v-if="!richCategories?.length" class="p-6 text-center text-xs text-gray-400">
                            কোনো ক্যাটাগরি পাওয়া যায়নি
                        </div>
                    </div>

                    <!-- Right-Side Mega Flyout Submenu Over Slider (Desktop Hover) -->
                    <div
                        v-if="hoveredHeroCategory && (hoveredHeroCategory.children?.length || hoveredHeroCategory.brands?.length || hoveredHeroCategory.preview_products?.length)"
                        class="hidden lg:flex flex-col justify-between absolute left-full top-0 w-[560px] xl:w-[680px] h-[380px] xl:h-[420px] bg-white text-gray-800 shadow-2xl border border-gray-200 p-5 z-40 overflow-y-auto"
                    >
                        <div>
                            <!-- Flyout Header -->
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-2xl">{{ getCategoryIcon(hoveredHeroCategory) }}</span>
                                    <div>
                                        <h3 class="font-black text-[#0d3b37] text-base leading-tight">
                                            {{ hoveredHeroCategory.name }}
                                        </h3>
                                        <p class="text-[11px] text-gray-400">
                                            মোট {{ hoveredHeroCategory.products_count || 0 }} টি পণ্য উপলব্ধ
                                        </p>
                                    </div>
                                </div>
                                <button
                                    @click="applyFilter(hoveredHeroCategory.slug)"
                                    class="text-xs font-bold text-[#0d3b37] hover:text-white bg-emerald-50 hover:bg-[#0d3b37] px-3 py-1.5 rounded-lg transition cursor-pointer"
                                >
                                    সব দেখুন →
                                </button>
                            </div>

                            <!-- Subcategories Grid -->
                            <div v-if="hoveredHeroCategory.children?.length" class="mb-4">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">
                                    সাব-ক্যাটাগরি সমূহ (Subcategories)
                                </h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    <button
                                        v-for="sub in hoveredHeroCategory.children"
                                        :key="sub.id"
                                        @click="applyFilter(sub.slug)"
                                        class="flex items-center justify-between px-3 py-2 rounded-lg border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/50 text-xs font-semibold text-gray-700 hover:text-[#0d3b37] transition text-left cursor-pointer"
                                    >
                                        <span class="truncate flex items-center gap-1.5">
                                            <span>{{ getCategoryIcon(sub) }}</span>
                                            <span class="truncate">{{ sub.name }}</span>
                                        </span>
                                        <span v-if="sub.products_count" class="text-[10px] text-gray-400 ml-1">({{ sub.products_count }})</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Popular Brands -->
                            <div v-if="hoveredHeroCategory.brands?.length" class="mb-4">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">
                                    জনপ্রিয় ব্র্যান্ড (Brands)
                                </h4>
                                <div class="flex flex-wrap gap-1.5">
                                    <Link
                                        v-for="brand in hoveredHeroCategory.brands"
                                        :key="brand"
                                        :href="`/products?category=${hoveredHeroCategory.slug}&brand=${encodeURIComponent(brand)}`"
                                        class="px-2.5 py-1 rounded-full bg-gray-100 hover:bg-[#0d3b37] text-gray-700 hover:text-white text-xs font-semibold transition"
                                    >
                                        🏷️ {{ brand }}
                                    </Link>
                                </div>
                            </div>

                            <!-- Top Products in this Category -->
                            <div v-if="hoveredHeroCategory.preview_products?.length">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">
                                    জনপ্রিয় পণ্যসমূহ (Featured)
                                </h4>
                                <div class="grid grid-cols-3 gap-3">
                                    <Link
                                        v-for="prod in hoveredHeroCategory.preview_products"
                                        :key="prod.id"
                                        :href="`/products/${prod.slug || prod.id}`"
                                        class="group border border-gray-100 hover:border-emerald-300 rounded-xl p-2 flex flex-col bg-gray-50/50 hover:bg-white hover:shadow-md transition"
                                    >
                                        <div class="aspect-square w-full rounded-lg bg-white overflow-hidden mb-1.5 relative">
                                            <img
                                                :src="prod.image ? (prod.image.startsWith('http') ? prod.image : `/storage/${prod.image}`) : 'https://placehold.co/200x200?text=Al-Aqsa'"
                                                :alt="prod.name"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                            />
                                            <span
                                                v-if="getDiscountPercent(prod) > 0"
                                                class="absolute top-1 left-1 bg-rose-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full"
                                            >
                                                -{{ getDiscountPercent(prod) }}%
                                            </span>
                                        </div>
                                        <span class="text-xs font-bold text-gray-800 group-hover:text-[#0d3b37] truncate">
                                            {{ prod.name }}
                                        </span>
                                        <div class="flex items-baseline gap-1 mt-0.5">
                                            <span class="text-xs font-black text-[#0d3b37]">
                                                ৳{{ Number(prod.sale_price || prod.price).toLocaleString() }}
                                            </span>
                                            <span v-if="prod.sale_price && prod.sale_price < prod.price" class="text-[10px] text-gray-400 line-through">
                                                ৳{{ Number(prod.price).toLocaleString() }}
                                            </span>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Main Carousel Slider -->
                <div
                    @mouseenter="stopTimer"
                    @mouseleave="startTimer"
                    class="flex-1 w-full min-w-0 relative bg-white border border-gray-200 shadow-xs group flex items-center justify-center select-none rounded-none overflow-hidden h-[240px] sm:h-[320px] lg:h-[380px] xl:h-[420px]"
                >
                    <!-- Admin Sliders -->
                    <template v-if="sliders && sliders.length > 0">
                        <div class="relative w-full h-full overflow-hidden rounded-none">
                            <div
                                class="flex h-full w-full transition-transform duration-500 ease-out"
                                :style="{ transform: `translateX(-${currentSlide * 100}%)` }"
                            >
                                <div
                                    v-for="slide in sliders"
                                    :key="slide.id"
                                    class="w-full h-full flex-shrink-0 relative overflow-hidden rounded-none"
                                >
                                    <component
                                        :is="slide.link ? Link : 'div'"
                                        :href="slide.link || undefined"
                                        class="w-full h-full block relative overflow-hidden rounded-none"
                                    >
                                        <img
                                            :src="slide.image_url || (slide.image ? (slide.image.startsWith('http') ? slide.image : `/storage/${slide.image}`) : '')"
                                            :alt="slide.title || 'Al-Aqsa Mart Offer'"
                                            class="w-full h-full object-cover object-center block rounded-none"
                                        />

                                        <div
                                            v-if="slide.title || slide.subtitle || slide.offer_badge"
                                            class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/35 to-transparent flex items-center px-6 sm:px-12 text-white"
                                        >
                                            <div class="max-w-lg">
                                                <span
                                                    v-if="slide.offer_badge"
                                                    class="inline-block bg-[#b58d3d] text-white px-3 py-1 text-xs font-bold uppercase tracking-wider mb-2 shadow"
                                                >
                                                    {{ slide.offer_badge }}
                                                </span>
                                                <h2 v-if="slide.title" class="text-2xl sm:text-3xl font-extrabold mb-1.5 leading-tight drop-shadow-md">
                                                    {{ slide.title }}
                                                </h2>
                                                <p v-if="slide.subtitle" class="text-gray-100 text-xs sm:text-sm leading-relaxed mb-3 drop-shadow">
                                                    {{ slide.subtitle }}
                                                </p>
                                                <span
                                                    v-if="slide.link"
                                                    class="inline-flex items-center gap-1.5 bg-[#0d3b37] hover:bg-[#064e3b] text-white text-xs sm:text-sm font-semibold px-4 py-1.5 shadow transition"
                                                >
                                                    অর্ডার করুন →
                                                </span>
                                            </div>
                                        </div>
                                    </component>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Default Fallback Slider -->
                    <template v-else>
                        <div class="w-full h-full flex items-center bg-gradient-to-r from-[#0d3b37] via-[#0b433e] to-[#12584e] text-white p-8 sm:p-12 relative overflow-hidden rounded-none">
                            <div class="max-w-xl relative z-10">
                                <span class="inline-block bg-[#b58d3d] text-white px-3 py-1 text-xs font-bold uppercase tracking-wider mb-3 shadow">
                                    🌙 বিশেষ কালেকশন
                                </span>
                                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 leading-tight">
                                    সেরা মানের হালাল ও খাঁটি পণ্য — সবচেয়ে কম দামে!
                                </h2>
                                <p class="text-emerald-100 text-xs sm:text-sm mb-4 max-w-md">
                                    অ্যাডমিন প্যানেল (Sliders) থেকে যেকোনো সময় আকর্ষণীয় ব্যানার যুক্ত করতে পারেন।
                                </p>
                                <a
                                    href="#products-list"
                                    class="inline-block bg-[#b58d3d] text-white hover:bg-[#99742e] px-5 py-2.5 text-xs sm:text-sm font-bold shadow-md transition"
                                >
                                    শপিং শুরু করুন
                                </a>
                            </div>
                            <div class="absolute right-6 bottom-4 opacity-15 select-none pointer-events-none hidden sm:block text-9xl">
                                🛍️
                            </div>
                        </div>
                    </template>

                    <!-- Navigation Arrows -->
                    <template v-if="sliders && sliders.length > 1">
                        <button
                            @click="prevSlide"
                            class="absolute left-3 top-1/2 -translate-y-1/2 z-20 bg-white/70 hover:bg-[#0d3b37] text-gray-800 hover:text-white w-9 h-10 flex items-center justify-center transition shadow-md active:scale-95 border border-gray-200"
                            aria-label="Previous Slide"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button
                            @click="nextSlide"
                            class="absolute right-3 top-1/2 -translate-y-1/2 z-20 bg-white/70 hover:bg-[#0d3b37] text-gray-800 hover:text-white w-9 h-10 flex items-center justify-center transition shadow-md active:scale-95 border border-gray-200"
                            aria-label="Next Slide"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <!-- Indicator Dots -->
                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5">
                            <button
                                v-for="(_, idx) in sliders"
                                :key="idx"
                                @click="goToSlide(idx)"
                                :class="[
                                    'h-1.5 transition-all duration-300',
                                    idx === currentSlide ? 'w-6 bg-[#b58d3d] shadow-xs' : 'w-2 bg-black/30 hover:bg-black/60'
                                ]"
                            ></button>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- 2. "SHOP BY CATEGORY" CAROUSEL (Only on Homepage when no category/offer filter is selected) -->
        <section v-if="!selectedCategory && !onSaleOnly && !filters?.search" class="mb-10 bg-white rounded-2xl p-6 sm:p-7 border border-gray-200/80 shadow-xs relative">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                    Shop By Category
                </h2>
                <span class="text-xs text-gray-400 font-medium hidden sm:inline">পছন্দের ক্যাটাগরি বেছে নিন</span>
            </div>

            <!-- Left Navigation Arrow -->
            <button
                @click="scrollCategory('left')"
                class="absolute left-2 sm:left-3 top-[58%] -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white border border-gray-300 hover:border-gray-500 shadow-md flex items-center justify-center text-gray-700 transition active:scale-95"
                aria-label="Previous Category"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Right Navigation Arrow -->
            <button
                @click="scrollCategory('right')"
                class="absolute right-2 sm:right-3 top-[58%] -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white border border-gray-300 hover:border-gray-500 shadow-md flex items-center justify-center text-gray-700 transition active:scale-95"
                aria-label="Next Category"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Categories Card List -->
            <div
                ref="categoryScrollContainer"
                class="flex items-start gap-4 sm:gap-6 overflow-x-auto px-5 sm:px-8 pb-2 scrollbar-none scroll-smooth"
            >
                <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="applyFilter(cat.slug)"
                    class="flex-shrink-0 flex flex-col items-center w-24 sm:w-28 text-center group cursor-pointer"
                >
                    <div
                        :class="[
                            'w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-white border flex items-center justify-center p-2.5 transition duration-200 shadow-xs group-hover:shadow-md group-hover:-translate-y-0.5 overflow-hidden',
                            selectedCategory === cat.slug
                                ? 'border-[#0d3b37] ring-2 ring-[#0d3b37]/20 shadow-sm'
                                : 'border-gray-200 group-hover:border-gray-300'
                        ]"
                    >
                        <img
                            v-if="cat.image_url || (cat.image && !cat.image.startsWith('http'))"
                            :src="cat.image_url || `/storage/${cat.image}`"
                            :alt="cat.name"
                            class="w-full h-full object-contain p-1 group-hover:scale-110 transition duration-300"
                        />
                        <span v-else class="text-3xl sm:text-4xl group-hover:scale-110 transition duration-200">
                            {{ getCategoryIcon(cat) }}
                        </span>
                    </div>

                    <span
                        :class="[
                            'text-xs font-medium mt-2.5 transition line-clamp-2 leading-tight',
                            selectedCategory === cat.slug ? 'text-[#0d3b37] font-bold' : 'text-gray-700 group-hover:text-gray-900'
                        ]"
                    >
                        {{ cat.name }}
                    </span>
                </button>
            </div>
        </section>

        <!-- ============================================================= -->
        <!-- 2B. SPECIAL OFFERS / FLASH DEALS SECTION ON HOMEPAGE          -->
        <!-- ============================================================= -->
        <section
            v-if="!selectedCategory && !onSaleOnly && !filters?.search && specialOffers && specialOffers.length > 0"
            class="mb-10 bg-gradient-to-br from-rose-50/70 via-white to-amber-50/60 rounded-2xl p-5 sm:p-7 border border-rose-200/70 shadow-xs"
        >
            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 mb-6 border-b border-rose-200/60">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-xl shadow-xs">
                        🔥
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">
                                বিশেষ অফার ও ফ্ল্যাশ ডিল (Special Offers)
                            </h2>
                            <span class="hidden sm:inline-block bg-rose-600 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                Hot Deals
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            সীমিত সময়ের জন্য সেরা পণ্যে আকর্ষণীয় মূল্যছাড়!
                        </p>
                    </div>
                </div>

                <button
                    @click="applyOfferFilter(true)"
                    class="inline-flex items-center gap-1.5 bg-[#0d3b37] hover:bg-[#064e3b] text-white text-xs sm:text-sm font-bold px-4 py-2 rounded-xl transition shadow-xs cursor-pointer"
                >
                    <span>সব অফার দেখুন</span>
                    <span>→</span>
                </button>
            </div>

            <!-- Special Offers Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5 sm:gap-4.5">
                <div
                    v-for="product in specialOffers"
                    :key="`offer-${product.id}`"
                    class="bg-white rounded-[22px] border border-rose-100 hover:border-rose-300 hover:shadow-xl transition-all duration-300 flex flex-col group relative overflow-hidden"
                >
                    <!-- Product Image Area + Discount Percentage Badge -->
                    <Link :href="`/products/${product.slug || product.id}`" class="block w-full aspect-square bg-gray-50 relative group overflow-hidden">
                        <img
                            :src="product.image ? (product.image.startsWith('http') ? product.image : `/storage/${product.image}`) : 'https://placehold.co/400x400?text=Al-Aqsa+Mart'"
                            :alt="product.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        />
                        <!-- Discount % Badge -->
                        <span
                            v-if="getDiscountPercent(product) > 0"
                            class="absolute top-2.5 left-2.5 z-10 bg-rose-600 text-white text-[10px] sm:text-[11px] font-black px-2.5 py-0.5 rounded-full shadow-md"
                        >
                            -{{ getDiscountPercent(product) }}% ছাড়
                        </span>
                    </Link>

                    <!-- Curved Smooth Hill-Notch Footer Wrapper -->
                    <div class="relative bg-emerald-100/70 mt-auto flex flex-col pt-3.5 px-3.5 pb-3.5 rounded-b-[22px]">
                        <div class="absolute -top-[23px] left-0 right-0 w-full overflow-hidden leading-none pointer-events-none z-10">
                            <svg class="w-full h-[24px] text-emerald-100/70 fill-current" viewBox="0 0 300 24" preserveAspectRatio="none">
                                <path d="M 0,16 C 60,16 110,16 135,14 C 160,12 170,1 195,1 C 220,1 270,1 292,1 C 298,1 300,5 300,10 L 300,24 L 0,24 Z"></path>
                            </svg>
                        </div>

                        <!-- Floating Circular Action Icons -->
                        <div class="absolute -top-[17px] right-3 z-20 flex items-center gap-1.5">
                            <button
                                type="button"
                                @click.prevent.stop="toggleWishlist(product.id)"
                                class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-full bg-white hover:bg-rose-50 shadow-sm border border-emerald-100 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                :class="wishlistIds.includes(product.id) ? 'text-rose-600' : 'text-gray-600 hover:text-rose-600'"
                                title="পছন্দের তালিকা"
                            >
                                <svg class="w-3.5 h-3.5" :fill="wishlistIds.includes(product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                            <Link
                                :href="`/products/${product.slug || product.id}`"
                                class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-full bg-white hover:bg-blue-50 text-gray-600 hover:text-blue-600 shadow-sm border border-emerald-100 flex items-center justify-center transition active:scale-90"
                                title="বিস্তারিত দেখুন"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </Link>
                        </div>

                        <!-- Product Title -->
                        <Link
                            :href="`/products/${product.slug || product.id}`"
                            class="font-bold text-gray-900 hover:text-[#0d3b37] transition truncate text-xs sm:text-[13px] mb-1 block relative z-10"
                            :title="product.name"
                        >
                            {{ product.name }}
                        </Link>

                        <!-- Pricing & Savings -->
                        <div class="flex items-baseline justify-between gap-1 mb-2.5 relative z-10">
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="text-sm sm:text-base font-black text-rose-700">
                                    ৳{{ Number(product.sale_price || product.price).toLocaleString() }}
                                </span>
                                <span
                                    v-if="product.sale_price && product.sale_price < product.price"
                                    class="text-[11px] sm:text-xs text-gray-500/80 line-through font-medium"
                                >
                                    ৳{{ Number(product.price).toLocaleString() }}
                                </span>
                            </div>

                            <span
                                v-if="product.sale_price && product.sale_price < product.price"
                                class="text-[10px] text-emerald-800 bg-white/80 px-1.5 py-0.5 rounded font-bold whitespace-nowrap"
                            >
                                সাশ্রয় ৳{{ Number(product.price - product.sale_price).toLocaleString() }}
                            </span>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="flex items-center gap-1.5 relative z-10">
                            <button
                                @click="addToCart(product.id)"
                                :disabled="addingProductId === product.id"
                                :class="[
                                    'flex-1 rounded-xl py-2 px-2 text-xs font-bold flex items-center justify-center gap-1.5 transition-all duration-200 active:scale-95 shadow-sm border cursor-pointer',
                                    justAddedProductId === product.id
                                        ? 'bg-emerald-600 text-white border-emerald-600'
                                        : 'bg-white hover:bg-[#0d3b37] text-[#0d3b37] hover:text-white border-emerald-200/60 hover:border-[#0d3b37]'
                                ]"
                            >
                                <svg v-if="justAddedProductId === product.id" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg v-else class="w-3.5 h-3.5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <span>{{ justAddedProductId === product.id ? 'যোগ হয়েছে!' : 'Add to Cart' }}</span>
                            </button>

                            <Link
                                :href="`/products/${product.slug || product.id}`"
                                class="w-8.5 h-8.5 rounded-xl bg-white hover:bg-[#0d3b37] text-gray-700 hover:text-white border border-emerald-200/60 hover:border-[#0d3b37] flex items-center justify-center transition active:scale-95 shadow-sm"
                                title="পণ্য দেখুন"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================= -->
        <!-- 3A. SPECIFIC CATEGORY OR SPECIAL OFFERS PAGE VIEW             -->
        <!-- ============================================================= -->
        <div v-if="selectedCategory || onSaleOnly" class="mb-12">
            <!-- Breadcrumbs Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs sm:text-sm text-gray-500 mb-5 pb-3 border-b border-gray-200/80">
                <nav class="flex items-center gap-2 font-medium flex-wrap">
                    <Link href="/" @click.prevent="clearAllFilters()" class="hover:text-[#0d3b37] transition">Home</Link>
                    <template v-if="activeCategoryObj?.parent">
                        <span>/</span>
                        <button @click="applyFilter(activeCategoryObj.parent.slug)" class="hover:text-[#0d3b37] transition cursor-pointer">
                            {{ activeCategoryObj.parent.name }}
                        </button>
                    </template>
                    <template v-if="selectedCategory">
                        <span>/</span>
                        <span class="text-gray-900 font-bold">
                            {{ activeCategoryObj?.name || selectedCategory }}
                        </span>
                    </template>
                    <template v-if="onSaleOnly">
                        <span>/</span>
                        <span class="text-rose-600 font-bold flex items-center gap-1">
                            🔥 বিশেষ অফার (On Sale)
                        </span>
                    </template>
                    <template v-if="selectedBrand">
                        <span>/</span>
                        <span class="bg-emerald-50 text-[#0d3b37] px-2 py-0.5 rounded-full text-xs font-bold flex items-center gap-1">
                            🏷️ {{ selectedBrand }}
                            <button @click="applyBrandFilter('')" class="hover:text-rose-600 ml-1">✕</button>
                        </span>
                    </template>
                </nav>

                <!-- Result count & Sort dropdown on right -->
                <div class="flex items-center gap-4">
                    <span class="text-xs text-gray-500 hidden sm:inline">
                        Showing {{ products.from || (products.data?.length ? 1 : 0) }}-{{ products.to || products.data?.length || 0 }} of {{ products.total || products.data?.length || 0 }} Results
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-500 hidden md:inline">Sort:</span>
                        <select
                            v-model="selectedSort"
                            @change="applySort"
                            class="bg-white border border-gray-300 rounded-md px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#0d3b37] font-medium text-gray-700 cursor-pointer shadow-2xs"
                        >
                            <option value="latest">Latest</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="name">Name (A-Z)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout: Left Filters + Right Products Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left Sidebar: Category / Subcategories + Offer Toggle + Price Filter -->
                <aside class="col-span-1 lg:col-span-3 space-y-4">
                    <!-- Category & Subcategory Tree Box -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-2xs">
                        <button
                            @click="isCategorySidebarOpen = !isCategorySidebarOpen"
                            class="w-full bg-[#0d3b37] text-white px-4 py-3 flex items-center justify-between font-bold text-xs uppercase tracking-wider text-left transition cursor-pointer"
                        >
                            <span class="truncate">
                                {{ sidebarCategoryTree?.name || 'CATEGORIES' }}
                            </span>
                            <svg
                                class="w-4 h-4 text-amber-300 transition-transform duration-200"
                                :class="{ 'rotate-180': !isCategorySidebarOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div v-show="isCategorySidebarOpen" class="p-2 bg-white space-y-1">
                            <!-- If viewing a category with subcategories -->
                            <template v-if="sidebarCategoryTree">
                                <button
                                    @click="applyFilter(sidebarCategoryTree.slug)"
                                    class="w-full text-left px-3 py-2 rounded text-xs flex items-center justify-between transition cursor-pointer"
                                    :class="selectedCategory === sidebarCategoryTree.slug ? 'font-bold bg-[#0d3b37] text-white shadow-xs' : 'font-semibold text-gray-800 hover:bg-emerald-50'"
                                >
                                    <span class="truncate">{{ sidebarCategoryTree.name }} (সব)</span>
                                    <span class="text-amber-300 font-bold">›</span>
                                </button>

                                <!-- Subcategories list -->
                                <div v-if="sidebarCategoryTree.children?.length" class="pl-3 pt-1 space-y-1 border-l-2 border-emerald-100 ml-2">
                                    <button
                                        v-for="sub in sidebarCategoryTree.children"
                                        :key="sub.id"
                                        @click="applyFilter(sub.slug)"
                                        class="w-full text-left px-3 py-1.5 rounded text-xs flex items-center justify-between transition cursor-pointer"
                                        :class="selectedCategory === sub.slug ? 'font-bold bg-emerald-700 text-white' : 'text-gray-600 hover:bg-emerald-50 hover:text-[#0d3b37]'"
                                    >
                                        <span class="truncate">• {{ sub.name }}</span>
                                        <span v-if="sub.products_count" class="text-[10px] opacity-75">({{ sub.products_count }})</span>
                                    </button>
                                </div>
                            </template>

                            <!-- Otherwise show all root categories -->
                            <template v-else>
                                <button
                                    v-for="cat in richCategories"
                                    :key="cat.id"
                                    @click="applyFilter(cat.slug)"
                                    class="w-full text-left px-3 py-2 rounded text-xs flex items-center justify-between text-gray-700 hover:bg-emerald-50 hover:text-[#0d3b37] font-medium transition cursor-pointer"
                                >
                                    <span class="truncate">{{ getCategoryIcon(cat) }} {{ cat.name }}</span>
                                    <span>›</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Special Offers Filter Box -->
                    <div class="bg-white border border-gray-200 rounded-lg p-3.5 shadow-2xs">
                        <label class="flex items-center justify-between cursor-pointer select-none">
                            <span class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                <span>🔥</span>
                                <span>শুধুমাত্র অফার পণ্য (On Sale)</span>
                            </span>
                            <input
                                type="checkbox"
                                :checked="onSaleOnly"
                                @change="applyOfferFilter($event.target.checked)"
                                class="rounded text-[#0d3b37] focus:ring-[#0d3b37] w-4 h-4 cursor-pointer"
                            />
                        </label>
                    </div>

                    <!-- Price Filter Box -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-2xs">
                        <div class="bg-[#0d3b37] text-white px-4 py-2.5 flex items-center justify-between font-bold text-xs uppercase tracking-wider">
                            <span>PRICE</span>
                            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                            </svg>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <span class="text-[10px] text-gray-500 uppercase font-semibold block mb-1">সর্বনিম্ন (৳)</span>
                                    <input
                                        v-model="minPriceInput"
                                        type="number"
                                        placeholder="0"
                                        class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:border-[#0d3b37]"
                                    />
                                </div>
                                <span class="text-gray-400 mt-4">-</span>
                                <div class="flex-1">
                                    <span class="text-[10px] text-gray-500 uppercase font-semibold block mb-1">সর্বোচ্চ (৳)</span>
                                    <input
                                        v-model="maxPriceInput"
                                        type="number"
                                        placeholder="10000"
                                        class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:border-[#0d3b37]"
                                    />
                                </div>
                            </div>

                            <button
                                @click="applyPriceFilter"
                                class="w-full bg-[#0d3b37] hover:bg-[#064e3b] text-white py-2 rounded text-xs font-bold uppercase tracking-wider transition select-none cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                FILTER
                            </button>

                            <button
                                v-if="minPriceInput || maxPriceInput"
                                @click="clearPriceFilter"
                                class="w-full text-center text-[11px] text-rose-600 hover:underline cursor-pointer"
                            >
                                ফিল্টার রিসেট করুন
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Right Products Grid -->
                <div class="col-span-1 lg:col-span-9">
                    <div v-if="products.data && products.data.length > 0" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4.5">
                        <div
                            v-for="product in products.data"
                            :key="product.id"
                            class="bg-white rounded-[22px] border border-gray-100 hover:border-gray-200 hover:shadow-xl transition-all duration-300 flex flex-col group relative overflow-hidden"
                        >
                            <!-- Product Image Area + Discount Badge -->
                            <Link :href="`/products/${product.slug || product.id}`" class="block w-full aspect-square bg-gray-50 relative group overflow-hidden">
                                <img
                                    :src="product.image ? (product.image.startsWith('http') ? product.image : `/storage/${product.image}`) : 'https://placehold.co/400x400?text=Al-Aqsa+Mart'"
                                    :alt="product.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                />
                                <span
                                    v-if="getDiscountPercent(product) > 0"
                                    class="absolute top-2.5 left-2.5 z-10 bg-rose-600 text-white text-[10px] sm:text-[11px] font-black px-2.5 py-0.5 rounded-full shadow-md"
                                >
                                    -{{ getDiscountPercent(product) }}% ছাড়
                                </span>
                            </Link>

                            <!-- Curved Smooth Hill-Notch Footer Wrapper -->
                            <div class="relative bg-emerald-100/70 mt-auto flex flex-col pt-3.5 px-3.5 pb-3.5 rounded-b-[22px]">
                                <div class="absolute -top-[23px] left-0 right-0 w-full overflow-hidden leading-none pointer-events-none z-10">
                                    <svg class="w-full h-[24px] text-emerald-100/70 fill-current" viewBox="0 0 300 24" preserveAspectRatio="none">
                                        <path d="M 0,16 C 60,16 110,16 135,14 C 160,12 170,1 195,1 C 220,1 270,1 292,1 C 298,1 300,5 300,10 L 300,24 L 0,24 Z"></path>
                                    </svg>
                                </div>

                                <!-- Floating Circular Action Icons -->
                                <div class="absolute -top-[17px] right-3 z-20 flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click.prevent.stop="toggleWishlist(product.id)"
                                        class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-full bg-white hover:bg-rose-50 shadow-sm border border-emerald-100 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                        :class="wishlistIds.includes(product.id) ? 'text-rose-600' : 'text-gray-600 hover:text-rose-600'"
                                        title="পছন্দের তালিকা"
                                    >
                                        <svg class="w-3.5 h-3.5" :fill="wishlistIds.includes(product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                    <Link
                                        :href="`/products/${product.slug || product.id}`"
                                        class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-full bg-white hover:bg-blue-50 text-gray-600 hover:text-blue-600 shadow-sm border border-emerald-100 flex items-center justify-center transition active:scale-90"
                                        title="বিস্তারিত দেখুন"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </Link>
                                </div>

                                <!-- Product Title -->
                                <Link
                                    :href="`/products/${product.slug || product.id}`"
                                    class="font-bold text-gray-900 hover:text-[#0d3b37] transition truncate text-xs sm:text-[13px] mb-1 block relative z-10"
                                    :title="product.name"
                                >
                                    {{ product.name }}
                                </Link>

                                <!-- Pricing & Sold Count -->
                                <div class="flex items-baseline justify-between gap-1 mb-2.5 relative z-10">
                                    <div class="flex items-baseline gap-1.5 flex-wrap">
                                        <span class="text-sm sm:text-base font-black text-gray-950">
                                            ৳{{ Number(product.sale_price || product.price).toLocaleString() }}
                                        </span>
                                        <span
                                            v-if="product.sale_price && product.sale_price < product.price"
                                            class="text-[11px] sm:text-xs text-gray-500/80 line-through font-medium"
                                        >
                                            ৳{{ Number(product.price).toLocaleString() }}
                                        </span>
                                    </div>

                                    <span class="text-[10px] text-gray-600 font-medium whitespace-nowrap">
                                        {{ (product.id * 17 + 85) % 250 + 20 }} sold
                                    </span>
                                </div>

                                <!-- Card Action Buttons -->
                                <div class="flex items-center gap-1.5 relative z-10">
                                    <button
                                        @click="addToCart(product.id)"
                                        :disabled="addingProductId === product.id"
                                        :class="[
                                            'flex-1 rounded-xl py-2 px-2 text-xs font-bold flex items-center justify-center gap-1.5 transition-all duration-200 active:scale-95 shadow-sm border cursor-pointer',
                                            justAddedProductId === product.id
                                                ? 'bg-emerald-600 text-white border-emerald-600'
                                                : 'bg-white hover:bg-[#0d3b37] text-[#0d3b37] hover:text-white border-emerald-200/60 hover:border-[#0d3b37] group/btn'
                                        ]"
                                    >
                                        <svg v-if="addingProductId === product.id" class="w-3.5 h-3.5 animate-spin text-current" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        <svg v-else-if="justAddedProductId === product.id" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <svg v-else class="w-3.5 h-3.5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <span>{{ addingProductId === product.id ? 'যোগ হচ্ছে...' : (justAddedProductId === product.id ? 'যোগ হয়েছে!' : 'Add to Cart') }}</span>
                                    </button>

                                    <Link
                                        :href="`/products/${product.slug || product.id}`"
                                        class="w-8.5 h-8.5 rounded-xl bg-white hover:bg-[#0d3b37] text-gray-700 hover:text-white border border-emerald-200/60 hover:border-[#0d3b37] flex items-center justify-center transition active:scale-95 shadow-sm"
                                        title="পণ্য দেখুন"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State in Category -->
                    <div v-else class="text-center py-16 bg-white rounded-2xl border border-gray-200 p-8">
                        <span class="text-5xl block mb-3">🔍</span>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">এই ফিল্টারে কোনো পণ্য পাওয়া যায়নি!</h3>
                        <p class="text-gray-500 text-xs sm:text-sm">অন্য কোনো ক্যাটাগরি অথবা ফিল্টার ব্যবহার করুন।</p>
                        <button
                            @click="clearAllFilters()"
                            class="mt-4 inline-block text-xs bg-[#0d3b37] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[#064e3b] transition cursor-pointer"
                        >
                            সকল পণ্য দেখুন
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.links && products.links.length > 3" class="mt-8 flex justify-center gap-1.5 flex-wrap">
                        <Link
                            v-for="(link, i) in products.links"
                            :key="i"
                            :href="link.url || '#'"
                            v-html="link.label"
                            :class="[
                                'px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition',
                                link.active ? 'bg-[#0d3b37] text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-emerald-50 border border-gray-300',
                                !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : ''
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- 3B. HOMEPAGE CATEGORY PARTITIONS / SECTIONS VIEW                -->
        <!-- ============================================================= -->
        <div v-else-if="categoriesWithProducts && categoriesWithProducts.length > 0 && !filters?.search" class="space-y-8 mb-12">
            <template
                v-for="catGroup in categoriesWithProducts"
                :key="catGroup.id"
            >
                <section class="bg-white rounded-2xl p-5 sm:p-7 border border-gray-100/90 shadow-2xs">
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-1 h-5 sm:h-6 bg-[#f95738] rounded-full inline-block"></span>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight">
                                {{ catGroup.name }}
                            </h2>
                        </div>

                        <button
                            @click="applyFilter(catGroup.slug)"
                            class="inline-flex items-center gap-1 text-xs sm:text-sm font-semibold text-gray-700 hover:text-[#0d3b37] transition duration-150 group cursor-pointer"
                        >
                            <span>See More</span>
                            <span class="font-bold text-gray-500 group-hover:text-[#0d3b37] group-hover:translate-x-0.5 transition-transform">›</span>
                        </button>
                    </div>

                    <!-- Category Products Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5 sm:gap-4.5">
                        <div
                            v-for="product in catGroup.products"
                            :key="product.id"
                            class="bg-white rounded-[22px] border border-gray-100 hover:border-gray-200 hover:shadow-xl transition-all duration-300 flex flex-col group relative overflow-hidden"
                        >
                            <!-- Product Image Area + Discount Badge -->
                            <Link :href="`/products/${product.slug || product.id}`" class="block w-full aspect-square bg-gray-50 relative group overflow-hidden">
                                <img
                                    :src="product.image ? (product.image.startsWith('http') ? product.image : `/storage/${product.image}`) : 'https://placehold.co/400x400?text=Al-Aqsa+Mart'"
                                    :alt="product.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                />
                                <span
                                    v-if="getDiscountPercent(product) > 0"
                                    class="absolute top-2.5 left-2.5 z-10 bg-rose-600 text-white text-[10px] sm:text-[11px] font-black px-2.5 py-0.5 rounded-full shadow-md"
                                >
                                    -{{ getDiscountPercent(product) }}% ছাড়
                                </span>
                            </Link>

                            <!-- Curved Smooth Hill-Notch Footer Wrapper -->
                            <div class="relative bg-emerald-100/70 mt-auto flex flex-col pt-3.5 px-3.5 pb-3.5 rounded-b-[22px]">
                                <div class="absolute -top-[23px] left-0 right-0 w-full overflow-hidden leading-none pointer-events-none z-10">
                                    <svg class="w-full h-[24px] text-emerald-100/70 fill-current" viewBox="0 0 300 24" preserveAspectRatio="none">
                                        <path d="M 0,16 C 60,16 110,16 135,14 C 160,12 170,1 195,1 C 220,1 270,1 292,1 C 298,1 300,5 300,10 L 300,24 L 0,24 Z"></path>
                                    </svg>
                                </div>

                                <!-- Floating Circular Action Icons -->
                                <div class="absolute -top-[17px] right-3 z-20 flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click.prevent.stop="toggleWishlist(product.id)"
                                        class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-full bg-white hover:bg-rose-50 shadow-sm border border-emerald-100 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                        :class="wishlistIds.includes(product.id) ? 'text-rose-600' : 'text-gray-600 hover:text-rose-600'"
                                        title="পছন্দের তালিকা"
                                    >
                                        <svg class="w-3.5 h-3.5" :fill="wishlistIds.includes(product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                    <Link
                                        :href="`/products/${product.slug || product.id}`"
                                        class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-full bg-white hover:bg-blue-50 text-gray-600 hover:text-blue-600 shadow-sm border border-emerald-100 flex items-center justify-center transition active:scale-90"
                                        title="বিস্তারিত দেখুন"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </Link>
                                </div>

                                <!-- Product Title -->
                                <Link
                                    :href="`/products/${product.slug || product.id}`"
                                    class="font-bold text-gray-900 hover:text-[#0d3b37] transition truncate text-xs sm:text-[13px] mb-1 block relative z-10"
                                    :title="product.name"
                                >
                                    {{ product.name }}
                                </Link>

                                <!-- Pricing & Sold Count -->
                                <div class="flex items-baseline justify-between gap-1 mb-2.5 relative z-10">
                                    <div class="flex items-baseline gap-1.5 flex-wrap">
                                        <span class="text-sm sm:text-base font-black text-gray-950">
                                            ৳{{ Number(product.sale_price || product.price).toLocaleString() }}
                                        </span>
                                        <span
                                            v-if="product.sale_price && product.sale_price < product.price"
                                            class="text-[11px] sm:text-xs text-gray-500/80 line-through font-medium"
                                        >
                                            ৳{{ Number(product.price).toLocaleString() }}
                                        </span>
                                    </div>

                                    <span class="text-[10px] text-gray-600 font-medium whitespace-nowrap">
                                        {{ (product.id * 17 + 85) % 250 + 20 }} sold
                                    </span>
                                </div>

                                <!-- Card Action Buttons -->
                                <div class="flex items-center gap-1.5 relative z-10">
                                    <button
                                        @click="addToCart(product.id)"
                                        :disabled="addingProductId === product.id"
                                        :class="[
                                            'flex-1 rounded-xl py-2 px-2 text-xs font-bold flex items-center justify-center gap-1.5 transition-all duration-200 active:scale-95 shadow-sm border cursor-pointer',
                                            justAddedProductId === product.id
                                                ? 'bg-emerald-600 text-white border-emerald-600'
                                                : 'bg-white hover:bg-[#0d3b37] text-[#0d3b37] hover:text-white border-emerald-200/60 hover:border-[#0d3b37] group/btn'
                                        ]"
                                    >
                                        <svg v-if="addingProductId === product.id" class="w-3.5 h-3.5 animate-spin text-current" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        <svg v-else-if="justAddedProductId === product.id" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <svg v-else class="w-3.5 h-3.5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <span>{{ addingProductId === product.id ? 'যোগ হচ্ছে...' : (justAddedProductId === product.id ? 'যোগ হয়েছে!' : 'Add to Cart') }}</span>
                                    </button>

                                    <Link
                                        :href="`/products/${product.slug || product.id}`"
                                        class="w-8.5 h-8.5 rounded-xl bg-white hover:bg-[#0d3b37] text-gray-700 hover:text-white border border-emerald-200/60 hover:border-[#0d3b37] flex items-center justify-center transition active:scale-95 shadow-sm"
                                        title="পণ্য দেখুন"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- OFFER BANNER(S) BETWEEN CATEGORIES -->
                <div
                    v-if="getCategoryBanners(catGroup.id).length > 0"
                    class="space-y-4 my-6"
                >
                    <div
                        v-for="banner in getCategoryBanners(catGroup.id)"
                        :key="banner.id"
                        class="overflow-hidden rounded-2xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 bg-white group"
                    >
                        <component
                            :is="banner.link ? (banner.link.startsWith('http') ? 'a' : Link) : 'div'"
                            :href="banner.link || undefined"
                            :target="banner.link && banner.link.startsWith('http') ? '_blank' : undefined"
                            class="block w-full"
                        >
                            <img
                                :src="banner.image_url || (banner.image?.startsWith('http') ? banner.image : `/storage/${banner.image}`)"
                                :alt="banner.title || 'Offer Banner'"
                                class="w-full h-auto max-h-[220px] sm:max-h-[300px] object-cover group-hover:scale-[1.01] transition-transform duration-500 rounded-2xl"
                            />
                        </component>
                    </div>
                </div>
            </template>

            <!-- Unassigned Offer Banners -->
            <div
                v-if="unassignedOfferBanners.length > 0"
                class="space-y-4 my-6"
            >
                <div
                    v-for="banner in unassignedOfferBanners"
                    :key="banner.id"
                    class="overflow-hidden rounded-2xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 bg-white group"
                >
                    <component
                        :is="banner.link ? (banner.link.startsWith('http') ? 'a' : Link) : 'div'"
                        :href="banner.link || undefined"
                        :target="banner.link && banner.link.startsWith('http') ? '_blank' : undefined"
                        class="block w-full"
                    >
                        <img
                            :src="banner.image_url || (banner.image?.startsWith('http') ? banner.image : `/storage/${banner.image}`)"
                            :alt="banner.title || 'Offer Banner'"
                            class="w-full h-auto max-h-[220px] sm:max-h-[300px] object-cover group-hover:scale-[1.01] transition-transform duration-500 rounded-2xl"
                        />
                    </component>
                </div>
            </div>
        </div>

        <!-- Fallback: Filtered or Searched Results -->
        <section v-else id="products-list">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-200">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900">
                        {{ filters?.search ? `"${filters.search}" এর অনুসন্ধানের ফলাফল` : 'সকল পণ্য সমূহ' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                        মোট <strong class="text-gray-800">{{ products.total || products.data?.length || 0 }}</strong> টি পণ্য পাওয়া গেছে
                    </p>
                </div>

                <!-- Sort Dropdown -->
                <div class="flex items-center gap-3 self-end sm:self-auto">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">সর্ট করুন:</label>
                    <select
                        v-model="selectedSort"
                        @change="applySort"
                        class="bg-white border border-gray-300 rounded-lg px-3.5 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b37] font-medium text-gray-700 shadow-xs cursor-pointer"
                    >
                        <option value="latest">নতুন আগমন (Latest)</option>
                        <option value="price_asc">মূল্য: কম থেকে বেশি</option>
                        <option value="price_desc">মূল্য: বেশি থেকে কম</option>
                        <option value="name">নাম অনুসারে (A-Z)</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid (Search/Filter Grid) -->
            <div v-if="products.data && products.data.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5 sm:gap-4.5">
                <div
                    v-for="product in products.data"
                    :key="product.id"
                    class="bg-white rounded-[22px] border border-gray-100 hover:border-gray-200 hover:shadow-xl transition-all duration-300 flex flex-col group relative overflow-hidden"
                >
                    <Link :href="`/products/${product.slug || product.id}`" class="block w-full aspect-square bg-gray-50 relative group overflow-hidden">
                        <img
                            :src="product.image ? (product.image.startsWith('http') ? product.image : `/storage/${product.image}`) : 'https://placehold.co/400x400?text=Al-Aqsa+Mart'"
                            :alt="product.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        />
                        <span
                            v-if="getDiscountPercent(product) > 0"
                            class="absolute top-2.5 left-2.5 z-10 bg-rose-600 text-white text-[10px] sm:text-[11px] font-black px-2.5 py-0.5 rounded-full shadow-md"
                        >
                            -{{ getDiscountPercent(product) }}% ছাড়
                        </span>
                    </Link>

                    <div class="relative bg-emerald-100/70 mt-auto flex flex-col pt-3.5 px-3.5 pb-3.5 rounded-b-[22px]">
                        <div class="absolute -top-[23px] left-0 right-0 w-full overflow-hidden leading-none pointer-events-none z-10">
                            <svg class="w-full h-[24px] text-emerald-100/70 fill-current" viewBox="0 0 300 24" preserveAspectRatio="none">
                                <path d="M 0,16 C 60,16 110,16 135,14 C 160,12 170,1 195,1 C 220,1 270,1 292,1 C 298,1 300,5 300,10 L 300,24 L 0,24 Z"></path>
                            </svg>
                        </div>

                        <div class="absolute -top-[17px] right-3 z-20 flex items-center gap-1.5">
                            <button
                                type="button"
                                @click.prevent.stop="toggleWishlist(product.id)"
                                class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-full bg-white hover:bg-rose-50 shadow-sm border border-emerald-100 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                :class="wishlistIds.includes(product.id) ? 'text-rose-600' : 'text-gray-600 hover:text-rose-600'"
                                title="পছন্দের তালিকা"
                            >
                                <svg class="w-3.5 h-3.5" :fill="wishlistIds.includes(product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                            <Link
                                :href="`/products/${product.slug || product.id}`"
                                class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-full bg-white hover:bg-blue-50 text-gray-600 hover:text-blue-600 shadow-sm border border-emerald-100 flex items-center justify-center transition active:scale-90"
                                title="বিস্তারিত দেখুন"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </Link>
                        </div>

                        <Link
                            :href="`/products/${product.slug || product.id}`"
                            class="font-bold text-gray-900 hover:text-[#0d3b37] transition truncate text-xs sm:text-[13px] mb-1 block relative z-10"
                            :title="product.name"
                        >
                            {{ product.name }}
                        </Link>

                        <div class="flex items-baseline justify-between gap-1 mb-2.5 relative z-10">
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="text-sm sm:text-base font-black text-gray-950">
                                    ৳{{ Number(product.sale_price || product.price).toLocaleString() }}
                                </span>
                                <span
                                    v-if="product.sale_price && product.sale_price < product.price"
                                    class="text-[11px] sm:text-xs text-gray-500/80 line-through font-medium"
                                >
                                    ৳{{ Number(product.price).toLocaleString() }}
                                </span>
                            </div>

                            <span class="text-[10px] text-gray-600 font-medium whitespace-nowrap">
                                {{ (product.id * 17 + 85) % 250 + 20 }} sold
                            </span>
                        </div>

                        <div class="flex items-center gap-1.5 relative z-10">
                            <button
                                @click="addToCart(product.id)"
                                :disabled="addingProductId === product.id"
                                :class="[
                                    'flex-1 rounded-xl py-2 px-2 text-xs font-bold flex items-center justify-center gap-1.5 transition-all duration-200 active:scale-95 shadow-sm border cursor-pointer',
                                    justAddedProductId === product.id
                                        ? 'bg-emerald-600 text-white border-emerald-600'
                                        : 'bg-white hover:bg-[#0d3b37] text-[#0d3b37] hover:text-white border-emerald-200/60 hover:border-[#0d3b37] group/btn'
                                ]"
                            >
                                <svg v-if="addingProductId === product.id" class="w-3.5 h-3.5 animate-spin text-current" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <svg v-else-if="justAddedProductId === product.id" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg v-else class="w-3.5 h-3.5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <span>{{ addingProductId === product.id ? 'যোগ হচ্ছে...' : (justAddedProductId === product.id ? 'যোগ হয়েছে!' : 'Add to Cart') }}</span>
                            </button>

                            <Link
                                :href="`/products/${product.slug || product.id}`"
                                class="w-8.5 h-8.5 rounded-xl bg-white hover:bg-[#0d3b37] text-gray-700 hover:text-white border border-emerald-200/60 hover:border-[#0d3b37] flex items-center justify-center transition active:scale-95 shadow-sm"
                                title="পণ্য দেখুন"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16 bg-white rounded-2xl border border-gray-200 p-8">
                <span class="text-5xl block mb-3">🔍</span>
                <h3 class="text-lg font-bold text-gray-800 mb-1">কোনো পণ্য পাওয়া যায়নি!</h3>
                <p class="text-gray-500 text-xs sm:text-sm">অন্য কোনো নাম বা ফিল্টার দিয়ে পুনরায় চেষ্টা করুন।</p>
                <button
                    @click="clearAllFilters()"
                    class="mt-4 inline-block text-xs bg-[#0d3b37] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[#064e3b] transition cursor-pointer"
                >
                    সকল পণ্য দেখুন
                </button>
            </div>

            <!-- Pagination -->
            <div v-if="products.links && products.links.length > 3" class="mt-10 flex justify-center gap-1.5 flex-wrap">
                <Link
                    v-for="(link, i) in products.links"
                    :key="i"
                    :href="link.url || '#'"
                    v-html="link.label"
                    :class="[
                        'px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition',
                        link.active ? 'bg-[#0d3b37] text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-emerald-50 border border-gray-300',
                        !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : ''
                    ]"
                />
            </div>
        </section>
    </AppLayout>
</template>
