<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    isHomeCategoryPersistent: {
        type: Boolean,
        default: false,
    }
});

const page = usePage();
const searchQuery = ref(page.props.filters?.search || '');
const isCategoryOpen = ref(false);
const hoveredCategory = ref(null);
const isMobileMenuOpen = ref(false);
const mobileMenuTab = ref('categories'); // 'categories' | 'menu'
const expandedMobileCategoryIds = ref([]);
const categoryDropdownRef = ref(null);

const flashSuccess = ref(page.props.flash?.success || '');
const flashError = ref(page.props.flash?.error || '');

watch(() => page.props.flash?.success, (val) => {
    flashSuccess.value = val || '';
    if (val) {
        setTimeout(() => {
            if (flashSuccess.value === val) flashSuccess.value = '';
        }, 3500);
    }
});

watch(() => page.props.flash?.error, (val) => {
    flashError.value = val || '';
    if (val) {
        setTimeout(() => {
            if (flashError.value === val) flashError.value = '';
        }, 4000);
    }
});

const isHome = computed(() => {
    const url = page.url.split('?')[0];
    const isHomePage = url === '/' || url === '';
    const hasCategoryFilter = !!page.props.filters?.category;
    const hasOfferFilter = !!page.props.filters?.on_sale;
    const hasSearchFilter = !!page.props.filters?.search;
    return isHomePage && !hasCategoryFilter && !hasOfferFilter && !hasSearchFilter;
});

const isActiveUrl = (targetPath, checkQuery = null) => {
    const [path, queryStr] = page.url.split('?');
    if (checkQuery) {
        const params = new URLSearchParams(queryStr || '');
        for (const [k, v] of Object.entries(checkQuery)) {
            if (params.get(k) !== String(v)) return false;
        }
        return true;
    }
    return path === targetPath && !queryStr;
};

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
    'grocery': '🛒',
    'oil': '🫒',
    'honey': '🍯',
    'attar': '🌸',
    'dates': '🌴',
    'fashion': '👔',
    'gadget': '⚡',
};

const getCategoryIcon = (cat) => {
    if (cat?.icon) return cat.icon;
    const str = `${cat?.slug || ''} ${cat?.name || ''}`.toLowerCase();
    for (const key in categoryIcons) {
        if (str.includes(key)) return categoryIcons[key];
    }
    return '🛍️';
};

const toggleMobileCategory = (catId) => {
    const idx = expandedMobileCategoryIds.value.indexOf(catId);
    if (idx > -1) {
        expandedMobileCategoryIds.value.splice(idx, 1);
    } else {
        expandedMobileCategoryIds.value.push(catId);
    }
};

const submitSearch = () => {
    router.get('/products', { search: searchQuery.value || undefined }, { preserveState: true });
};

const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 160;
};

const handleClickOutside = (e) => {
    if (isHome.value && !isScrolled.value && window.innerWidth >= 1024) {
        return;
    }
    if (categoryDropdownRef.value && !categoryDropdownRef.value.contains(e.target)) {
        isCategoryOpen.value = false;
        hoveredCategory.value = null;
    }
};

watch(() => page.url, () => {
    isCategoryOpen.value = false;
    hoveredCategory.value = null;
    isMobileMenuOpen.value = false;
});

let unregisterRouterHook = null;

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    document.addEventListener('click', handleClickOutside);
    handleScroll();

    unregisterRouterHook = router.on('navigate', () => {
        isCategoryOpen.value = false;
        hoveredCategory.value = null;
        isMobileMenuOpen.value = false;
    });
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    document.removeEventListener('click', handleClickOutside);
    if (unregisterRouterHook) {
        unregisterRouterHook();
    }
});

const getDiscountPercent = (product) => {
    const price = Number(product?.price || 0);
    const sale = Number(product?.sale_price || 0);
    if (price > 0 && sale > 0 && sale < price) {
        return Math.round(((price - sale) / price) * 100);
    }
    return 0;
};

