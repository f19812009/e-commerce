<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    query: String,
    orders: {
        type: Array,
        default: () => [],
    },
});

const searchInput = ref(props.query || '');

const submitTrack = () => {
    router.get('/track-order', { q: searchInput.value }, { preserveState: true });
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'pending': return { label: 'পেন্ডিং (Pending)', cls: 'bg-amber-100 text-amber-800 border-amber-200' };
        case 'processing': return { label: 'প্রসেসিং চলছে (Processing)', cls: 'bg-blue-100 text-blue-800 border-blue-200' };
        case 'shipped': return { label: 'ডেলিভারির পথে (Shipped)', cls: 'bg-indigo-100 text-indigo-800 border-indigo-200' };
        case 'delivered': return { label: 'ডেলিভারি সম্পন্ন (Delivered)', cls: 'bg-emerald-100 text-emerald-800 border-emerald-200' };
        case 'cancelled': return { label: 'বাতিল (Cancelled)', cls: 'bg-rose-100 text-rose-800 border-rose-200' };
        default: return { label: status, cls: 'bg-gray-100 text-gray-800 border-gray-200' };
    }
};

const steps = [
    { key: 'pending', label: 'অর্ডার গৃহীত' },
    { key: 'processing', label: 'প্রসেসিং' },
    { key: 'shipped', label: 'ডেলিভারির পথে' },
    { key: 'delivered', label: 'ডেলিভারি সম্পন্ন' },
];

const getStepIndex = (status) => {
    const idx = steps.findIndex(s => s.key === status);
    return idx === -1 ? 0 : idx;
};
</script>

<template>
    <AppLayout>
        <Head title="অর্ডার ট্র্যাক করুন — Al-Aqsa Mart" />

        <div class="max-w-3xl mx-auto my-6">
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 shadow-xs mb-8">
                <div class="text-center mb-6">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-50 text-[#0d3b37] text-2xl mb-3">🚚</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900">অর্ডার ট্র্যাকিং (Track Your Order)</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">
                        আপনার অর্ডার নম্বর (যেমন: <strong>ORD-...</strong>) অথবা অর্ডারে ব্যবহৃত <strong>মোবাইল নম্বর</strong> দিয়ে সার্চ করুন
                    </p>
                </div>

                <form @submit.prevent="submitTrack" class="flex flex-col sm:flex-row gap-3">
                    <input
                        v-model="searchInput"
                        type="text"
                        required
                        placeholder="অর্ডার নম্বর (ORD-...) অথবা মোবাইল নম্বর (017...) লিখুন"
                        class="flex-1 bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b37] focus:bg-white"
                    />
                    <button
                        type="submit"
                        class="bg-[#0d3b37] hover:bg-[#064e3b] text-white font-bold px-6 py-3 rounded-xl text-sm shadow-sm transition cursor-pointer"
                    >
                        অর্ডার খুঁজুন →
                    </button>
                </form>
            </div>

            <!-- Results -->
            <div v-if="query && orders.length > 0" class="space-y-6">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="bg-white rounded-2xl p-6 sm:p-7 border border-gray-200 shadow-xs space-y-5"
                >
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
                        <div>
                            <span class="text-xs text-gray-400 font-semibold uppercase">অর্ডার নম্বর</span>
                            <h3 class="text-lg font-black text-[#0d3b37]">#{{ order.order_number }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                গ্রাহক: <strong>{{ order.customer_name }}</strong> | শহর: {{ order.city }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span :class="['inline-block px-3 py-1 rounded-full text-xs font-bold border', getStatusBadge(order.status).cls]">
                                {{ getStatusBadge(order.status).label }}
                            </span>
                            <p class="text-sm font-black text-gray-900 mt-1.5">সর্বমোট: ৳{{ Number(order.total).toLocaleString() }}</p>
                        </div>
                    </div>

                    <!-- Progress Stepper (if not cancelled) -->
                    <div v-if="order.status !== 'cancelled'" class="grid grid-cols-4 gap-2 pt-1">
                        <div
                            v-for="(step, idx) in steps"
                            :key="step.key"
                            class="flex flex-col items-center text-center"
                        >
                            <div
                                :class="[
                                    'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold mb-1.5 transition shadow-2xs',
                                    idx <= getStepIndex(order.status)
                                        ? 'bg-[#0d3b37] text-white'
                                        : 'bg-gray-100 text-gray-400'
                                ]"
                            >
                                {{ idx < getStepIndex(order.status) ? '✓' : idx + 1 }}
                            </div>
                            <span
                                :class="[
                                    'text-[11px] font-semibold',
                                    idx <= getStepIndex(order.status) ? 'text-[#0d3b37] font-bold' : 'text-gray-400'
                                ]"
                            >
                                {{ step.label }}
                            </span>
                        </div>
                    </div>

                    <!-- Courier & Admin Tracking Info Box (if updated from Admin Panel) -->
                    <div
                        v-if="order.courier_name || order.tracking_code || order.tracking_note"
                        class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-4 space-y-2"
                    >
                        <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm">
                            <div v-if="order.courier_name" class="flex items-center gap-1.5">
                                <span class="text-gray-500">🚚 কুরিয়ার সার্ভিস:</span>
                                <strong class="text-[#0d3b37] font-black">{{ order.courier_name }}</strong>
                            </div>
                            <div v-if="order.tracking_code" class="flex items-center gap-1.5">
                                <span class="text-gray-500">🔖 ট্র্যাকিং কোড:</span>
                                <span class="bg-white border border-emerald-300 text-[#0d3b37] px-2.5 py-0.5 rounded-md font-mono font-bold text-xs">
                                    {{ order.tracking_code }}
                                </span>
                            </div>
                        </div>
                        <p v-if="order.tracking_note" class="text-xs sm:text-sm text-gray-700 bg-white/80 p-2.5 rounded-lg border border-emerald-100">
                            💬 <strong>ডেলিভারি আপডেট:</strong> {{ order.tracking_note }}
                        </p>
                    </div>

                    <!-- Ordered Items -->
                    <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                        <p class="text-xs font-bold text-gray-600 uppercase">অর্ডারকৃত পণ্যসমূহ:</p>
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex items-center justify-between text-xs sm:text-sm py-1 border-b border-gray-200/60 last:border-none"
                        >
                            <span class="font-medium text-gray-800">{{ item.product_name }} × {{ item.quantity }}</span>
                            <span class="font-bold text-gray-900">৳{{ Number(item.subtotal).toLocaleString() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else-if="query && orders.length === 0" class="bg-white rounded-2xl p-10 text-center border border-gray-200">
                <span class="text-4xl block mb-2">🔍</span>
                <h3 class="text-base font-bold text-gray-800">কোনো অর্ডার খুঁজে পাওয়া যায়নি!</h3>
                <p class="text-xs text-gray-500 mt-1">সঠিক অর্ডার নম্বর অথবা অর্ডারে প্রদত্ত মোবাইল নম্বরটি দিয়ে আবার চেষ্টা করুন।</p>
            </div>
        </div>
    </AppLayout>
</template>
