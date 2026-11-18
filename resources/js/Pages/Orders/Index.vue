<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    orders: Object,
});

const getStatusBadge = (status) => {
    switch (status) {
        case 'pending': return 'bg-amber-100 text-amber-700';
        case 'processing': return 'bg-blue-100 text-blue-700';
        case 'shipped': return 'bg-indigo-100 text-indigo-700';
        case 'delivered':
        case 'completed': return 'bg-emerald-100 text-emerald-700';
        case 'cancelled': return 'bg-rose-100 text-rose-700';
        default: return 'bg-gray-100 text-gray-700';
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'pending': return 'অপেক্ষমান (Pending)';
        case 'processing': return 'প্রক্রিয়াধীন (Processing)';
        case 'shipped': return 'পথে আছে (Shipped)';
        case 'delivered': return 'ডেলিভারিকৃত (Delivered)';
        case 'cancelled': return 'বাতিল (Cancelled)';
        default: return status;
    }
};
</script>

<template>
    <AppLayout>
        <Head title="আমার অর্ডার — Al-Aqsa Mart" />

        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <h1 class="text-2xl md:text-3xl font-black text-gray-900">আমার অর্ডার সমূহ 📑</h1>
            <Link
                href="/track-order"
                class="inline-flex items-center gap-2 bg-emerald-50 hover:bg-[#0d3b37] text-[#0d3b37] hover:text-white font-bold px-4 py-2 rounded-xl text-xs sm:text-sm border border-emerald-200 transition"
            >
                <span>🚚</span>
                <span>অর্ডার ট্র্যাকিং পেজ</span>
            </Link>
        </div>

        <div v-if="orders.data.length > 0" class="space-y-4">
            <div
                v-for="order in orders.data"
                :key="order.id"
                class="bg-white rounded-2xl p-6 shadow-xs border border-gray-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
            >
                <div>
                    <div class="flex items-center gap-3 mb-1 flex-wrap">
                        <span class="font-bold text-gray-900 text-base">অর্ডার #{{ order.order_number || order.id }}</span>
                        <span :class="['px-2.5 py-0.5 rounded-full text-xs font-semibold', getStatusBadge(order.status)]">
                            {{ getStatusLabel(order.status) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500">
                        তারিখ: {{ new Date(order.created_at).toLocaleDateString('bn-BD') }} | মেথড: {{ order.payment_method }}
                    </p>
                </div>

                <div class="flex items-center justify-between sm:justify-end gap-6">
                    <div class="text-right">
                        <div class="text-xs text-gray-500">মোট মূল্য</div>
                        <div class="text-lg font-black text-[#0d3b37]">৳{{ Number(order.total).toLocaleString() }}</div>
                    </div>

                    <Link
                        :href="`/orders/${order.id}`"
                        class="bg-emerald-50 hover:bg-[#0d3b37] hover:text-white text-[#0d3b37] font-semibold px-4 py-2 rounded-xl text-sm transition"
                    >
                        বিস্তারিত দেখুন →
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="orders.links && orders.links.length > 3" class="mt-8 flex justify-center gap-1.5 flex-wrap">
                <Link
                    v-for="(link, i) in orders.links"
                    :key="i"
                    :href="link.url || '#'"
                    v-html="link.label"
                    :class="[
                        'px-4 py-2 rounded-xl text-sm font-semibold transition',
                        link.active ? 'bg-[#0d3b37] text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-emerald-50 border border-gray-200',
                        !link.url ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''
                    ]"
                />
            </div>
        </div>

        <!-- No Orders -->
        <div v-else class="bg-white rounded-2xl p-12 text-center border border-gray-200/80 max-w-md mx-auto shadow-xs">
            <span class="text-5xl block mb-4">📦</span>
            <h3 class="text-lg font-bold text-gray-800 mb-2">এখনো কোনো অর্ডার করেননি</h3>
            <p class="text-gray-500 text-xs sm:text-sm mb-6">আপনার পছন্দের পণ্য খুঁজে সহজে অর্ডার সম্পন্ন করুন।</p>
            <Link href="/" class="bg-[#0d3b37] hover:bg-[#064e3b] text-white font-bold px-6 py-2.5 rounded-xl text-sm shadow-xs transition">
                শপিং শুরু করুন
            </Link>
        </div>
    </AppLayout>
</template>
