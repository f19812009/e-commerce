<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    order: Object,
});

const getStatusBadge = (status) => {
    switch (status) {
        case 'pending': return 'bg-amber-100 text-amber-700';
        case 'processing': return 'bg-blue-100 text-blue-700';
        case 'completed': return 'bg-emerald-100 text-emerald-700';
        case 'cancelled': return 'bg-rose-100 text-rose-700';
        default: return 'bg-gray-100 text-gray-700';
    }
};
</script>

<template>
    <AppLayout>
        <Head :title="`অর্ডার #${order.order_number || order.id} — Al-Aqsa Mart`" />

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <Link href="/orders" class="text-sm font-semibold text-[#0d3b37] hover:underline">
                ← অর্ডার তালিকায় ফিরুন
            </Link>
            <div class="flex items-center gap-3">
                <a
                    :href="`/orders/${order.id}/invoice`"
                    target="_blank"
                    class="inline-flex items-center gap-2 bg-[#0d3b37] hover:bg-[#064e3b] text-white text-xs sm:text-sm font-bold px-4 py-2 rounded-xl shadow transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>ইনভয়েস ডাউনলোড (PDF)</span>
                </a>
                <span :class="['px-3 py-1 rounded-full text-xs font-bold uppercase', getStatusBadge(order.status)]">
                    {{ order.status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Items Table -->
            <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-emerald-100/60">
                <h2 class="text-lg font-bold text-gray-900 mb-6">অর্ডারের পণ্যসমূহ (অর্ডার #{{ order.id }})</h2>

                <div class="divide-y divide-gray-100">
                    <div
                        v-for="item in order.items"
                        :key="item.id"
                        class="py-4 flex items-center justify-between gap-4"
                    >
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">{{ item.product_name }}</h4>
                            <p class="text-xs text-gray-500">মূল্য: ৳{{ item.price }} × {{ item.quantity }}</p>
                        </div>
                        <div class="font-bold text-gray-900 text-base">
                            ৳{{ item.subtotal }}
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 mt-6 pt-4 space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>সাব-টোটাল</span>
                        <span class="font-bold text-gray-900">৳{{ order.subtotal }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>ডেলিভারি চার্জ</span>
                        <span class="font-bold text-gray-900">৳{{ order.shipping }}</span>
                    </div>
                    <div class="flex justify-between text-base font-black text-gray-900 pt-2 border-t">
                        <span>সর্বমোট</span>
                        <span class="text-xl text-[#0d3b37]">৳{{ order.total }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer & Shipping details -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 h-fit space-y-4">
                <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">গ্রাহক ও ডেলিভারি বিবরণ</h3>

                <div class="text-sm space-y-3 text-gray-700">
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase">গ্রাহকের নাম</div>
                        <div class="font-medium text-gray-900">{{ order.customer_name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase">ফোন</div>
                        <div class="font-medium text-gray-900">{{ order.customer_phone }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase">ইমেইল</div>
                        <div class="font-medium text-gray-900">{{ order.customer_email }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase">ডেলিভারি ঠিকানা</div>
                        <div class="font-medium text-gray-900">{{ order.shipping_address }}, {{ order.city }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase">পেমেন্ট মেথড</div>
                        <div class="font-bold uppercase text-gray-900">{{ order.payment_method }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
