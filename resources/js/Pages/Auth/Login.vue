<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AppLayout>
        <Head title="লগইন — Al-Aqsa Mart" />

        <div class="max-w-md mx-auto my-12 bg-white rounded-3xl p-8 sm:p-10 shadow-sm border border-gray-100">
            <div class="text-center mb-8">
                <span class="text-4xl block mb-2">🔐</span>
                <h1 class="text-2xl font-black text-gray-900">অ্যাকাউন্টে লগইন</h1>
                <p class="text-gray-500 text-sm mt-1">আপনার ইমেইল ও পাসওয়ার্ড দিন</p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">ইমেইল এড্রেস</label>
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
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">পাসওয়ার্ড</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="••••••••"
                    />
                    <p v-if="form.errors.password" class="text-xs text-rose-500 mt-1">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-gray-600 cursor-pointer">
                        <input type="checkbox" v-model="form.remember" class="rounded text-[#0d3b37] focus:ring-emerald-500" />
                        <span>মনে রাখুন</span>
                    </label>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-gradient-to-r from-[#0d3b37] via-[#047857] to-[#b58d3d] hover:opacity-95 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-emerald-900/20 active:scale-98 transition disabled:opacity-50"
                >
                    <span v-if="form.processing">লগইন হচ্ছে...</span>
                    <span v-else>লগইন করুন</span>
                </button>
            </form>

            <div class="text-center mt-6 text-sm text-gray-600">
                নতুন ব্যবহারকারী?
                <Link href="/register" class="font-bold text-[#0d3b37] hover:underline">
                    অ্যাকাউন্ট খুলুন
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
