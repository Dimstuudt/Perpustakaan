<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle, BookOpen, ArrowRight, Smartphone, Clock, CheckCircle2, BookMarked, Sparkles } from 'lucide-vue-next';
import Password from 'primevue/password';
import InputText from 'primevue/inputtext';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';

// Props bawaan Laravel Breeze
defineProps<{
  status?: string;
  canResetPassword: boolean;
}>();

// Data form login
const form = useForm({
  login: '',
  password: '',
  remember: false,
});

const isVisible = ref(false);

// 🌟 Saat komponen dimount
onMounted(() => {
  // Animasi muncul
  setTimeout(() => {
    isVisible.value = true;
  }, 100);

  // Ambil flash message dari Laravel (pesan sukses/error)
  const page = usePage();
  const flash = page.props.flash as { success?: string; error?: string };

  // Tampilkan SweetAlert sukses
  if (flash?.success) {
    Swal.fire({
      title: 'Berhasil!',
      text: flash.success,
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#10B981',
      allowOutsideClick: false,
      allowEscapeKey: false,
    });
  }

  // Tampilkan SweetAlert gagal (kalau ada)
  if (flash?.error) {
    Swal.fire({
      title: 'Gagal!',
      text: flash.error,
      icon: 'error',
      confirmButtonText: 'Coba Lagi',
      confirmButtonColor: '#EF4444',
    });
  }
});

// Fungsi submit login
const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>


<template>
    <Head title="Log in" />

    <div class="min-h-screen flex overflow-hidden relative">



        <!-- Animated Background Particles -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
            <div class="particle particle-4"></div>
            <div class="particle particle-5"></div>
        </div>

        <!-- Left Side - Image Section with Content -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <!-- Background Image - Clear & Visible -->
            <img
                src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2000"
                alt="Library"
                class="absolute inset-0 w-full h-full object-cover scale-animation"
            />

            <!-- Gradient overlay for text readability -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/60 via-indigo-900/50 to-purple-900/60"></div>

            <!-- Floating decoration circles -->
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
                            <p class="text-blue-200 text-xs flex items-center gap-1">
                                <Sparkles class="w-3 h-3" />
                                Smart Library System
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Main Heading -->
                <div class="slide-in-left delay-200 space-y-3">
                    <h2 class="text-4xl font-bold leading-tight">
                        Peminjaman Buku<br/>
                        <span class="bg-gradient-to-r from-blue-300 via-indigo-300 to-purple-300 bg-clip-text text-transparent">
                            Lebih Mudah & Praktis
                        </span>
                    </h2>
                    <p class="text-lg text-blue-100 leading-relaxed max-w-lg">
                        Pinjam buku favorit Anda secara online kapan saja, dimana saja. Ambil langsung di perpustakaan tanpa perlu antri!
                    </p>
                </div>

                <!-- Features with Icons - Compact Grid -->
                <div class="grid grid-cols-2 gap-3 slide-in-left delay-400">
                    <div class="feature-card flex flex-col items-center text-center gap-2 p-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                            <Smartphone class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm mb-1">Pinjam Dari Rumah</h3>
                            <p class="text-xs text-blue-100">
                                Pilih buku dari smartphone Anda
                            </p>
                        </div>
                    </div>

                    <div class="feature-card flex flex-col items-center text-center gap-2 p-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-lg">
                            <CheckCircle2 class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm mb-1">Ambil Di Perpustakaan</h3>
                            <p class="text-xs text-blue-100">
                                Ambil buku yang sudah dibooking
                            </p>
                        </div>
                    </div>

                    <div class="feature-card flex flex-col items-center text-center gap-2 p-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg">
                            <Clock class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm mb-1">Notifikasi Otomatis</h3>
                            <p class="text-xs text-blue-100">
                                Pengingat sebelum jatuh tempo
                            </p>
                        </div>
                    </div>

                    <div class="feature-card flex flex-col items-center text-center gap-2 p-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center shadow-lg">
                            <BookMarked class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm mb-1">Riwayat Peminjaman</h3>
                            <p class="text-xs text-blue-100">
                                Kelola peminjaman dengan mudah
                            </p>
                        </div>
                    </div>
                </div>

                <!-- CTA Text -->
                <div class="slide-in-left delay-600 pt-4 border-t border-white/20">
                    <p class="text-blue-100 text-base">
                        ✨ <strong>Hemat waktu</strong> dan nikmati pengalaman meminjam buku yang lebih modern!
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-gradient-to-br from-slate-50 to-blue-50 relative">

            <div class="w-full max-w-md relative z-10" :class="{'fade-in-scale': isVisible}">



                <!-- Header -->
                <div class="text-center mb-8 fade-in delay-100">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 shadow-xl mb-3 pulse-slow">
                        <BookOpen class="w-7 h-7 text-white" />
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                    <p class="text-gray-600">Masuk untuk mulai meminjam buku</p>
                </div>

                <!-- Tombol Kembali -->
<a
  href="/welcome"
  class="group inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-blue-700 transition-all duration-300 mb-8"
>
  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600 transition-transform duration-300 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
  </svg>
  <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
    Kembali ke Halaman Utama
  </span>
