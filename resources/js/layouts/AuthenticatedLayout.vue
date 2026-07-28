<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth.user

const sidebarOpen = ref(false)
</script>

<template>
  <div class="bg-brand-neutralBg text-brand-neutralText flex h-screen overflow-hidden relative">
    <!-- Overlay (muncul di HP saat sidebar dibuka) -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-black/40 z-30 md:hidden"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
      class="w-64 bg-white border-r flex flex-col h-full shadow-sm z-40 fixed md:static transition-transform duration-200"
    >
      <div class="h-16 flex items-center justify-between px-6 border-b">
        <span class="text-brand-variant font-extrabold text-2xl tracking-wide flex items-center gap-2">
          <i class="fas fa-fingerprint text-brand-primary"></i> Sinarta
        </span>
        <button @click="sidebarOpen = false" class="md:hidden text-gray-400">
          <i class="fas fa-times text-lg"></i>
        </button>
      </div>

      <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>
        <Link :href="route('dashboard')" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-brand-variant rounded-xl font-medium">
          <i class="fas fa-home w-5"></i> Dashboard
        </Link>
        <Link  class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-brand-variant rounded-xl font-medium">
          <i class="fas fa-camera w-5"></i> Absensi (Selfie)
        </Link>

        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mt-6 mb-2">Pengajuan</p>
        <Link :href="route('izin.index')" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-brand-variant rounded-xl font-medium">
          <i class="fas fa-envelope-open-text w-5"></i> Izin & Cuti
        </Link>
        <Link :href="route('lembur.index')"  class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-brand-variant rounded-xl font-medium">
          <i class="fas fa-business-time w-5"></i> Lembur
        </Link>
        <Link class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-brand-variant rounded-xl font-medium">
          <i class="fas fa-route w-5"></i> Dinas
        </Link>

        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mt-6 mb-2">Keuangan</p>
        <Link  class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-brand-variant rounded-xl font-medium">
          <i class="fas fa-file-invoice-dollar w-5"></i> Slip Gaji Saya
        </Link>

        <template v-if="user.role === 'atasan' || user.role === 'admin'">
          <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mt-6 mb-2">Manajemen</p>
          <Link class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-brand-variant rounded-xl font-medium">
            <i class="fas fa-check-double w-5"></i> Approval Pengajuan
          </Link>
          <Link  class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-brand-variant rounded-xl font-medium">
            <i class="fas fa-file-signature w-5"></i> Generate Slip Gaji
          </Link>
        </template>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full relative w-full">
      <header class="h-16 bg-white/80 backdrop-blur-md border-b flex items-center justify-between px-4 md:px-6 z-10">
        <div class="flex items-center gap-3">
          <button @click="sidebarOpen = true" class="md:hidden text-gray-500 hover:text-brand-primary">
            <i class="fas fa-bars text-xl"></i>
          </button>
          <h1 class="text-lg md:text-xl font-bold text-brand-neutralText">{{ page.props.title ?? '' }}</h1>
        </div>

        <div class="flex items-center gap-2 md:gap-4">
          <button class="w-9 h-9 md:w-10 md:h-10 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-500">
            <i class="fas fa-bell"></i>
          </button>
          <div class="h-6 w-px bg-gray-300 hidden md:block"></div>
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-brand-primary text-white flex items-center justify-center font-bold text-xs md:text-sm">
              {{ user.name.split(' ').map(n => n[0]).join('').slice(0, 3) }}
            </div>
            <div class="hidden md:block text-left">
              <p class="text-sm font-bold text-gray-800 leading-tight">{{ user.name }}</p>
              <p class="text-xs text-gray-500 font-medium">{{ user.jabatan ?? user.role }}</p>
            </div>
          </div>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50">
        <slot />
      </div>
    </main>
  </div>
</template>