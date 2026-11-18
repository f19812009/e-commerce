<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AppLayout>
        <Head title="রেজিস্টার — ShopBD" />

        <div class="max-w-md mx-auto my-10 bg-white rounded-3xl p-8 sm:p-10 shadow-sm border border-gray-100">
            <div class="text-center mb-8">
                <span class="text-4xl block mb-2">✨</span>
                <h1 class="text-2xl font-black text-gray-900">নতুন অ্যাকাউন্ট তৈরি</h1>
                <p class="text-gray-500 text-sm mt-1">কয়েক সেকেন্ডেই রেজিস্ট্রেশন সম্পন্ন করুন</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">পূর্ণ নাম *</label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="আপনার নাম"
                    />
                    <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">ইমেইল এড্রেস *</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="example@mail.com"
                    />
                    <p v-if="form.errors.email" class="text-xs text-rose-500 mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">পাসওয়ার্ড (কমপক্ষে ৮ অক্ষর) *</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="••••••••"
                    />
                    <p v-if="form.errors.password" class="text-xs text-rose-500 mt-1">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">পাসওয়ার্ড নিশ্চিত করুন *</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="••••••••"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-gradient-to-r from-[#0d3b37] via-[#047857] to-[#b58d3d] hover:opacity-95 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-emerald-900/20 active:scale-98 transition mt-2 disabled:opacity-50"
                >
                    <span v-if="form.processing">রেজিস্টার হচ্ছে...</span>
                    <span v-else>রেজিস্টার করুন</span>
                </button>
            </form>

            <div class="text-center mt-6 text-sm text-gray-600">
                ইতিমধ্যে অ্যাকাউন্ট আছে?
                <Link href="/login" class="font-bold text-[#0d3b37] hover:underline">
                    লগইন করুন
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
