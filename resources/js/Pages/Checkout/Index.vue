<script setup>
import { computed } from 'vue';
import { useForm, usePage, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    items: Array,
    subtotal: Number,
    shipping: Number,
    total: Number,
});

const page = usePage();
const user = page.props.auth?.user;

const form = useForm({
    customer_name: user ? user.name : '',
    customer_email: user ? user.email : '',
    customer_phone: '',
    shipping_address: '',
    city: 'ঢাকা (Dhaka)',
    postal_code: '',
    payment_method: 'cod',
    notes: '',
});

// Common cities in Bangladesh
const popularCities = [
    'ঢাকা (Dhaka)',
    'চট্টগ্রাম (Chattogram)',
    'সিলেট (Sylhet)',
    'রাজশাহী (Rajshahi)',
    'খুলনা (Khulna)',
    'বরিশাল (Barishal)',
    'রংপুর (Rangpur)',
    'ময়মনসিংহ (Mymensingh)',
    'কুমিল্লা (Cumilla)',
    'গাজীপুর (Gazipur)',
    'নারায়ণগঞ্জ (Narayanganj)',
    'অন্যান্য জেলা (Other District)'
];

const submitOrder = () => {
    form.post('/checkout/place');
};
</script>

<template>
    <AppLayout>
        <Head title="অর্ডার সম্পন্ন করুন — Al-Aqsa Mart" />

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 mb-6">
            <Link href="/" class="hover:text-[#0d3b37] transition font-medium">হোম</Link>
            <span>/</span>
            <Link href="/cart" class="hover:text-[#0d3b37] transition font-medium">কার্ট</Link>
            <span>/</span>
            <span class="text-gray-800 font-semibold">চেকআউট ও অর্ডার কনফার্মেশন</span>
        </nav>

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight">
                চেকআউট ও ডেলিভারি তথ্য 📦
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                নিচের ফর্মটি পূরণ করে অর্ডার নিশ্চিত করুন। ক্যাশ অন ডেলিভারিতে পণ্য বুঝে পেয়ে মূল্য পরিশোধ করুন।
            </p>
        </div>

        <form @submit.prevent="submitOrder" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-16">
            <!-- Left: Checkout Form (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Step 1: Customer Information Card -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-gray-200/80 space-y-5">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-[#0d3b37] text-white flex items-center justify-center text-xs font-bold">
                                ১
                            </span>
                            <h2 class="text-base sm:text-lg font-bold text-gray-900">
                                কাস্টমার তথ্য ও ডেলিভারি ঠিকানা
                            </h2>
                        </div>
                        <span class="text-xs text-rose-500 font-medium">* চিহ্নিত ঘরগুলো আবশ্যক</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                আপনার নাম *
                            </label>
                            <input
                                v-model="form.customer_name"
                                type="text"
                                required
                                class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b37] focus:bg-white transition"
                                placeholder="পূর্ণ নাম লিখুন"
                            />
                            <p v-if="form.errors.customer_name" class="text-xs text-rose-500 mt-1">{{ form.errors.customer_name }}</p>
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                মোবাইল নম্বর *
                            </label>
                            <input
                                v-model="form.customer_phone"
                                type="tel"
                                required
                                class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b37] focus:bg-white transition"
                                placeholder="০১৭১১-xxxxxx"
                            />
                            <p v-if="form.errors.customer_phone" class="text-xs text-rose-500 mt-1">{{ form.errors.customer_phone }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Email (Optional for convenience) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                ইমেইল ঠিকানা <span class="text-gray-400 font-normal">(ঐচ্ছিক)</span>
                            </label>
                            <input
                                v-model="form.customer_email"
                                type="email"
                                class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b37] focus:bg-white transition"
                                placeholder="email@example.com"
                            />
                            <p v-if="form.errors.customer_email" class="text-xs text-rose-500 mt-1">{{ form.errors.customer_email }}</p>
                        </div>

                        <!-- City Selection -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                জেলা / শহর *
                            </label>
                            <select
                                v-model="form.city"
                                required
                                class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b37] focus:bg-white transition cursor-pointer font-medium"
                            >
                                <option v-for="c in popularCities" :key="c" :value="c">{{ c }}</option>
                            </select>
                            <p v-if="form.errors.city" class="text-xs text-rose-500 mt-1">{{ form.errors.city }}</p>
                        </div>
                    </div>

                    <!-- Full Delivery Address -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            সম্পূর্ণ ডেলিভারি ঠিকানা *
                        </label>
                        <textarea
                            v-model="form.shipping_address"
                            rows="2"
                            required
                            class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b37] focus:bg-white transition"
                            placeholder="বাসা/হোল্ডিং নং, রোড নং, এলাকা বা গ্রামের নাম লিখুন..."
                        ></textarea>
                        <p v-if="form.errors.shipping_address" class="text-xs text-rose-500 mt-1">{{ form.errors.shipping_address }}</p>
                    </div>

                    <!-- Order Notes (Optional) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            অর্ডার নোট বা বিশেষ নির্দেশনা <span class="text-gray-400 font-normal">(ঐচ্ছিক)</span>
                        </label>
                        <input
                            v-model="form.notes"
                            type="text"
                            class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b37] focus:bg-white transition"
                            placeholder="যেমন: বিকেলে ডেলিভারি দিলে ভালো হয় / কল করে আসবেন"
                        />
                    </div>
                </div>

                <!-- Step 2: Payment Method Selection -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-gray-200/80 space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                        <span class="w-7 h-7 rounded-full bg-[#0d3b37] text-white flex items-center justify-center text-xs font-bold">
                            ২
                        </span>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900">
                            পেমেন্ট পদ্ধতি নির্বাচন করুন
                        </h2>
                    </div>

                    <div class="space-y-3">
                        <!-- Cash On Delivery Option -->
                        <label
                            class="flex items-center gap-3.5 p-4 border-2 rounded-xl cursor-pointer transition select-none"
                            :class="form.payment_method === 'cod' ? 'border-[#0d3b37] bg-[#0d3b37]/5' : 'border-gray-200 hover:border-gray-300 bg-white'"
                        >
                            <input
                                type="radio"
                                v-model="form.payment_method"
                                value="cod"
                                class="w-4 h-4 text-[#0d3b37] focus:ring-[#0d3b37]"
                            />
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-900 text-sm">ক্যাশ অন ডেলিভারি (Cash on Delivery)</span>
                                    <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded">জনপ্রিয়</span>
                                </div>
                                <span class="text-xs text-gray-500">পণ্য হাতে পেয়ে দেখে মূল্য পরিশোধ করুন। কোনো অগ্রিম চার্জ নেই।</span>
                            </div>
                            <span class="text-2xl">💵</span>
                        </label>

                        <!-- bKash Option -->
                        <label
                            class="flex items-center gap-3.5 p-4 border-2 rounded-xl cursor-pointer transition select-none"
                            :class="form.payment_method === 'bkash' ? 'border-pink-600 bg-pink-50/30' : 'border-gray-200 hover:border-gray-300 bg-white'"
                        >
                            <input
                                type="radio"
                                v-model="form.payment_method"
                                value="bkash"
                                class="w-4 h-4 text-pink-600 focus:ring-pink-500"
                            />
                            <div class="flex-1">
                                <span class="font-bold text-gray-900 text-sm block">বিকাশ (bKash Payment)</span>
                                <span class="text-xs text-gray-500">বিকাশের মাধ্যমে সরাসরি পেমেন্ট সুবিধা।</span>
                            </div>
                            <span class="font-black text-pink-600 text-sm bg-pink-50 px-2 py-1 rounded">bKash</span>
                        </label>

                        <!-- Nagad Option -->
                        <label
                            class="flex items-center gap-3.5 p-4 border-2 rounded-xl cursor-pointer transition select-none"
                            :class="form.payment_method === 'nagad' ? 'border-amber-600 bg-amber-50/30' : 'border-gray-200 hover:border-gray-300 bg-white'"
                        >
                            <input
                                type="radio"
                                v-model="form.payment_method"
                                value="nagad"
                                class="w-4 h-4 text-amber-600 focus:ring-amber-500"
                            />
                            <div class="flex-1">
                                <span class="font-bold text-gray-900 text-sm block">নগদ (Nagad Payment)</span>
                                <span class="text-xs text-gray-500">নগদের মাধ্যমে সহজ ও দ্রুত পেমেন্ট সুবিধা।</span>
                            </div>
                            <span class="font-black text-amber-600 text-sm bg-amber-50 px-2 py-1 rounded">Nagad</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right: Order Items & Summary (5 cols, Sticky) -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-gray-200/80 sticky top-24 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">
                        আপনার অর্ডার ({{ items.length }} টি পণ্য)
                    </h3>
                    <Link href="/cart" class="text-xs font-semibold text-[#0d3b37] hover:underline">
                        পরিবর্তন করুন
                    </Link>
                </div>

                <!-- Product Items List -->
                <div class="space-y-3 max-h-72 overflow-y-auto pr-1 divide-y divide-gray-50">
                    <div
                        v-for="item in items"
                        :key="item.id"
                        class="flex items-center gap-3 pt-3 first:pt-0"
                    >
                        <div class="w-12 h-12 rounded-lg bg-gray-50 border border-gray-200 overflow-hidden flex-shrink-0 p-1">
                            <img
                                :src="item.image || 'https://placehold.co/100x100?text=Item'"
                                :alt="item.name"
                                class="w-full h-full object-contain"
                            />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-semibold text-gray-800 truncate" :title="item.name">
                                {{ item.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                ৳{{ item.price }} × {{ item.quantity }}
                            </p>
                        </div>
                        <span class="font-bold text-gray-900 text-sm flex-shrink-0">
                            ৳{{ item.subtotal }}
                        </span>
                    </div>
                </div>

                <!-- Price Calculations -->
                <div class="border-t border-gray-100 pt-4 space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>পণ্যের মোট মূল্য</span>
                        <span class="font-bold text-gray-800">৳{{ subtotal }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span>ডেলিভারি চার্জ</span>
                        <span v-if="shipping === 0" class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs font-bold">
                            ফ্রি ডেলিভারি!
                        </span>
                        <span v-else class="font-bold text-gray-800">৳{{ shipping }}</span>
                    </div>

                    <div v-if="subtotal < 1000" class="text-[11px] text-[#b58d3d] bg-[#b58d3d]/10 p-2 rounded-lg font-medium">
                        💡 আর মাত্র ৳{{ 1000 - subtotal }} টাকার পণ্য কিনলে ডেলিভারি চার্জ সম্পূর্ণ ফ্রি!
                    </div>

                    <div class="border-t-2 border-gray-100 pt-3 flex justify-between items-baseline">
                        <span class="font-black text-gray-900 text-base">সর্বমোট প্রদেয় মূল্য:</span>
                        <span class="text-2xl font-black text-[#0d3b37]">৳{{ total }}</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-[#0d3b37] hover:bg-[#064e3b] text-white font-bold py-3.5 px-6 rounded-xl shadow-md active:scale-98 transition flex items-center justify-center gap-2 text-base disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="form.processing">অর্ডার প্রসেস হচ্ছে...</span>
                    <span v-else class="flex items-center gap-1.5">
                        <span>✓</span> অর্ডার নিশ্চিত করুন (৳{{ total }})
                    </span>
                </button>

                <p class="text-[11px] text-center text-gray-400">
                    অর্ডার নিশ্চিত করে আপনি আল-আকসা মার্টের শর্তাবলী ও প্রাইভেসি পলিসিতে সম্মতি দিচ্ছেন।
                </p>
            </div>
        </form>
    </AppLayout>
</template>
