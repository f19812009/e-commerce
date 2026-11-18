<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    product: Object,
    related: Array,
});

const resolveImageUrl = (img) => {
    if (!img) return 'https://placehold.co/600x600?text=Al-Aqsa+Mart';
    return img.startsWith('http') ? img : `/storage/${img}`;
};

// Gallery Images (main image + gallery array)
const allImages = computed(() => {
    const list = [];
    if (props.product.image) list.push(resolveImageUrl(props.product.image));
    if (Array.isArray(props.product.gallery)) {
        props.product.gallery.forEach((g) => {
            if (g) list.push(resolveImageUrl(g));
        });
    }
    return list.length > 0 ? list : ['https://placehold.co/600x600?text=Al-Aqsa+Mart'];
});

const activeImage = ref(allImages.value[0]);

// Variants, Colors & Sizes
const variants = computed(() => Array.isArray(props.product.variants) ? props.product.variants : []);
const specs = computed(() => props.product.specifications && typeof props.product.specifications === 'object' ? props.product.specifications : {});

const selectedVariantIndex = ref(variants.value.length > 0 ? 0 : null);
const selectedColor = ref(Array.isArray(specs.value.colors) && specs.value.colors.length > 0 ? specs.value.colors[0] : null);
const selectedSize = ref(Array.isArray(specs.value.sizes) && specs.value.sizes.length > 0 ? specs.value.sizes[0] : null);

const activeVariant = computed(() => {
    if (selectedVariantIndex.value !== null && variants.value[selectedVariantIndex.value]) {
        return variants.value[selectedVariantIndex.value];
    }
    return null;
});

const effectivePrice = computed(() => {
    if (activeVariant.value && activeVariant.value.price && Number(activeVariant.value.price) > 0) {
        return Number(activeVariant.value.price);
    }
    return Number(props.product.sale_price || props.product.price);
});

const effectiveStock = computed(() => {
    if (activeVariant.value && activeVariant.value.stock !== undefined && activeVariant.value.stock !== null && activeVariant.value.stock !== '') {
        return Number(activeVariant.value.stock);
    }
    return Number(props.product.stock || 0);
});

const selectedOptionLabel = computed(() => {
    const parts = [];
    if (activeVariant.value?.name) {
        parts.push(activeVariant.value.name);
    } else {
        if (selectedColor.value) parts.push(`Color: ${selectedColor.value}`);
        if (selectedSize.value) parts.push(`Size: ${selectedSize.value}`);
    }
    return parts.length > 0 ? parts.join(' | ') : null;
});

