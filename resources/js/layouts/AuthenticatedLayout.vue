<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const user = page.props.auth.user;

const sidebarOpen = ref(false);
</script>

<template>
    <div class="relative flex h-screen overflow-hidden bg-brand-neutralBg text-brand-neutralText">
        <!-- Overlay (muncul di HP saat sidebar dibuka) -->
        <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/40 md:hidden"></div>

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed z-40 flex h-full w-64 flex-col border-r bg-white shadow-sm transition-transform duration-200 md:static"
        >
            <div class="flex h-16 items-center justify-between border-b px-6">
                <span class="flex items-center gap-2 text-2xl font-extrabold tracking-wide text-brand-variant">
                    <i class="fas fa-fingerprint text-brand-primary"></i> Sinarta
                </span>
                <button @click="sidebarOpen = false" class="text-gray-400 md:hidden">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <nav class="flex-1 space-y-2 overflow-y-auto px-4 py-6">
                <template v-if="user.role === 'karyawan'">
                    <p class="mb-2 px-4 text-xs font-bold uppercase tracking-wider text-gray-400">Menu Utama</p>
                    <Link
                        :href="route('absensi.index')"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-gray-600 hover:bg-red-50 hover:text-brand-variant"
                    >
                        <i class="fas fa-camera w-5"></i> Absensi
                    </Link>

                    <p class="mb-2 mt-6 px-4 text-xs font-bold uppercase tracking-wider text-gray-400">Pengajuan</p>
                    <Link
                        :href="route('izin.index')"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-gray-600 hover:bg-red-50 hover:text-brand-variant"
                    >
                        <i class="fas fa-envelope-open-text w-5"></i> Izin & Cuti
                    </Link>
                    <Link
                        :href="route('lembur.index')"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-gray-600 hover:bg-red-50 hover:text-brand-variant"
                    >
                        <i class="fas fa-business-time w-5"></i> Lembur
                    </Link>
                    <Link
                        :href="route('dinas.index')"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-gray-600 hover:bg-red-50 hover:text-brand-variant"
                    >
                        <i class="fas fa-route w-5"></i> Dinas
                    </Link>

                    <p class="mb-2 mt-6 px-4 text-xs font-bold uppercase tracking-wider text-gray-400">Keuangan</p>
                    <Link class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-gray-600 hover:bg-red-50 hover:text-brand-variant">
                        <i class="fas fa-file-invoice-dollar w-5"></i> Slip Gaji Saya
                    </Link>
                </template>

                <template v-if="user.role === 'atasan' || user.role === 'admin'">
                    <p class="mb-2 mt-6 px-4 text-xs font-bold uppercase tracking-wider text-gray-400">Manajemen</p>
                    <Link
                        :href="route('approval.index')"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-gray-600 hover:bg-red-50 hover:text-brand-variant"
                    >
                        <i class="fas fa-check-double w-5"></i> Approval Pengajuan
                    </Link>
                    <Link class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-gray-600 hover:bg-red-50 hover:text-brand-variant">
                        <i class="fas fa-file-signature w-5"></i> Generate Slip Gaji
                    </Link>
                </template>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="relative flex h-full w-full flex-1 flex-col">
            <header class="z-10 flex h-16 items-center justify-between border-b bg-white/80 px-4 backdrop-blur-md md:px-6">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="text-gray-500 hover:text-brand-primary md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-lg font-bold text-brand-neutralText md:text-xl">{{ page.props.title ?? '' }}</h1>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <button class="flex h-9 w-9 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 md:h-10 md:w-10">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="hidden h-6 w-px bg-gray-300 md:block"></div>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-primary text-xs font-bold text-white md:h-9 md:w-9 md:text-sm"
                        >
                            {{
                                user.name
                                    .split(' ')
                                    .map((n) => n[0])
                                    .join('')
                                    .slice(0, 3)
                            }}
                        </div>
                        <div class="hidden text-left md:block">
                            <p class="text-sm font-bold leading-tight text-gray-800">{{ user.name }}</p>
                            <p class="text-xs font-medium text-gray-500">{{ user.jabatan ?? user.role }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8">
                <slot />
            </div>
        </main>
    </div>
</template>
