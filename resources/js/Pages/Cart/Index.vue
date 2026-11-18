<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    items: Array,
    subtotal: Number,
    shipping: Number,
    total: Number,
});

const updateQty = (id, newQty) => {
    if (newQty < 1) return;
    router.patch(`/cart/update/${id}`, { quantity: newQty }, {
        preserveScroll: true,
    });
};

const removeItem = (id) => {
    router.delete(`/cart/remove/${id}`, {
        preserveScroll: true,
    });
};

const clearCart = () => {
    if (confirm('আপনি কি পুরো কার্ট খালি করতে চান?')) {
        router.delete('/cart/clear');
    }
};
</script>

<template>
    <AppLayout>
        <Head title="শপিং কার্ট — Al-Aqsa Mart" />

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 mb-6">
            <Link href="/" class="hover:text-[#0d3b37] transition font-medium">হোম</Link>
            <span>/</span>
            <span class="text-gray-800 font-semibold">শপিং কার্ট</span>
        </nav>

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900">
                শপিং কার্ট 🛒 ({{ items.length }} টি পণ্য)
            </h1>
            <Link
                v-if="items.length > 0"
                href="/"
                class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold text-[#0d3b37] hover:underline"
            >
                <span>←</span> আরও পণ্য যুক্ত করুন
            </Link>
        </div>

        <!-- Cart Has Items -->
        <div v-if="items.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-16">
            <!-- Items Table/List (7 cols) -->
            <div class="lg:col-span-7 space-y-4">
                <div
                    v-for="item in items"
                    :key="item.id"
                    class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-gray-200/80 flex items-center gap-4 sm:gap-5"
                >
                    <!-- Product Image -->
                    <div class="w-18 h-18 sm:w-22 sm:h-22 rounded-xl overflow-hidden bg-gray-50 flex-shrink-0 border border-gray-100 p-1 flex items-center justify-center">
                        <img
                            :src="item.image || 'https://placehold.co/150x150?text=Item'"
                            :alt="item.name"
                            class="w-full h-full object-contain"
                        />
                    </div>

                    <!-- Details -->
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 truncate text-sm sm:text-base mb-1" :title="item.name">
                            {{ item.name }}
                        </h3>
                        <p class="text-xs sm:text-sm font-bold text-[#0d3b37] mb-3">
                            একক মূল্য: ৳{{ item.price }}
                        </p>

                        <!-- Quantity Selector -->
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-gray-300 rounded-lg bg-white shadow-xs overflow-hidden">
                                <button
                                    @click="updateQty(item.id, item.quantity - 1)"
                                    class="w-7 h-7 flex items-center justify-center font-bold text-gray-600 hover:bg-gray-100 active:scale-90 transition text-sm"
                                >
                                    -
                                </button>
                                <span class="w-9 text-center font-black text-xs sm:text-sm text-gray-900">{{ item.quantity }}</span>
                                <button
                                    @click="updateQty(item.id, item.quantity + 1)"
                                    class="w-7 h-7 flex items-center justify-center font-bold text-gray-600 hover:bg-gray-100 active:scale-90 transition text-sm"
                                >
                                    +
                                </button>
                            </div>

                            <button
                                @click="removeItem(item.id)"
                                class="text-xs text-rose-500 hover:text-rose-700 font-semibold hover:underline ml-2"
                            >
                                মুছে ফেলুন
                            </button>
                        </div>
                    </div>

                    <!-- Subtotal for this item -->
                    <div class="text-right font-black text-gray-900 text-base sm:text-lg flex-shrink-0">
                        ৳{{ item.subtotal }}
                    </div>
                </div>

                <div class="flex justify-between items-center pt-2 px-1">
                    <Link href="/" class="text-xs sm:text-sm font-semibold text-[#0d3b37] hover:underline flex items-center gap-1">
                        ← কেনাকাটা চালিয়ে যান
                    </Link>
                    <button @click="clearCart" class="text-xs sm:text-sm font-medium text-rose-500 hover:underline">
                        কার্ট সম্পূর্ণ খালি করুন
                    </button>
                </div>
            </div>

            <!-- Summary Card (5 cols) -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-gray-200/80 sticky top-24 space-y-4">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">
                    অর্ডার সারাংশ
                </h3>

                <div class="flex justify-between text-sm text-gray-600">
                    <span>পণ্যের মোট মূল্য (সাব-টোটাল)</span>
                    <span class="font-bold text-gray-800">৳{{ subtotal }}</span>
                </div>

                <div class="flex justify-between text-sm text-gray-600 items-center">
                    <span>ডেলিভারি চার্জ</span>
                    <span v-if="shipping === 0" class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs font-bold">
                        ফ্রি ডেলিভারি!
                    </span>
                    <span v-else class="font-bold text-gray-800">৳{{ shipping }}</span>
                </div>

                <div v-if="subtotal < 1000" class="text-xs text-[#b58d3d] bg-[#b58d3d]/10 p-2.5 rounded-xl font-medium leading-relaxed">
                    💡 আর মাত্র <strong>৳{{ 1000 - subtotal }}</strong> টাকার পণ্য কিনলে পেয়ে যাচ্ছেন সম্পূর্ণ ফ্রি ডেলিভারি!
                </div>

                <div class="border-t-2 border-gray-100 pt-3 flex justify-between items-baseline">
                    <span class="text-base font-black text-gray-900">সর্বমোট:</span>
                    <span class="text-2xl font-black text-[#0d3b37]">৳{{ total }}</span>
                </div>

                <Link
                    href="/checkout"
                    class="w-full block text-center bg-[#0d3b37] hover:bg-[#064e3b] text-white font-bold py-3.5 px-6 rounded-xl shadow-md active:scale-98 transition text-sm sm:text-base mt-4"
                >
                    চেকআউট সম্পন্ন করুন →
                </Link>

                <div class="pt-2 text-center text-xs text-gray-400 flex items-center justify-center gap-1.5">
                    <span>🔒</span> নিরাপদ ও দ্রুততম অর্ডার প্রক্রিয়া
                </div>
            </div>
        </div>

        <!-- Empty Cart -->
        <div v-else class="bg-white rounded-2xl p-12 text-center border border-gray-200/80 max-w-lg mx-auto shadow-xs my-8">
            <span class="text-6xl block mb-4">🛒</span>
            <h2 class="text-xl font-bold text-gray-900 mb-2">আপনার কার্ট খালি!</h2>
            <p class="text-gray-500 text-xs sm:text-sm mb-6 leading-relaxed">
                আপনার শপিং কার্টে বর্তমানে কোনো পণ্য যোগ করা নেই। আমাদের বিশাল কালেকশন থেকে পছন্দমতো পণ্য বেছে নিন।
            </p>
            <Link
                href="/"
                class="inline-block bg-[#0d3b37] hover:bg-[#064e3b] text-white font-bold px-6 py-3 rounded-xl shadow-md transition text-sm"
            >
                শপিং শুরু করুন
            </Link>
        </div>
    </AppLayout>
</template>