const specLabels = {
    model_name: 'মডেল (Model)',
    ram: 'র‍্যাম (RAM)',
    storage: 'স্টোরেজ (Storage / ROM)',
    processor: 'প্রসেসর (Processor / CPU)',
    graphics: 'গ্রাফিক্স (Graphics / GPU)',
    display: 'ডিসপ্লে (Display)',
    screen_size: 'স্ক্রিন সাইজ (Screen Size)',
    resolution: 'রেজোলিউশন (Resolution)',
    panel_type: 'প্যানেল টাইপ (Panel Type)',
    refresh_rate: 'রিফ্রেশ রেট (Refresh Rate)',
    response_time: 'রেসপন্স টাইম (Response Time)',
    camera: 'ক্যামেরা (Camera)',
    battery: 'ব্যাটারি ও চার্জিং (Battery)',
    sim_network: 'সিম ও নেটওয়ার্ক (SIM & Network)',
    os: 'অপারেটিং সিস্টেম (OS)',
    keyboard_ports: 'কিবোর্ড ও পোর্ট (Keyboard & Ports)',
    ports: 'পোর্ট ও কানেক্টিভিটি (Ports)',
    connectivity: 'কানেক্টিভিটি (Connectivity)',
    special_feature: 'বিশেষ ফিচার (Special Features)',
    water_resistance: 'ওয়াটার রেজিস্ট্যান্স (Waterproof)',
    compatibility: 'কম্প্যাটিবিলিটি (Compatibility)',
    fabric_material: 'কাপড় / ম্যাটেরিয়াল (Material)',
    gender: 'কাদের জন্য (Gender)',
    fit_type: 'ফিটিং / ধরন (Fit / Style)',
    care_instructions: 'যত্ন ও ধোয়ার নিয়ম (Wash Care)',
    author: 'লেখক / অনুবাদক (Author)',
    publisher: 'প্রকাশনী (Publisher)',
    edition: 'সংস্করণ (Edition)',
    pages: 'পৃষ্ঠা সংখ্যা (Pages)',
    cover_type: 'বাঁধাই (Binding)',
    language: 'ভাষা (Language)',
    isbn: 'ISBN / কোড',
    weight_volume: 'ওজন / পরিমাণ (Net Weight)',
    origin: 'উৎস দেশ (Origin)',
    ingredients: 'উপাদানসমূহ (Ingredients)',
    certification: 'মান ও সার্টিফিকেশন (Certification)',
    mfg_date: 'উৎপাদন তারিখ (Mfg Date)',
    expiry_date: 'মেয়াদ উত্তীর্ণের তারিখ (Expiry Date)',
    capacity: 'ক্যাপাসিটি (Capacity)',
    power_wattage: 'পাওয়ার / ওয়াটেজ (Power)',
    material: 'বডি ম্যাটেরিয়াল (Body Material)',
};

const formattedSpecs = computed(() => {
    const rows = [];
    if (props.product.brand) {
        rows.push({ label: 'ব্র্যান্ড (Brand)', value: props.product.brand });
    }
    if (props.product.warranty) {
        rows.push({ label: 'ওয়ারেন্টি (Warranty)', value: props.product.warranty });
    }

    Object.entries(specLabels).forEach(([key, label]) => {
        const val = specs.value[key];
        if (val !== undefined && val !== null && val !== '') {
            rows.push({ label, value: Array.isArray(val) ? val.join(', ') : val });
        }
    });

    if (Array.isArray(specs.value.colors) && specs.value.colors.length > 0) {
        rows.push({ label: 'কালার অপশন (Colors)', value: specs.value.colors.join(', ') });
    }
    if (Array.isArray(specs.value.sizes) && specs.value.sizes.length > 0) {
        rows.push({ label: 'সাইজ অপশন (Sizes)', value: specs.value.sizes.join(', ') });
    }

    if (specs.value.custom && typeof specs.value.custom === 'object') {
        Object.entries(specs.value.custom).forEach(([k, v]) => {
            if (k && v) rows.push({ label: k, value: v });
        });
    }

    return rows;
});

const quantity = ref(1);

const decreaseQty = () => {
    if (quantity.value > 1) quantity.value--;
};

const increaseQty = () => {
    if (quantity.value < effectiveStock.value) quantity.value++;
};

const isAdding = ref(false);
const isJustAdded = ref(false);

const addToCart = () => {
    isAdding.value = true;
    router.post(`/cart/add/${props.product.id}`, {
        quantity: quantity.value,
        variant_label: selectedOptionLabel.value,
        variant_price: effectivePrice.value,
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['cart', 'flash'],
        showProgress: false,
        onFinish: () => {
            isAdding.value = false;
            isJustAdded.value = true;
            setTimeout(() => {
                isJustAdded.value = false;
            }, 2000);
        },
    });
};

const buyNow = () => {
    router.post(`/cart/buy/${props.product.id}`, {
        quantity: quantity.value,
        variant_label: selectedOptionLabel.value,
        variant_price: effectivePrice.value,
    });
};
</script>

