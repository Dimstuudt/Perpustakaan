<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, BookOpen, ArrowRight, KeyRound, Lock, ShieldCheck, CheckCircle2 } from 'lucide-vue-next';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import { onMounted, ref } from 'vue';
import Swal from 'sweetalert2';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const isVisible = ref(false);

const submit = () => {
    form.post(route('password.store'), {
        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Password Anda telah berhasil diperbarui. Silakan login dengan password baru.',
                icon: 'success',
                confirmButtonText: 'Login Sekarang',
                confirmButtonColor: '#3b82f6',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = route('login');
                }
            });
        },
        onError: () => {
            Swal.fire({
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat memperbarui password. Silakan coba lagi.',
                icon: 'error',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#ef4444',
            });
        },
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, 100);
});
</script>

<template>
    <Head title="Reset password" />

    <div class="min-h-screen flex overflow-hidden relative">

        <!-- Animated Background Particles -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
            <div class="particle particle-4"></div>
            <div class="particle particle-5"></div>
        </div>

        <!-- Left Side - Image Section -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <!-- Background Image -->
            <img
                src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2000"
                alt="Library"
                class="absolute inset-0 w-full h-full object-cover scale-animation"
            />

            <!-- Gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/60 via-indigo-900/50 to-purple-900/60"></div>

            <!-- Floating circles -->
            <div class="absolute inset-0">
                <div class="floating-circle circle-1"></div>
                <div class="floating-circle circle-2"></div>
                <div class="floating-circle circle-3"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center p-12 text-white h-full space-y-6">

                <!-- Logo & Title -->
                <div class="slide-in-left">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center shadow-2xl pulse-animation">
                            <BookOpen class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Perpustakaan Digital</h1>
                            <p class="text-blue-200 text-xs">Smart Library System</p>
                        </div>
                    </div>
                </div>

                <!-- Main Heading -->
                <div class="slide-in-left delay-200 space-y-3">
                    <h2 class="text-4xl font-bold leading-tight">
                        Reset Password<br/>
                        <span class="bg-gradient-to-r from-blue-300 via-indigo-300 to-purple-300 bg-clip-text text-transparent">
                            Buat Password Baru
                        </span>
                    </h2>
                    <p class="text-lg text-blue-100 leading-relaxed max-w-lg">
                        Silakan buat password baru yang kuat dan aman untuk akun Anda.
                    </p>
                </div>

                <!-- Security Tips -->
                <div class="space-y-3 slide-in-left delay-400">
                    <div class="feature-card flex items-start gap-3 p-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-all duration-300">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                            <Lock class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm mb-1">Password Kuat</h3>
                            <p class="text-xs text-blue-100">
                                Gunakan kombinasi huruf besar, kecil, angka, dan simbol
                            </p>
                        </div>
                    </div>

                    <div class="feature-card flex items-start gap-3 p-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-all duration-300">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                            <ShieldCheck class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm mb-1">Minimal 8 Karakter</h3>
                            <p class="text-xs text-blue-100">
                                Password harus memiliki minimal 8 karakter
                            </p>
                        </div>
                    </div>

                    <div class="feature-card flex items-start gap-3 p-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-all duration-300">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                            <KeyRound class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm mb-1">Password Unik</h3>
                            <p class="text-xs text-blue-100">
                                Hindari menggunakan password yang sama dengan akun lain
                            </p>
                        </div>
                    </div>

                    <div class="feature-card flex items-start gap-3 p-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-all duration-300">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                            <CheckCircle2 class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm mb-1">Konfirmasi Password</h3>
                            <p class="text-xs text-blue-100">
                                Pastikan kedua password yang dimasukkan sama
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="slide-in-left delay-600 pt-4 border-t border-white/20">
                    <p class="text-blue-100 text-sm">
                        🔒 <strong>Data Anda aman.</strong> Password akan dienkripsi dengan teknologi terkini.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-gradient-to-br from-slate-50 to-blue-50 relative">

            <div class="w-full max-w-md relative z-10" :class="{'fade-in-scale': isVisible}">

                <!-- Mobile Header -->
                <div class="lg:hidden flex items-center justify-center mb-6 bounce-in">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg pulse-animation">
                            <BookOpen class="w-6 h-6 text-white" />
                        </div>
                        <span class="text-2xl font-bold text-gray-900">Perpustakaan</span>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mb-6 fade-in">
                    <a
                        href="/welcome"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                    >
                        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Kembali ke Halaman Utama
                    </a>
                </div>

                <!-- Header -->
                <div class="text-center mb-8 fade-in delay-100">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 shadow-xl mb-3 pulse-slow">
                        <KeyRound class="w-7 h-7 text-white" />
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Reset Password</h2>
                    <p class="text-gray-600">Buat password baru untuk akun Anda</p>
                </div>

                <!-- Info Box -->
                <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 fade-in delay-200">
                    <div class="flex gap-3">
                        <ShieldCheck class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Syarat Password</p>
                            <p class="text-xs text-blue-700">Minimal 8 karakter dengan kombinasi huruf besar, kecil, angka, dan simbol untuk keamanan maksimal.</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit">
                    <div class="space-y-5">

                        <!-- Email (readonly) -->
                        <div class="space-y-2 fade-in delay-300 slide-up">
                            <Label for="email" class="text-sm font-semibold text-gray-700">
                                Email
                            </Label>
                            <InputText
                                id="email"
                                type="email"
                                name="email"
                                autocomplete="email"
                                v-model="form.email"
                                readonly
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed"
                            />
                            <InputError :message="form.errors.email" class="text-sm" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2 fade-in delay-400 slide-up">
                            <Label for="password" class="text-sm font-semibold text-gray-700">
                                Password Baru
                            </Label>
                            <Password
                                id="password"
                                type="password"
                                name="password"
                                autocomplete="new-password"
                                v-model="form.password"
                                autofocus
                                toggleMask
                                placeholder="Masukkan password baru"
                                inputClass="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900 hover:border-gray-300"
                                class="w-full"
                            />
                            <InputError :message="form.errors.password" class="text-sm" />
                        </div>

                        <!-- Password Confirmation -->
                        <div class="space-y-2 fade-in delay-500 slide-up">
                            <Label for="password_confirmation" class="text-sm font-semibold text-gray-700">
                                Konfirmasi Password
                            </Label>
                            <Password
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                autocomplete="new-password"
                                v-model="form.password_confirmation"
                                toggleMask
                                :feedback="false"
                                placeholder="Ulangi password baru"
                                inputClass="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900 hover:border-gray-300"
                                class="w-full"
                            />
                            <InputError :message="form.errors.password_confirmation" class="text-sm" />
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white text-base font-semibold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-[1.03] hover:-translate-y-1 active:scale-[0.98] active:translate-y-0 transition-all duration-300 flex items-center justify-center gap-2 fade-in delay-600 slide-up group"
                            :disabled="form.processing"
                        >
                            <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin" />
                            <template v-else>
                                <Lock class="w-5 h-5" />
                                <span>Reset Password</span>
                                <ArrowRight class="w-5 h-5 transition-transform group-hover:translate-x-2" />
                            </template>
                        </Button>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center fade-in delay-700">
                    <p class="text-xs text-gray-500">&copy; 2025 Sistem Perpustakaan Digital</p>
                </div>
            </div>
        </div>
    </div>
    </template>

    <style scoped>
    /* Animations */
    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.9) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-50px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes bounceIn {
        0% { opacity: 0; transform: scale(0.3); }
        50% { opacity: 1; transform: scale(1.05); }
        70% { transform: scale(0.9); }
        100% { transform: scale(1); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes pulseSlow {
        0%, 100% { transform: scale(1); box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2); }
        50% { transform: scale(1.05); box-shadow: 0 15px 35px rgba(59, 130, 246, 0.3); }
    }

    @keyframes floatCircle {
        0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; }
        50% { transform: translate(30px, -30px) scale(1.1); opacity: 0.5; }
    }

    @keyframes scaleIn {
        from { transform: scale(1); }
        to { transform: scale(1.02); }
    }

    @keyframes particleFloat {
        0%, 100% { transform: translate(0, 0); }
        25% { transform: translate(100px, -100px); }
        50% { transform: translate(200px, -50px); }
        75% { transform: translate(100px, 50px); }
    }

    .fade-in-scale { animation: fadeInScale 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .fade-in { animation: fadeIn 0.6s ease-out forwards; opacity: 0; }
    .slide-in-left { animation: slideInLeft 0.8s ease-out forwards; }
    .slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    .bounce-in { animation: bounceIn 0.8s ease-out; }
    .pulse-animation { animation: pulse 2s ease-in-out infinite; }
    .pulse-slow { animation: pulseSlow 3s ease-in-out infinite; }
    .scale-animation { animation: scaleIn 20s ease-in-out infinite alternate; }

    .floating-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        animation: floatCircle 8s ease-in-out infinite;
    }

    .circle-1 { width: 300px; height: 300px; top: 10%; left: 10%; animation-delay: 0s; }
    .circle-2 { width: 200px; height: 200px; top: 60%; right: 15%; animation-delay: 2s; }
    .circle-3 { width: 150px; height: 150px; bottom: 20%; left: 30%; animation-delay: 4s; }

    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        border-radius: 50%;
        animation: particleFloat 15s ease-in-out infinite;
        opacity: 0.6;
    }

    .particle-1 { top: 20%; left: 10%; animation-delay: 0s; }
    .particle-2 { top: 40%; right: 20%; animation-delay: 3s; }
    .particle-3 { bottom: 30%; left: 30%; animation-delay: 6s; }
    .particle-4 { top: 60%; right: 40%; animation-delay: 9s; }
    .particle-5 { bottom: 20%; right: 10%; animation-delay: 12s; }

    .feature-card {
        opacity: 0;
        animation: slideInLeft 0.8s ease-out forwards;
    }

    .feature-card:nth-child(1) { animation-delay: 0.6s; }
    .feature-card:nth-child(2) { animation-delay: 0.8s; }
    .feature-card:nth-child(3) { animation-delay: 1s; }
    .feature-card:nth-child(4) { animation-delay: 1.2s; }

    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-500 { animation-delay: 0.5s; }
    .delay-600 { animation-delay: 0.6s; }
    .delay-700 { animation-delay: 0.7s; }

    :deep(.p-password) { width: 100%; }
    :deep(.p-password input) { width: 100%; }
    :deep(.p-inputtext:focus) {
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
    }

    @media (max-width: 1024px) {
        .fade-in-scale { animation: fadeIn 0.6s ease-out forwards; }
    }
    </style>