</a>


                <!-- Status Message -->
                <div v-if="status" class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 fade-in shake">
                    <p class="text-sm font-medium text-green-700">{{ status }}</p>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Username/Email -->
                    <div class="space-y-2 fade-in delay-200 slide-up">
                        <Label for="login" class="text-sm font-semibold text-gray-700">
                            Username atau Email
                        </Label>
                        <InputText
                            id="login"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            v-model="form.login"
                            placeholder="Masukkan username atau email"
                            class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900 placeholder:text-gray-400 text-base hover:border-gray-300"
                        />
                        <InputError :message="form.errors.login" class="text-sm" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2 fade-in delay-300 slide-up">
                        <div class="flex items-center justify-between">
                            <Label for="password" class="text-sm font-semibold text-gray-700">
                                Password
                            </Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline transition-colors"
                                :tabindex="5"
                            >
                                Lupa password?
                            </TextLink>
                        </div>
                        <Password
                            id="password"
                            type="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            v-model="form.password"
                            placeholder="Masukkan password"
                            inputClass="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900 text-base hover:border-gray-300"
                            class="w-full"
                            :feedback="false"
                            toggleMask
                        />
                        <InputError :message="form.errors.password" class="text-sm" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center fade-in delay-400 slide-up">
                        <Label for="remember" class="flex items-center gap-3 cursor-pointer group">
                            <Checkbox
                                id="remember"
                                v-model="form.remember"
                                :tabindex="3"
                                class="w-5 h-5 rounded-md border-2 border-gray-300 data-[state=checked]:bg-blue-600 data-[state=checked]:border-blue-600 transition-all duration-200"
                            />
                            <span class="text-sm font-medium text-gray-700 select-none group-hover:text-gray-900">Ingat saya</span>
                        </Label>
                    </div>

                    <!-- Login Button -->
                    <Button
                        type="submit"
                        class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white text-base font-semibold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-[1.03] hover:-translate-y-1 active:scale-[0.98] active:translate-y-0 transition-all duration-300 flex items-center justify-center gap-2 fade-in delay-500 slide-up group"
                        :tabindex="4"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin" />
                        <template v-else>
                            <span>Masuk Sekarang</span>
                            <ArrowRight class="w-5 h-5 transition-transform group-hover:translate-x-2" />
                        </template>
                    </Button>

                    <!-- Divider -->
                    <div class="relative my-6 fade-in delay-600">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-gradient-to-br from-slate-50 to-blue-50 text-gray-500 font-medium">Atau masuk dengan</span>
                        </div>
                    </div>

                    <!-- Google Login -->
                    <a
                        href="/auth/google"
                        class="w-full flex items-center justify-center gap-3 px-4 py-3.5 rounded-xl border-2 border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 text-gray-700 font-medium transition-all duration-300 transform hover:scale-[1.02] hover:-translate-y-0.5 active:scale-[0.98] shadow-sm hover:shadow-md fade-in delay-700 slide-up"
                    >
                        <img
                            src="https://www.svgrepo.com/show/475656/google-color.svg"
                            alt="Google"
                            class="h-5 w-5"
                        />
                        <span>Google</span>
                    </a>

                    <!-- Sign Up Link -->
                    <div class="text-center text-sm text-gray-600 pt-4 fade-in delay-800">
                        Belum punya akun?
                        <TextLink
                            :href="route('register')"
                            :tabindex="5"
                            class="font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors ml-1"
                        >
                            Daftar sekarang
                        </TextLink>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center fade-in delay-900">
                    <p class="text-xs text-gray-500">&copy; 2025 Sistem Perpustakaan Digital</p>
                </div>
            </div>
        </div>
    </div>
</template>

    <style scoped>
    /* Enhanced Animations */
    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
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

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        33% { transform: translateY(-20px) rotate(5deg); }
        66% { transform: translateY(-10px) rotate(-5deg); }
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

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    /* Apply Animations */
    .fade-in-scale {
        animation: fadeInScale 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
    }

    .slide-in-left {
        animation: slideInLeft 0.8s ease-out forwards;
    }

    .slide-up {
        animation: slideUp 0.6s ease-out forwards;
        opacity: 0;
    }

    .bounce-in {
        animation: bounceIn 0.8s ease-out;
    }

    .pulse-animation {
        animation: pulse 2s ease-in-out infinite;
    }

    .pulse-slow {
        animation: pulseSlow 3s ease-in-out infinite;
    }

    .scale-animation {
        animation: scaleIn 20s ease-in-out infinite alternate;
    }

    .shake {
        animation: shake 0.5s ease-in-out;
    }

    /* Floating Circles */
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

    /* Background Particles */
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

    /* Feature Cards */
    .feature-card {
        opacity: 0;
        animation: slideInLeft 0.8s ease-out forwards;
    }

    .feature-card:nth-child(1) { animation-delay: 0.6s; }
    .feature-card:nth-child(2) { animation-delay: 0.8s; }
    .feature-card:nth-child(3) { animation-delay: 1s; }
    .feature-card:nth-child(4) { animation-delay: 1.2s; }

    /* Delays */
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-500 { animation-delay: 0.5s; }
    .delay-600 { animation-delay: 0.6s; }
    .delay-700 { animation-delay: 0.7s; }
    .delay-800 { animation-delay: 0.8s; }
    .delay-900 { animation-delay: 0.9s; }

    /* PrimeVue Customization */
    :deep(.p-password) { width: 100%; }
    :deep(.p-password input) { width: 100%; }
    :deep(.p-inputtext:focus) {
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .fade-in-scale {
            animation: fadeIn 0.6s ease-out forwards;
        }
    }
    </style>