<template>
    <AppLayout>
        <Head :title="`${product.name} — Al-Aqsa Mart`" />

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 mb-6">
            <Link href="/" class="hover:text-[#0d3b37] transition font-medium">হোম</Link>
            <span>/</span>
            <Link v-if="product.category" :href="`/products?category=${product.category.slug}`" class="hover:text-[#0d3b37] transition font-medium">
                {{ product.category.name }}
            </Link>
            <span>/</span>
            <span class="text-gray-800 font-semibold truncate max-w-xs">{{ product.name }}</span>
        </nav>

        <!-- Main Product Card -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 lg:p-10 shadow-xs border border-gray-200/80 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 mb-12">
            <!-- Left: Product Image & Gallery Section (5 cols) -->
            <div class="lg:col-span-5 flex flex-col items-center">
                <div class="w-full aspect-square rounded-2xl overflow-hidden bg-gray-50 border border-gray-200/80 p-4 relative group flex items-center justify-center">
                    <img
                        :src="activeImage"
                        :alt="product.name"
                        class="w-full h-full object-contain group-hover:scale-105 transition duration-500"
                    />

                    <!-- Discount Badge -->
                    <span
                        v-if="product.sale_price && product.sale_price < product.price"
                        class="absolute top-4 left-4 bg-rose-600 text-white text-xs font-bold px-2.5 py-1 rounded shadow-xs"
                    >
                        {{ Math.round(((product.price - product.sale_price) / product.price) * 100) }}% ছাড়
                    </span>
                </div>

                <!-- Thumbnail Gallery -->
                <div v-if="allImages.length > 1" class="grid grid-cols-5 gap-2.5 w-full mt-3">
                    <button
                        v-for="(img, idx) in allImages"
                        :key="idx"
                        type="button"
                        @click="activeImage = img"
                        :class="[
                            'aspect-square rounded-xl border p-1.5 bg-gray-50 overflow-hidden transition',
                            activeImage === img ? 'border-[#0d3b37] ring-2 ring-[#0d3b37]/20 bg-white' : 'border-gray-200 hover:border-gray-300'
                        ]"
                    >
                        <img :src="img" class="w-full h-full object-contain" />
                    </button>
                </div>

                <!-- Safe Shopping Assurance Pills -->
                <div class="grid grid-cols-2 gap-3 w-full mt-4">
                    <div class="flex items-center gap-2.5 p-3 rounded-xl bg-gray-50 border border-gray-100 text-xs text-gray-700">
                        <span class="text-xl">🚚</span>
                        <div>
                            <p class="font-bold text-gray-900 leading-tight">দ্রুত ডেলিভারি</p>
                            <p class="text-[11px] text-gray-500">সারাদেশে ২-৩ দিনে</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 p-3 rounded-xl bg-gray-50 border border-gray-100 text-xs text-gray-700">
                        <span class="text-xl">🛡️</span>
                        <div>
                            <p class="font-bold text-gray-900 leading-tight">{{ product.warranty || 'ক্যাশ অন ডেলিভারি' }}</p>
                            <p class="text-[11px] text-gray-500">হাতে পেয়ে মূল্য দিন</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Product Details & Purchase Controls (7 cols) -->
            <div class="lg:col-span-7 flex flex-col">
                <!-- Category, Brand & SKU Tag -->
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#b58d3d] bg-[#b58d3d]/10 px-2.5 py-0.5 rounded">
                            {{ product.category?.name || 'General' }}
                        </span>
                        <span v-if="product.brand" class="text-xs font-bold text-[#0d3b37] bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded">
                            ব্র্যান্ড: {{ product.brand }}
                        </span>
                    </div>
                    <span v-if="activeVariant?.sku || product.sku" class="text-xs text-gray-400 font-mono">
                        SKU: {{ activeVariant?.sku || product.sku }}
                    </span>
                </div>

                <!-- Product Title -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-3 leading-snug">
                    {{ product.name }}
                </h1>

                <!-- Rating & Warranty Badge -->
                <div class="flex flex-wrap items-center gap-2 mb-4 text-xs">
                    <div class="flex text-amber-400 text-sm">★★★★★</div>
                    <span class="text-emerald-700 font-semibold">৫.০</span>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-500">ভেরিফায়েড প্রডাক্ট</span>
                    <template v-if="product.warranty">
                        <span class="text-gray-400">|</span>
                        <span class="text-emerald-700 font-semibold">🛡️ {{ product.warranty }}</span>
                    </template>
                </div>

                <!-- Price & Stock Status -->
                <div class="bg-gray-50/80 rounded-2xl p-4 sm:p-5 border border-gray-200/60 mb-5 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl sm:text-4xl font-black text-[#0d3b37]">
                            ৳{{ effectivePrice }}
                        </span>
                        <span v-if="!activeVariant && product.sale_price && product.sale_price < product.price" class="text-base sm:text-lg text-gray-400 line-through">
                            ৳{{ product.price }}
                        </span>
                        <span v-if="!activeVariant && product.sale_price && product.sale_price < product.price" class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-100">
                            ৳{{ product.price - product.sale_price }} সাশ্রয়
                        </span>
                    </div>

                    <div>
                        <span v-if="effectiveStock > 0" class="inline-flex items-center gap-1.5 text-xs bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            স্টকে আছে ({{ effectiveStock }} {{ product.unit || 'টি' }})
                        </span>
                        <span v-else class="inline-flex items-center gap-1.5 text-xs bg-rose-100 text-rose-700 px-3 py-1 rounded-full font-semibold">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            স্টক আউট
                        </span>
                    </div>
                </div>

                <!-- Product Variants Selector (If Variants exist) -->
                <div v-if="variants.length > 0" class="mb-5 space-y-2.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">
                        ভ্যারিয়েন্ট নির্বাচন করুন (Select Option):
                    </label>
                    <div class="flex flex-wrap gap-2.5">
                        <button
                            v-for="(v, idx) in variants"
                            :key="idx"
                            type="button"
                            @click="selectedVariantIndex = idx"
                            :class="[
                                'px-3.5 py-2 rounded-xl text-xs font-bold border transition flex items-center gap-2',
                                selectedVariantIndex === idx
                                    ? 'bg-[#0d3b37] text-white border-[#0d3b37] shadow-xs'
                                    : 'bg-white text-gray-700 border-gray-300 hover:border-[#0d3b37]'
                            ]"
                        >
                            <span>{{ v.name }}</span>
                            <span v-if="v.price" :class="selectedVariantIndex === idx ? 'text-emerald-200' : 'text-gray-500'">
                                (৳{{ v.price }})
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Color Selector (If no full variants, but colors exist in specs) -->
                <div v-if="variants.length === 0 && Array.isArray(specs.colors) && specs.colors.length > 0" class="mb-4 space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">
                        কালার নির্বাচন করুন (Color): <span class="text-[#0d3b37]">{{ selectedColor }}</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="color in specs.colors"
                            :key="color"
                            type="button"
                            @click="selectedColor = color"
                            :class="[
                                'px-3 py-1.5 rounded-lg text-xs font-bold border transition',
                                selectedColor === color
                                    ? 'bg-[#0d3b37] text-white border-[#0d3b37]'
                                    : 'bg-white text-gray-700 border-gray-300 hover:border-gray-400'
                            ]"
                        >
                            {{ color }}
                        </button>
                    </div>
                </div>

                <!-- Size Selector (If no full variants, but sizes exist in specs) -->
                <div v-if="variants.length === 0 && Array.isArray(specs.sizes) && specs.sizes.length > 0" class="mb-5 space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">
                        সাইজ নির্বাচন করুন (Size): <span class="text-[#0d3b37]">{{ selectedSize }}</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="sz in specs.sizes"
                            :key="sz"
                            type="button"
                            @click="selectedSize = sz"
                            :class="[
                                'px-3 py-1.5 rounded-lg text-xs font-bold border transition',
                                selectedSize === sz
                                    ? 'bg-[#0d3b37] text-white border-[#0d3b37]'
                                    : 'bg-white text-gray-700 border-gray-300 hover:border-gray-400'
                            ]"
                        >
                            {{ sz }}
                        </button>
                    </div>
                </div>

                <!-- Product Summary / Short Description -->
                <div class="text-gray-700 text-sm sm:text-base leading-relaxed mb-6 whitespace-pre-line border-b border-gray-100 pb-6">
                    {{ product.description || 'এই পণ্যের জন্য বিস্তারিত বিবরণ শীঘ্রই যুক্ত করা হবে।' }}
                </div>

                <!-- Actions: Qty & Two CTA Buttons (Add to Cart + Buy Now) -->
                <div v-if="effectiveStock > 0" class="space-y-4 mt-auto">
                    <!-- Quantity Selector & Total Preview -->
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">পরিমাণ:</span>
                        <div class="flex items-center border border-gray-300 rounded-xl bg-white shadow-xs overflow-hidden">
                            <button
                                @click="decreaseQty"
                                class="w-9 h-9 flex items-center justify-center font-bold text-gray-600 hover:bg-gray-100 active:scale-90 transition"
                            >
                                -
                            </button>
                            <span class="w-12 text-center font-black text-gray-900 text-sm">{{ quantity }}</span>
                            <button
                                @click="increaseQty"
                                class="w-9 h-9 flex items-center justify-center font-bold text-gray-600 hover:bg-gray-100 active:scale-90 transition"
                            >
                                +
                            </button>
                        </div>
                        <span class="text-xs text-gray-500 font-medium">
                            মোট: <strong class="text-[#0d3b37] text-sm">৳{{ effectivePrice * quantity }}</strong>
                        </span>
                    </div>

                    <!-- Direct Order & Add to Cart Action Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <!-- Buy Now Button -->
                        <button
                            @click="buyNow"
                            class="w-full bg-[#b58d3d] hover:bg-[#99742e] text-white font-bold py-3.5 px-6 rounded-xl shadow-md active:scale-98 transition flex items-center justify-center gap-2 text-sm sm:text-base"
                        >
                            <span>⚡</span> এখনই অর্ডার করুন
                        </button>

                        <!-- Add to Cart Button -->
                        <button
                            @click="addToCart"
                            :disabled="isAdding"
                            :class="[
                                'w-full font-bold py-3.5 px-6 rounded-xl shadow-md active:scale-98 transition flex items-center justify-center gap-2 text-sm sm:text-base border',
                                isJustAdded
                                    ? 'bg-emerald-600 text-white border-emerald-600'
                                    : 'bg-[#0d3b37] hover:bg-[#064e3b] text-white border-[#0d3b37]'
                            ]"
                        >
                            <svg v-if="isAdding" class="w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <svg v-else-if="isJustAdded" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span v-else>🛒</span>
                            <span>{{ isAdding ? 'কার্টে যোগ হচ্ছে...' : (isJustAdded ? 'কার্টে যোগ হয়েছে!' : 'কার্টে যোগ করুন') }}</span>
                        </button>
                    </div>

                    <!-- Phone order assistance banner -->
                    <div class="flex items-center justify-center gap-2 text-xs text-gray-600 bg-amber-50/60 border border-amber-200/50 py-2.5 px-4 rounded-xl">
                        <span class="text-base text-amber-600">📞</span>
                        <span>ফোনে সরাসরি অর্ডার করতে কল করুন: <strong class="text-[#0d3b37] font-bold">16297</strong></span>
                    </div>
                </div>
                <div v-else class="text-rose-600 font-semibold p-4 bg-rose-50 rounded-2xl text-center border border-rose-200">
                    দুঃখিত, পণ্যটি বর্তমানে স্টকে উপলভ্য নয়।
                </div>
            </div>
        </div>

        <!-- Dynamic Technical Specifications Table (Shown when specs exist) -->
        <section v-if="formattedSpecs.length > 0" class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200/80 shadow-xs mb-12">
            <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-5">
                পণ্যের স্পেসিফিকেশন ও বৈশিষ্ট্য (Specifications)
            </h2>
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="w-full text-sm text-left border-collapse">
                    <tbody>
                        <tr
                            v-for="(row, index) in formattedSpecs"
                            :key="index"
                            :class="index % 2 === 0 ? 'bg-gray-50/70' : 'bg-white'"
                            class="border-b border-gray-100 last:border-b-0"
                        >
                            <th class="py-3 px-4 font-bold text-gray-700 w-1/3 sm:w-1/4 border-r border-gray-100">
                                {{ row.label }}
                            </th>
                            <td class="py-3 px-4 text-gray-800 font-medium">
                                {{ row.value }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Specifications & Trust Guarantee Tab Section -->
        <section class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200/80 shadow-xs mb-12">
            <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-5">
                পণ্য বিবরণ ও ডেলিভারি পলিসি
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                <div class="space-y-2 p-4 bg-gray-50/70 rounded-xl border border-gray-100">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">১০০% আসল পণ্য</p>
                    <p class="text-gray-700 leading-relaxed text-xs sm:text-sm">
                        আল-আকসা মার্টের প্রতিটি পণ্য সরাসরি প্রস্তুতকারক ও বিশ্বস্ত সরবরাহকারী থেকে সংগৃহীত।
                    </p>
                </div>

                <div class="space-y-2 p-4 bg-gray-50/70 rounded-xl border border-gray-100">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">সহজ রিটার্ন পলিসি</p>
                    <p class="text-gray-700 leading-relaxed text-xs sm:text-sm">
                        পণ্য হাতে পেয়ে কোনো সমস্যা থাকলে ৭ দিনের মধ্যে সহজে রিটার্ন বা পরিবর্তন করার সুবিধা।
                    </p>
                </div>

                <div class="space-y-2 p-4 bg-gray-50/70 rounded-xl border border-gray-100">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">ডেলিভারি চার্জ</p>
                    <p class="text-gray-700 leading-relaxed text-xs sm:text-sm">
                        ৳১০০০ বা তদূর্ধ্ব কেনাকাটায় সারাদেশে ডেলিভারি চার্জ সম্পূর্ণ ফ্রি!
                    </p>
                </div>
            </div>
        </section>

        <!-- Related Products Section -->
        <section v-if="related && related.length > 0" class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-gray-900">সম্পর্কিত অন্যান্য পণ্য</h2>
                <Link
                    v-if="product.category"
                    :href="`/products?category=${product.category.slug}`"
                    class="text-xs font-semibold text-[#0d3b37] hover:underline"
                >
                    আরও দেখুন →
                </Link>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div
                    v-for="item in related"
                    :key="item.id"
                    class="bg-white rounded-xl p-3 border border-gray-200 hover:border-emerald-300 hover:shadow-lg transition-all duration-300 flex flex-col group"
                >
                    <Link :href="`/products/${item.slug || item.id}`" class="block aspect-square rounded-lg overflow-hidden bg-gray-50 mb-2.5 p-2">
                        <img
                            :src="item.image ? (item.image.startsWith('http') ? item.image : `/storage/${item.image}`) : 'https://placehold.co/400x400?text=Al-Aqsa+Mart'"
                            :alt="item.name"
                            class="w-full h-full object-contain group-hover:scale-105 transition"
                        />
                    </Link>
                    <Link :href="`/products/${item.slug || item.id}`" class="font-medium text-gray-800 group-hover:text-[#0d3b37] transition line-clamp-2 text-xs leading-snug mb-2 flex-1">
                        {{ item.name }}
                    </Link>
                    <div class="mt-auto pt-2 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-sm font-bold text-[#0d3b37]">
                            ৳{{ item.sale_price || item.price }}
                        </span>
                        <Link
                            :href="`/products/${item.slug || item.id}`"
                            class="text-[11px] font-semibold text-[#b58d3d] hover:underline"
                        >
                            বিস্তারিত
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
