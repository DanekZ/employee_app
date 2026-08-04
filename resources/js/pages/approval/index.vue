<script setup>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AuthenticatedLayout })

defineProps({
  izin: Array,
  lembur: Array,
  dinas: Array,
})

function approve(routeName, id) {
  router.patch(route(routeName, id))
}

function reject(routeName, id) {
  router.patch(route(routeName.replace('.approve', '.reject'), id))
}
</script>

<template>
  <div class="max-w-5xl mx-auto space-y-8">
    <h2 class="text-2xl font-bold text-gray-900">Approval Pengajuan</h2>

    <!-- Izin -->
    <div>
      <h3 class="text-lg font-bold text-gray-800 mb-3">Izin ({{ izin.length }})</h3>
      <div v-if="izin.length === 0" class="text-gray-500 text-sm">Tidak ada pengajuan izin pending.</div>
      <div v-for="item in izin" :key="item.id" class="bg-white border rounded-xl p-4 flex justify-between items-center mb-2">
        <div>
          <p class="font-semibold text-gray-800">{{ item.user.name }} — {{ item.jenis }}</p>
          <p class="text-sm text-gray-500">{{ item.tujuan }} · {{ item.tanggal_mulai }}</p>
        </div>
        <div class="flex gap-2">
          <button @click="approve('izin.approve', item.id)" class="px-3 py-1.5 bg-brand-accent text-white text-sm font-bold rounded-lg">Approve</button>
          <button @click="reject('izin.approve', item.id)" class="px-3 py-1.5 bg-red-500 text-white text-sm font-bold rounded-lg">Reject</button>
        </div>
      </div>
    </div>

    <!-- Lembur -->
    <div>
      <h3 class="text-lg font-bold text-gray-800 mb-3">Lembur ({{ lembur.length }})</h3>
      <div v-if="lembur.length === 0" class="text-gray-500 text-sm">Tidak ada pengajuan lembur pending.</div>
      <div v-for="item in lembur" :key="item.id" class="bg-white border rounded-xl p-4 flex justify-between items-center mb-2">
        <div>
          <p class="font-semibold text-gray-800">{{ item.user.name }}</p>
          <p class="text-sm text-gray-500">{{ item.tanggal }} · {{ item.jam_mulai }} - {{ item.jam_selesai }}</p>
        </div>
        <div class="flex gap-2">
          <button @click="approve('lembur.approve', item.id)" class="px-3 py-1.5 bg-brand-accent text-white text-sm font-bold rounded-lg">Approve</button>
          <button @click="reject('lembur.approve', item.id)" class="px-3 py-1.5 bg-red-500 text-white text-sm font-bold rounded-lg">Reject</button>
        </div>
      </div>
    </div>

    <!-- Dinas -->
    <div>
      <h3 class="text-lg font-bold text-gray-800 mb-3">Dinas ({{ dinas.length }})</h3>
      <div v-if="dinas.length === 0" class="text-gray-500 text-sm">Tidak ada pengajuan dinas pending.</div>
      <div v-for="item in dinas" :key="item.id" class="bg-white border rounded-xl p-4 flex justify-between items-center mb-2">
        <div>
          <p class="font-semibold text-gray-800">{{ item.user.name }}</p>
          <p class="text-sm text-gray-500">{{ item.tujuan_alamat }} · {{ item.tanggal }}</p>
        </div>
        <div class="flex gap-2">
          <button @click="approve('dinas.approve', item.id)" class="px-3 py-1.5 bg-brand-accent text-white text-sm font-bold rounded-lg">Approve</button>
          <button @click="reject('dinas.approve', item.id)" class="px-3 py-1.5 bg-red-500 text-white text-sm font-bold rounded-lg">Reject</button>
        </div>
      </div>
    </div>
  </div>
</template>