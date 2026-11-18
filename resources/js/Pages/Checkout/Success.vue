<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    order: Object,
});
</script>

<template>
    <AppLayout>
        <Head title="অর্ডার সফল — Al-Aqsa Mart" />

        <div class="max-w-2xl mx-auto my-8 sm:my-12">
            <!-- Order Success Confirmation Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-emerald-100/80 text-center relative overflow-hidden">
                <!-- Top Decorative Header Strip -->
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#0d3b37] via-[#047857] to-[#b58d3d]"></div>

                <!-- Green Tick Icon -->
                <div class="w-20 h-20 bg-emerald-50 border-2 border-emerald-200 text-emerald-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-5 shadow-sm">
                    ✓
                </div>

                <span class="inline-block bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                    অর্ডার সফল হয়েছে
                </span>

                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mb-2">
                    আলহামদুলিল্লাহ! আপনার অর্ডারটি গৃহীত হয়েছে
                </h1>
                <p class="text-gray-600 text-sm max-w-lg mx-auto mb-8 leading-relaxed">
                    ধন্যবাদ আমাদের সাথে কেনাকাটার জন্য। আমাদের প্রতিনিধি দ্রুততম সময়ের মধ্যে আপনার সাথে ফোনে যোগাযোগ করে অর্ডার কনফার্ম করবেন এবং ডেলিভারি প্রস্তুত করবেন।
                </p>

                <!-- Order Details Summary Box -->
                <div class="bg-[#fcfdfd] border border-gray-200/80 rounded-2xl p-5 sm:p-6 mb-8 text-left space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">অর্ডার ট্র্যাকিং নম্বর</span>
                        <span class="font-mono font-black text-[#0d3b37] text-base bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-100">
                            #{{ order.order_number || order.id }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">গ্রাহকের নাম:</span>
                        <span class="font-semibold text-gray-800">{{ order.customer_name }}</span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">মোবাইল নম্বর:</span>
                        <span class="font-semibold text-gray-800">{{ order.customer_phone }}</span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">পেমেন্ট পদ্ধতি:</span>
                        <span class="font-bold text-gray-800 uppercase bg-gray-100 px-2 py-0.5 rounded text-xs">
                            {{ order.payment_method === 'cod' ? 'ক্যাশ অন ডেলিভারি (COD)' : order.payment_method }}
                        </span>
                    </div>

                    <div class="flex items-start justify-between text-sm pt-1">
                        <span class="text-gray-500 flex-shrink-0">ডেলিভারি ঠিকানা:</span>
                        <span class="font-medium text-gray-800 text-right max-w-xs sm:max-w-sm">
                            {{ order.shipping_address }}, {{ order.city }}
                        </span>
                    </div>

                    <!-- Items Ordered Preview -->
                    <div v-if="order.items && order.items.length > 0" class="pt-3 border-t border-gray-100 space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-2">অর্ডারকৃত পণ্যসমূহ:</span>
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex justify-between items-center text-xs sm:text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded-lg"
                        >
                            <span class="font-medium truncate max-w-[220px] sm:max-w-xs">{{ item.product_name }} × {{ item.quantity }}</span>
                            <span class="font-bold text-gray-900">৳{{ item.subtotal }}</span>
                        </div>
                    </div>

                    <!-- Total Amount Banner -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <span class="font-bold text-gray-800 text-base">সর্বমোট প্রদেয় মূল্য:</span>
                        <span class="text-2xl font-black text-[#0d3b37]">৳{{ order.total }}</span>
                    </div>
                </div>

                <!-- Action Buttons: Invoice Download, Back to Home & Orders -->
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <a
                        :href="`/orders/${order.id}/invoice`"
                        target="_blank"
                        class="w-full sm:flex-1 bg-white border-2 border-[#0d3b37] text-[#0d3b37] hover:bg-[#0d3b37] hover:text-white font-bold py-3 px-5 rounded-2xl shadow-sm transition flex items-center justify-center gap-2 text-sm"
                    >
                        <span>📄</span> ইনভয়েস ডাউনলোড করুন
                    </a>

                    <Link
                        href="/"
                        class="w-full sm:flex-1 bg-gradient-to-r from-[#0d3b37] via-[#047857] to-[#b58d3d] hover:opacity-95 text-white font-bold py-3 px-5 rounded-2xl shadow-md transition flex items-center justify-center gap-2 text-sm"
                    >
                        <span>🛍️</span> আরও পণ্য দেখুন
                    </Link>
                </div>

                <div v-if="$page.props.auth?.user" class="mt-4 pt-4 border-t border-gray-100">
                    <Link
                        href="/orders"
                        class="text-xs font-semibold text-gray-500 hover:text-[#0d3b37] hover:underline"
                    >
                        আপনার পূর্বের সকল অর্ডার দেখতে অর্ডার হিস্ট্রি পেজে যান →
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