const siteVal = (key, fallback = '') => {
    const site = page.props.app?.site;
    if (site && Object.prototype.hasOwnProperty.call(site, key) && site[key] !== null && site[key] !== undefined) {
        return String(site[key]).trim();
    }
    return fallback;
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#f8fafc] text-gray-800">
        <!-- Floating Toast / Alerts (No layout shift) -->
        <div class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none max-w-sm">
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="flashSuccess" class="bg-[#0d3b37] text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 pointer-events-auto border border-emerald-500/40">
                    <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-xs flex-shrink-0">✓</span>
                    <span class="text-xs sm:text-sm font-semibold">{{ flashSuccess }}</span>
                    <button @click="flashSuccess = ''" class="ml-auto text-emerald-200 hover:text-white text-xs pl-2">✕</button>
                </div>
            </transition>
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="flashError" class="bg-rose-700 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 pointer-events-auto border border-rose-500/40">
                    <span class="w-6 h-6 rounded-full bg-rose-500/30 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">⚠</span>
                    <span class="text-xs sm:text-sm font-semibold">{{ flashError }}</span>
                    <button @click="flashError = ''" class="ml-auto text-rose-200 hover:text-white text-xs pl-2">✕</button>
                </div>
            </transition>
        </div>

        <!-- Top Announcement / Utility Bar -->
        <div class="bg-[#0b2f2c] text-emerald-100 text-xs py-2 px-4 sm:px-6 lg:px-8 border-b border-emerald-900/50">
            <div class="max-w-[1360px] mx-auto flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-4">
                    <div v-if="siteVal('topbar_hotline', '16297') || siteVal('topbar_hours', 'সকাল ৯:০০ - রাত ১১:০০')" class="flex items-center gap-1.5 font-medium">
                        <span class="text-amber-400">📞</span>
                        <span>
                            <template v-if="siteVal('topbar_hotline', '16297')">হটলাইন: <strong class="text-white font-bold">{{ siteVal('topbar_hotline', '16297') }}</strong></template>
                            <template v-if="siteVal('topbar_hours', 'সকাল ৯:০০ - রাত ১১:০০')"> ({{ siteVal('topbar_hours', 'সকাল ৯:০০ - রাত ১১:০০') }})</template>
                        </span>
                    </div>
                    <span v-if="siteVal('topbar_hotline', '16297') || siteVal('topbar_hours', 'সকাল ৯:০০ - রাত ১১:০০')" class="hidden sm:inline opacity-30">|</span>
                    <Link
                        href="/products?on_sale=1"
                        class="hidden sm:inline-flex items-center gap-1 text-amber-300 hover:text-amber-200 font-semibold transition"
                    >
                        <span>🔥 বিশেষ মূল্যছাড় চলছে!</span>
                    </Link>
                </div>
                <div class="flex items-center gap-3 sm:gap-4 text-xs">
                    <Link href="/track-order" class="hover:text-amber-300 transition flex items-center gap-1 font-medium">
                        <span>🚚</span>
                        <span>অর্ডার ট্র্যাক করুন</span>
                    </Link>
                    <span class="opacity-40">|</span>
                    <Link href="/orders" class="hover:text-amber-300 transition hidden sm:inline">আমার অর্ডার</Link>
                    <template v-if="siteVal('topbar_slogan', '🌙 আল-আকসা ইসলামিক উদ্যোগ')">
                        <span class="opacity-40 hidden sm:inline">|</span>
                        <span class="text-amber-400 font-semibold">{{ siteVal('topbar_slogan', '🌙 আল-আকসা ইসলামিক উদ্যোগ') }}</span>
                    </template>
                </div>
            </div>
        </div>

        <!-- Main Header (Sticky on scroll) -->
        <header class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-xs">
            <!-- Middle Section: Mobile Hamburger, Logo, Search, and Help/Cart -->
            <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
                <div class="flex items-center justify-between gap-3 md:gap-8">
                    <div class="flex items-center gap-2.5">
                        <!-- Mobile Menu Trigger -->
                        <button
                            type="button"
                            @click="isMobileMenuOpen = true"
                            class="md:hidden p-2 rounded-lg text-[#0d3b37] hover:bg-emerald-50 border border-gray-200 transition"
                            aria-label="Open Menu"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Logo -->
                        <Link href="/" class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0 group">
                            <img
                                :src="$page.props.app?.logo || '/images/logo.png'"
                                :alt="siteVal('brand_name', 'Al-Aqsa Mart')"
                                :style="{ height: `${Number(siteVal('logo_height', '68')) || 68}px` }"
                                class="w-auto max-w-[200px] sm:max-w-[260px] md:max-w-[320px] object-contain group-hover:scale-105 transition duration-300"
                            />
                            <div
                                v-if="siteVal('company_primary_name', 'AL-AQSA') !== '' || siteVal('company_secondary_name', 'MART') !== ''"
                                class="flex flex-col"
                            >
                                <span
                                    v-if="siteVal('company_primary_name', 'AL-AQSA') !== ''"
                                    class="text-lg sm:text-xl md:text-2xl font-black text-[#0d3b37] tracking-tight leading-none group-hover:text-emerald-700 transition"
                                >
                                    {{ siteVal('company_primary_name', 'AL-AQSA') }}
                                </span>
                                <span
                                    v-if="siteVal('company_secondary_name', 'MART') !== ''"
                                    class="text-[10px] md:text-[11px] font-bold text-[#b58d3d] tracking-widest leading-tight"
                                >
                                    {{ siteVal('company_secondary_name', 'MART') }}
                                </span>
                            </div>
                        </Link>
                    </div>

                    <!-- Search Box (Centrally aligned & prominent) -->
                    <form @submit.prevent="submitSearch" class="flex-1 max-w-2xl hidden md:flex items-center">
                        <div class="flex w-full items-stretch rounded-lg border-2 border-[#0d3b37] overflow-hidden shadow-xs focus-within:ring-2 focus-within:ring-emerald-400/40">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="পণ্য, ক্যাটাগরি বা ব্র্যান্ড খুঁজুন (যেমন: বই, মধু, আতর, গ্যাজেট)..."
                                class="w-full px-4 py-2 text-sm text-gray-800 placeholder-gray-400 bg-white border-none focus:outline-none focus:ring-0"
                            />
                            <button
                                v-if="searchQuery"
                                type="button"
                                @click="searchQuery = ''; submitSearch()"
                                class="px-2 text-gray-400 hover:text-gray-600 text-xs"
                                title="মুছে ফেলুন"
                            >
                                ✕
                            </button>
                            <button
                                type="submit"
                                class="bg-[#0d3b37] hover:bg-[#064e3b] text-white px-6 font-semibold text-sm transition flex items-center justify-center gap-1.5 flex-shrink-0 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span>Search</span>
                            </button>
                        </div>
                    </form>

                    <!-- Header Right: Track Order, Help & Cart preview -->
                    <div class="flex items-center gap-2.5 sm:gap-4 flex-shrink-0">
                        <div v-if="siteVal('support_phone', '01700-000000')" class="hidden xl:flex items-center gap-2 text-left pl-2 pr-4 py-1 border-r border-gray-200">
                            <div class="w-9 h-9 rounded-full bg-emerald-50 text-[#0d3b37] flex items-center justify-center font-bold">
                                📞
                            </div>
                            <div class="text-xs leading-tight">
                                <span class="text-gray-400 block text-[10px]">সাহায্য ও অনুসন্ধান</span>
                                <strong class="text-[#0d3b37] font-bold text-sm">{{ siteVal('support_phone', '01700-000000') }}</strong>
                            </div>
                        </div>

                        <!-- Cart Quick Button -->
                        <Link href="/cart" class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 hover:border-[#0d3b37] hover:bg-emerald-50/40 transition">
                            <div class="relative">
                                <svg class="w-6 h-6 text-[#0d3b37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span
                                    v-if="$page.props.cart?.count > 0"
                                    class="absolute -top-2 -right-2 bg-[#b58d3d] text-white text-[10px] font-black rounded-full min-w-4 h-4 px-1 flex items-center justify-center shadow-xs"
                                >
                                    {{ $page.props.cart.count }}
                                </span>
                            </div>
                            <div class="hidden sm:block text-left text-xs leading-tight">
                                <span class="text-gray-400 block text-[10px]">আমার কার্ট</span>
                                <span class="font-bold text-gray-800">৳{{ $page.props.cart?.subtotal || 0 }}</span>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Mobile Search Form -->
                <form @submit.prevent="submitSearch" class="mt-2.5 md:hidden flex">
                    <div class="flex w-full items-stretch rounded-lg border-2 border-[#0d3b37] overflow-hidden">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="পণ্য, ব্র্যান্ড বা ক্যাটাগরি খুঁজুন..."
                            class="w-full px-3 py-2 text-sm text-gray-800 bg-white border-none focus:outline-none"
                        />
                        <button type="submit" class="bg-[#0d3b37] text-white px-4 text-sm font-semibold flex items-center justify-center">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Deep Emerald Navigation Bar (Main Menu + Mega Category Submenu) -->
            <div class="bg-[#0d3b37] text-white relative">
                <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-12">
                        <!-- Left: ALL CATEGORIES Button & Attached Mega Flyout Menu -->
                        <div
                            class="relative flex-shrink-0 w-56 sm:w-64 lg:w-[270px]"
                            ref="categoryDropdownRef"
                            @mouseleave="hoveredCategory = null"
                        >
                            <button
                                @click="isCategoryOpen = !isCategoryOpen"
                                class="w-full flex items-center justify-between bg-[#082a27] hover:bg-[#051f1c] text-white px-4 lg:px-5 h-12 font-bold text-xs sm:text-sm tracking-wide uppercase transition select-none cursor-pointer"
                                :class="{ 'bg-[#051f1c]': isCategoryOpen }"
                            >
                                <span class="flex items-center gap-2.5">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                    <span>ALL CATEGORIES</span>
                                </span>
                                <svg
                                    class="w-4 h-4 text-gray-300 transition-transform duration-200"
                                    :class="{ 'rotate-180': isCategoryOpen }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Vertical Category Menu Dropdown + Right Mega Submenu Flyout -->
                            <div
                                :class="[
                                    isHome && !isScrolled
                                        ? (isCategoryOpen ? 'block lg:hidden' : 'hidden')
                                        : (isCategoryOpen ? 'block' : 'hidden')
                                ]"
                                class="absolute left-0 top-full w-full bg-white text-gray-800 shadow-2xl border border-t-0 border-gray-200 z-50 py-1 font-sans rounded-b-xl"
                            >
                                <div class="divide-y divide-gray-100 max-h-[420px] overflow-y-auto">
                                    <div
                                        v-for="cat in $page.props.app?.categories || []"
                                        :key="cat.id"
                                        @mouseenter="hoveredCategory = cat"
                                        class="relative"
                                    >
                                        <Link
                                            :href="`/products?category=${cat.slug}`"
                                            @click="isCategoryOpen = false; hoveredCategory = null"
                                            class="flex items-center justify-between px-4 py-2.5 hover:bg-emerald-50/70 hover:text-[#0d3b37] transition group text-[13px] font-medium text-gray-700"
                                            :class="{ 'bg-emerald-50/80 text-[#0d3b37] font-semibold': hoveredCategory?.id === cat.id }"
                                        >
                                            <div class="flex items-center gap-3 truncate">
                                                <span class="text-base flex-shrink-0 group-hover:scale-110 transition-transform duration-150">
                                                    {{ getCategoryIcon(cat) }}
                                                </span>
                                                <span class="truncate group-hover:font-semibold">{{ cat.name }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span v-if="cat.products_count" class="text-[10px] text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full">
                                                    {{ cat.products_count }}
                                                </span>
                                                <span class="text-gray-400 group-hover:text-[#0d3b37] group-hover:translate-x-0.5 transition-transform text-sm font-semibold">
                                                    ›
                                                </span>
                                            </div>
                                        </Link>
                                    </div>

                                    <!-- If no categories -->
                                    <div v-if="!$page.props.app?.categories?.length" class="p-4 text-center text-xs text-gray-400">
                                        কোনো ক্যাটাগরি পাওয়া যায়নি
                                    </div>
                                </div>

                                <!-- Right-Side Mega Flyout Submenu (Desktop Hover) -->
                                <div
                                    v-if="hoveredCategory && (hoveredCategory.children?.length || hoveredCategory.brands?.length || hoveredCategory.preview_products?.length)"
                                    class="hidden lg:flex flex-col justify-between absolute left-full top-0 w-[560px] xl:w-[660px] min-h-[360px] bg-white text-gray-800 shadow-2xl border border-gray-200 rounded-r-2xl p-5 z-50"
                                >
                                    <div>
                                        <!-- Flyout Header -->
                                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                                            <div class="flex items-center gap-2.5">
                                                <span class="text-2xl">{{ getCategoryIcon(hoveredCategory) }}</span>
                                                <div>
                                                    <h3 class="font-black text-[#0d3b37] text-base leading-tight">
                                                        {{ hoveredCategory.name }}
                                                    </h3>
                                                    <p class="text-[11px] text-gray-400">
                                                        মোট {{ hoveredCategory.products_count || 0 }} টি পণ্য উপলব্ধ
                                                    </p>
                                                </div>
                                            </div>
                                            <Link
                                                :href="`/products?category=${hoveredCategory.slug}`"
                                                @click="isCategoryOpen = false; hoveredCategory = null"
                                                class="text-xs font-bold text-[#0d3b37] hover:text-white bg-emerald-50 hover:bg-[#0d3b37] px-3 py-1.5 rounded-lg transition"
                                            >
                                                সব দেখুন →
                                            </Link>
                                        </div>

                                        <!-- Subcategories Grid (If Parent Category has Subcategories) -->
                                        <div v-if="hoveredCategory.children?.length" class="mb-5">
                                            <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5">
                                                সাব-ক্যাটাগরি সমূহ (Subcategories)
                                            </h4>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                <Link
                                                    v-for="sub in hoveredCategory.children"
                                                    :key="sub.id"
                                                    :href="`/products?category=${sub.slug}`"
                                                    @click="isCategoryOpen = false; hoveredCategory = null"
                                                    class="flex items-center justify-between px-3 py-2 rounded-lg border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/50 text-xs font-semibold text-gray-700 hover:text-[#0d3b37] transition"
                                                >
                                                    <span class="truncate flex items-center gap-1.5">
                                                        <span>{{ getCategoryIcon(sub) }}</span>
                                                        <span class="truncate">{{ sub.name }}</span>
                                                    </span>
                                                    <span v-if="sub.products_count" class="text-[10px] text-gray-400 ml-1">({{ sub.products_count }})</span>
                                                </Link>
                                            </div>
                                        </div>

                                        <!-- Popular Brands in Category -->
                                        <div v-if="hoveredCategory.brands?.length" class="mb-5">
                                            <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">
                                                জনপ্রিয় ব্র্যান্ড (Brands)
                                            </h4>
                                            <div class="flex flex-wrap gap-1.5">
                                                <Link
                                                    v-for="brand in hoveredCategory.brands"
                                                    :key="brand"
                                                    :href="`/products?category=${hoveredCategory.slug}&brand=${encodeURIComponent(brand)}`"
                                                    @click="isCategoryOpen = false; hoveredCategory = null"
                                                    class="px-2.5 py-1 rounded-full bg-gray-100 hover:bg-[#0d3b37] text-gray-700 hover:text-white text-xs font-semibold transition"
                                                >
                                                    🏷️ {{ brand }}
                                                </Link>
                                            </div>
                                        </div>

                                        <!-- Featured Products Preview -->
                                        <div v-if="hoveredCategory.preview_products?.length">
                                            <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5">
                                                জনপ্রিয় পণ্যসমূহ (Top Picks)
                                            </h4>
                                            <div class="grid grid-cols-3 gap-3">
                                                <Link
                                                    v-for="prod in hoveredCategory.preview_products"
                                                    :key="prod.id"
                                                    :href="`/products/${prod.slug || prod.id}`"
                                                    @click="isCategoryOpen = false; hoveredCategory = null"
                                                    class="group border border-gray-100 hover:border-emerald-300 rounded-xl p-2 flex flex-col bg-gray-50/50 hover:bg-white hover:shadow-md transition"
                                                >
                                                    <div class="aspect-square w-full rounded-lg bg-white overflow-hidden mb-2 relative">
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
                        </div>

                        <!-- Center: Main Menu Links with Submenu Dropdown -->
                        <nav class="hidden md:flex items-center gap-1 lg:gap-1.5 flex-1 px-3 lg:px-5">
                            <Link
                                href="/"
                                class="px-3 py-1.5 rounded-md text-xs lg:text-sm font-semibold hover:bg-white/10 transition whitespace-nowrap"
                                :class="isHome ? 'text-amber-300 bg-white/10 font-bold' : 'text-white'"
                            >
                                হোম
                            </Link>

                            <Link
                                href="/products"
                                class="px-3 py-1.5 rounded-md text-xs lg:text-sm font-semibold hover:bg-white/10 transition whitespace-nowrap"
                                :class="isActiveUrl('/products') ? 'text-amber-300 bg-white/10 font-bold' : 'text-white'"
                            >
                                সকল পণ্য
                            </Link>

                            <!-- Special Offers Link with Hot Badge -->
                            <Link
                                href="/products?on_sale=1"
                                class="px-3 py-1.5 rounded-md text-xs lg:text-sm font-bold hover:bg-white/15 transition flex items-center gap-1.5 whitespace-nowrap"
                                :class="$page.props.filters?.on_sale ? 'bg-amber-400 text-[#082a27] shadow-xs' : 'text-amber-300'"
                            >
                                <span>🔥 বিশেষ অফার</span>
                                <span
                                    v-if="$page.props.app?.offers_count > 0"
                                    class="text-[10px] font-black px-1.5 py-0.2 rounded-full"
                                    :class="$page.props.filters?.on_sale ? 'bg-[#082a27] text-amber-300' : 'bg-rose-600 text-white'"
                                >
                                    {{ $page.props.app.offers_count }}
                                </span>
                            </Link>

                            <!-- New Arrivals -->
                            <Link
                                href="/products?sort=latest"
                                class="px-3 py-1.5 rounded-md text-xs lg:text-sm font-semibold hover:bg-white/10 transition whitespace-nowrap hidden lg:inline-block"
                                :class="isActiveUrl('/products', { sort: 'latest' }) ? 'text-amber-300 bg-white/10 font-bold' : 'text-white'"
                            >
                                নতুন কালেকশন
                            </Link>

                            <!-- Categories Hover Mega Submenu in Main Menu Bar -->
                            <div class="relative group">
                                <button
                                    type="button"
                                    class="px-3 py-1.5 rounded-md text-xs lg:text-sm font-semibold text-white hover:bg-white/10 transition flex items-center gap-1 whitespace-nowrap cursor-pointer"
                                >
                                    <span>ক্যাটাগরিসমূহ</span>
                                    <svg class="w-3.5 h-3.5 text-amber-300 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Dropdown Submenu on Hover -->
                                <div class="invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 absolute left-0 top-full pt-1 z-50 w-[520px] xl:w-[640px]">
                                    <div class="bg-white text-gray-800 rounded-2xl shadow-2xl border border-gray-200 p-5 grid grid-cols-2 sm:grid-cols-3 gap-4 max-h-[420px] overflow-y-auto">
                                        <div
                                            v-for="cat in $page.props.app?.categories || []"
                                            :key="cat.id"
                                            class="space-y-1.5"
                                        >
                                            <Link
                                                :href="`/products?category=${cat.slug}`"
                                                class="flex items-center gap-2 font-bold text-xs sm:text-sm text-[#0d3b37] hover:text-emerald-700 transition pb-1 border-b border-gray-100"
                                            >
                                                <span>{{ getCategoryIcon(cat) }}</span>
                                                <span class="truncate">{{ cat.name }}</span>
                                            </Link>

                                            <!-- Subcategories under this category -->
                                            <ul v-if="cat.children?.length" class="space-y-1 pl-5">
                                                <li v-for="sub in cat.children" :key="sub.id">
                                                    <Link
                                                        :href="`/products?category=${sub.slug}`"
                                                        class="text-xs text-gray-600 hover:text-[#0d3b37] hover:underline block truncate"
                                                    >
                                                        • {{ sub.name }}
                                                    </Link>
                                                </li>
                                            </ul>
                                            <p v-else class="text-[11px] text-gray-400 pl-5">
                                                {{ cat.products_count || 0 }} টি পণ্য
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Track Order Link -->
                            <Link
                                href="/track-order"
                                class="px-3 py-1.5 rounded-md text-xs lg:text-sm font-semibold hover:bg-white/10 transition flex items-center gap-1 whitespace-nowrap"
                                :class="page.url.startsWith('/track-order') ? 'text-amber-300 bg-white/10 font-bold' : 'text-white'"
                            >
                                <span>🚚 অর্ডার ট্র্যাকিং</span>
                            </Link>
                        </nav>

                        <!-- Right: Login / Sign Up (or User Profile) -->
                        <div class="flex items-center gap-3">
                            <template v-if="$page.props.auth?.user">
                                <div class="flex items-center gap-2">
                                    <Link
                                        href="/orders"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs sm:text-sm font-semibold text-white hover:bg-white/10 transition"
                                    >
                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="max-w-[100px] sm:max-w-[140px] truncate">{{ $page.props.auth.user.name }}</span>
                                    </Link>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        class="text-xs bg-rose-600/80 hover:bg-rose-600 text-white px-2.5 py-1 rounded transition cursor-pointer"
                                    >
                                        লগআউট
                                    </Link>
                                </div>
                            </template>
                            <template v-else>
                                <Link
                                    href="/login"
                                    class="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-white hover:text-amber-300 px-3 py-1.5 rounded-md hover:bg-white/10 transition"
                                >
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Login / Sign Up</span>
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile Slide-Over Navigation Drawer -->
        <transition
            enter-active-class="transition-opacity ease-linear duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-linear duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isMobileMenuOpen" class="fixed inset-0 z-[100] md:hidden flex">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/50 backdrop-blur-xs" @click="isMobileMenuOpen = false"></div>

                <!-- Drawer Content -->
                <div class="relative w-80 max-w-[85vw] bg-white h-full shadow-2xl flex flex-col z-10 overflow-hidden">
                    <!-- Drawer Header -->
                    <div class="bg-[#0d3b37] text-white px-4 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="text-lg font-black tracking-tight text-amber-300 uppercase">{{ $page.props.app?.site?.brand_name || 'AL-AQSA MART' }}</span>
                        </div>
                        <button
                            @click="isMobileMenuOpen = false"
                            class="w-8 h-8 rounded-lg bg-white/10 text-white flex items-center justify-center hover:bg-white/20"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Drawer Tabs -->
                    <div class="grid grid-cols-2 border-b border-gray-200 bg-gray-50 text-xs font-bold uppercase">
                        <button
                            @click="mobileMenuTab = 'categories'"
                            class="py-3 text-center border-b-2 transition"
                            :class="mobileMenuTab === 'categories' ? 'border-[#0d3b37] text-[#0d3b37] bg-white' : 'border-transparent text-gray-500'"
                        >
                            ক্যাটাগরিসমূহ
                        </button>
                        <button
                            @click="mobileMenuTab = 'menu'"
                            class="py-3 text-center border-b-2 transition"
                            :class="mobileMenuTab === 'menu' ? 'border-[#0d3b37] text-[#0d3b37] bg-white' : 'border-transparent text-gray-500'"
                        >
                            মেইন মেনু
                        </button>
                    </div>

                    <!-- Tab 1: Categories & Subcategories -->
                    <div v-if="mobileMenuTab === 'categories'" class="flex-1 overflow-y-auto divide-y divide-gray-100">
                        <div v-for="cat in $page.props.app?.categories || []" :key="cat.id">
                            <div class="flex items-center justify-between px-4 py-3 hover:bg-emerald-50/50">
                                <Link
                                    :href="`/products?category=${cat.slug}`"
                                    @click="isMobileMenuOpen = false"
                                    class="flex items-center gap-3 text-sm font-semibold text-gray-800 flex-1"
                                >
                                    <span>{{ getCategoryIcon(cat) }}</span>
                                    <span>{{ cat.name }}</span>
                                    <span v-if="cat.products_count" class="text-[11px] text-gray-400">({{ cat.products_count }})</span>
                                </Link>
                                <button
                                    v-if="cat.children?.length"
                                    @click="toggleMobileCategory(cat.id)"
                                    class="p-1.5 rounded-md bg-gray-100 text-gray-600 hover:bg-emerald-100 hover:text-[#0d3b37]"
                                >
                                    <svg
                                        class="w-4 h-4 transition-transform"
                                        :class="{ 'rotate-180': expandedMobileCategoryIds.includes(cat.id) }"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Mobile Subcategories Accordion -->
                            <div
                                v-if="cat.children?.length && expandedMobileCategoryIds.includes(cat.id)"
                                class="bg-emerald-50/40 pl-9 pr-4 py-2 space-y-1.5 border-t border-emerald-100/60"
                            >
                                <Link
                                    v-for="sub in cat.children"
                                    :key="sub.id"
                                    :href="`/products?category=${sub.slug}`"
                                    @click="isMobileMenuOpen = false"
                                    class="flex items-center justify-between py-1.5 text-xs font-medium text-gray-700 hover:text-[#0d3b37]"
                                >
                                    <span>• {{ sub.name }}</span>
                                    <span v-if="sub.products_count" class="text-[10px] text-gray-400">{{ sub.products_count }}</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Main Menu Links -->
                    <div v-else class="flex-1 overflow-y-auto p-4 space-y-2 text-sm font-semibold">
                        <Link href="/" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-gray-800">
                            <span>🏠</span> <span>হোম পেজ</span>
                        </Link>
                        <Link href="/products" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-gray-800">
                            <span>🛍️</span> <span>সকল পণ্য</span>
                        </Link>
                        <Link href="/products?on_sale=1" @click="isMobileMenuOpen = false" class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-rose-50 text-rose-700 font-bold">
                            <span class="flex items-center gap-3"><span>🔥</span> <span>বিশেষ অফার (Flash Deals)</span></span>
                            <span v-if="$page.props.app?.offers_count" class="bg-rose-600 text-white text-xs px-2 py-0.5 rounded-full">
                                {{ $page.props.app.offers_count }}
                            </span>
                        </Link>
                        <Link href="/products?sort=latest" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-gray-800">
                            <span>✨</span> <span>নতুন কালেকশন</span>
                        </Link>
                        <Link href="/track-order" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-gray-800">
                            <span>🚚</span> <span>অর্ডার ট্র্যাক করুন</span>
                        </Link>
                        <Link href="/cart" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-gray-800">
                            <span>🛒</span> <span>আমার কার্ট ({{ $page.props.cart?.count || 0 }})</span>
                        </Link>
                        <Link href="/orders" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-gray-800">
                            <span>📦</span> <span>আমার অর্ডারসমূহ</span>
                        </Link>
                    </div>

                    <!-- Drawer Footer -->
                    <div class="p-4 border-t border-gray-200 bg-gray-50 text-xs text-gray-600">
                        <p class="font-bold text-[#0d3b37]">📞 হটলাইন: {{ $page.props.app?.site?.topbar_hotline || '16297' }} / {{ $page.props.app?.site?.support_phone || '01700-000000' }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $page.props.app?.site?.topbar_hours || 'সকাল ৯:০০ থেকে রাত ১১:০০ পর্যন্ত' }}</p>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Main Content (Standard Balanced Container: max-w-[1360px]) -->
        <main :class="['flex-1 max-w-[1360px] w-full mx-auto px-4 sm:px-6 lg:px-8', isHome ? 'pt-4 pb-6' : 'py-6']">
            <slot />
        </main>

        <!-- Complete 4-Column Dynamic E-Commerce Footer -->
        <footer
            class="text-gray-300 pt-12 pb-8 mt-auto border-t border-emerald-900/60"
            :style="{ backgroundColor: $page.props.app?.site?.footer_bg_color || '#07221f' }"
        >
            <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-white/10">
                    <!-- Column 1: Brand & About -->
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <img :src="$page.props.app?.logo || '/images/logo.png'" :alt="siteVal('brand_name', 'Al-Aqsa Mart')" class="h-14 sm:h-16 w-auto max-w-[220px] object-contain bg-white px-2.5 py-1.5 rounded-xl shadow-xs" />
                            <div v-if="siteVal('brand_name', 'AL-AQSA MART') || siteVal('company_tagline', 'Islamic & Halal Shop')">
                                <span v-if="siteVal('brand_name', 'AL-AQSA MART')" class="text-xl font-black text-white block leading-none uppercase">
                                    {{ siteVal('brand_name', 'AL-AQSA MART') }}
                                </span>
                                <span v-if="siteVal('company_tagline', 'Islamic & Halal Shop')" class="text-[10px] font-bold text-[#b58d3d] tracking-widest uppercase">
                                    {{ siteVal('company_tagline', 'Islamic & Halal Shop') }}
                                </span>
                            </div>
                        </div>
                        <p v-if="siteVal('footer_description', 'আপনার বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম। সেরা মানের খাঁটি, অর্গানিক ও হালাল পণ্য দ্রুততম সময়ে সারা বাংলাদেশে আপনার দোরগোড়ায় পৌঁছে দেওয়াই আমাদের লক্ষ্য।')" class="text-xs sm:text-sm text-gray-400 leading-relaxed mb-4">
                            {{ siteVal('footer_description', 'আপনার বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম। সেরা মানের খাঁটি, অর্গানিক ও হালাল পণ্য দ্রুততম সময়ে সারা বাংলাদেশে আপনার দোরগোড়ায় পৌঁছে দেওয়াই আমাদের লক্ষ্য।') }}
                        </p>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-emerald-300 font-semibold mb-3">
                            <span v-if="$page.props.app?.site?.footer_badge_1 !== ''" class="px-2.5 py-1 rounded-full bg-emerald-950/80 border border-emerald-800">
                                {{ $page.props.app?.site?.footer_badge_1 || '✓ ১০০% হালাল ও খাঁটি' }}
                            </span>
                            <span v-if="$page.props.app?.site?.footer_badge_2 !== ''" class="px-2.5 py-1 rounded-full bg-emerald-950/80 border border-emerald-800">
                                {{ $page.props.app?.site?.footer_badge_2 || '✓ ক্যাশ অন ডেলিভারি' }}
                            </span>
                        </div>
                        <!-- Optional Social Links -->
                        <div v-if="$page.props.app?.site?.facebook_url || $page.props.app?.site?.youtube_url || $page.props.app?.site?.whatsapp_number" class="flex items-center gap-2 mt-3">
                            <a v-if="$page.props.app?.site?.facebook_url" :href="$page.props.app.site.facebook_url" target="_blank" class="px-2.5 py-1 rounded-lg bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white text-xs font-bold transition">
                                Facebook
                            </a>
                            <a v-if="$page.props.app?.site?.youtube_url" :href="$page.props.app.site.youtube_url" target="_blank" class="px-2.5 py-1 rounded-lg bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white text-xs font-bold transition">
                                YouTube
                            </a>
                            <a v-if="$page.props.app?.site?.whatsapp_number" :href="`https://wa.me/${$page.props.app.site.whatsapp_number}`" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white text-xs font-bold transition">
                                WhatsApp
                            </a>
                        </div>
                    </div>

                    <!-- Column 2: Quick Links -->
                    <div>
                        <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-[#b58d3d] rounded-full"></span>
                            <span>গুরুত্বপূর্ণ লিংক</span>
                        </h4>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-gray-400">
                            <li><Link href="/" class="hover:text-amber-300 transition">হোম পেজ</Link></li>
                            <li><Link href="/products" class="hover:text-amber-300 transition">সকল পণ্য সমূহ</Link></li>
                            <li><Link href="/products?on_sale=1" class="hover:text-amber-300 transition text-amber-300 font-semibold">🔥 বিশেষ অফার ও ডিসকাউন্ট</Link></li>
                            <li><Link href="/products?sort=latest" class="hover:text-amber-300 transition">নতুন কালেকশন (New Arrivals)</Link></li>
                            <li><Link href="/cart" class="hover:text-amber-300 transition">শপিং কার্ট</Link></li>
                        </ul>
                    </div>

                    <!-- Column 3: Customer Service -->
                    <div>
                        <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-[#b58d3d] rounded-full"></span>
                            <span>গ্রাহক সেবা</span>
                        </h4>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-gray-400">
                            <li><Link href="/track-order" class="hover:text-amber-300 transition font-semibold text-emerald-300">🚚 অর্ডার ট্র্যাকিং (Track Order)</Link></li>
                            <li><Link href="/orders" class="hover:text-amber-300 transition">আমার অর্ডার হিস্ট্রি</Link></li>
                            <li><Link href="/checkout" class="hover:text-amber-300 transition">চেকআউট পেজ</Link></li>
                            <li v-if="$page.props.app?.site?.footer_service_1 !== ''"><span class="text-gray-400">{{ $page.props.app?.site?.footer_service_1 || '৭ দিনের সহজ রিটার্ন পলিসি' }}</span></li>
                            <li v-if="$page.props.app?.site?.footer_service_2 !== ''"><span class="text-gray-400">{{ $page.props.app?.site?.footer_service_2 || 'সারা বাংলাদেশে হোম ডেলিভারি' }}</span></li>
                        </ul>
                    </div>

                    <!-- Column 4: Contact & Payment Methods -->
                    <div>
                        <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-[#b58d3d] rounded-full"></span>
                            <span>যোগাযোগ ও পেমেন্ট</span>
                        </h4>
                        <div class="space-y-2 text-xs sm:text-sm text-gray-400 mb-4">
                            <p>📍 {{ $page.props.app?.site?.company_address || 'ঢাকা, বাংলাদেশ' }}</p>
                            <p>📞 হটলাইন: <strong class="text-white">{{ $page.props.app?.site?.support_phone || '01700-000000' }}</strong><template v-if="$page.props.app?.site?.topbar_hotline">, <strong class="text-white">{{ $page.props.app.site.topbar_hotline }}</strong></template></p>
                            <p>✉️ ইমেইল: {{ $page.props.app?.site?.support_email || 'support@alaqsamart.com' }}</p>
                        </div>
                        <div v-if="$page.props.app?.site?.footer_payment_methods !== ''">
                            <span class="text-[11px] uppercase tracking-wider text-gray-400 block mb-2 font-semibold">গ্রহণযোগ্য পেমেন্ট মাধ্যম:</span>
                            <div class="flex flex-wrap gap-1.5 text-[11px] font-bold">
                                <span
                                    v-for="(pm, idx) in ($page.props.app?.site?.footer_payment_methods || 'Cash on Delivery, bKash, Nagad, Rocket').split(',').map(s => s.trim()).filter(Boolean)"
                                    :key="idx"
                                    class="px-2.5 py-1 rounded text-white"
                                    :class="[
                                        pm.toLowerCase().includes('bkash') ? 'bg-pink-600/80' :
                                        pm.toLowerCase().includes('nagad') ? 'bg-orange-600/80' :
                                        pm.toLowerCase().includes('rocket') ? 'bg-purple-600/80' :
                                        'bg-white/10'
                                    ]"
                                >
                                    {{ pm }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Copyright Bar -->
                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-400">
                    <p>{{ $page.props.app?.site?.footer_copyright || `© ${new Date().getFullYear()} Al-Aqsa Mart. সর্বস্বত্ব সংরক্ষিত।` }}</p>
                    <p class="text-emerald-400/90 font-medium">{{ $page.props.app?.site?.footer_bottom_tagline || '🌙 বিশ্বস্ততা ও হালাল বাণিজ্যের অঙ্গীকার' }}</p>
                </div>
            </div>
        </footer>
    </div>
</template>
