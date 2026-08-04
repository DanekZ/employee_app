<script setup>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: AuthenticatedLayout })

const form = useForm({
  tanggal: '',
  tujuan_alamat: '',
  jam_keluar: '',
  jam_kembali: '',
  alat_transportasi: '',
  alasan: '',
})

function submit() {
  form.post(route('dinas.store'), {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <div class="max-w-lg mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Ajukan Dinas</h2>

    <form @submit.prevent="submit" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
      <div>
        <label class="text-sm font-semibold text-gray-600">Tanggal</label>
        <input type="date" v-model="form.tanggal" class="w-full mt-1 border rounded-lg p-2" />
        <p v-if="form.errors.tanggal" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal }}</p>
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-600">Tujuan / Alamat</label>
        <input type="text" v-model="form.tujuan_alamat" placeholder="misal: Kantor Dinas Kependudukan" class="w-full mt-1 border rounded-lg p-2" />
        <p v-if="form.errors.tujuan_alamat" class="text-red-500 text-xs mt-1">{{ form.errors.tujuan_alamat }}</p>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-sm font-semibold text-gray-600">Jam Keluar</label>
          <input type="time" v-model="form.jam_keluar" class="w-full mt-1 border rounded-lg p-2" />
          <p v-if="form.errors.jam_keluar" class="text-red-500 text-xs mt-1">{{ form.errors.jam_keluar }}</p>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-600">Jam Kembali</label>
          <input type="time" v-model="form.jam_kembali" class="w-full mt-1 border rounded-lg p-2" />
          <p v-if="form.errors.jam_kembali" class="text-red-500 text-xs mt-1">{{ form.errors.jam_kembali }}</p>
        </div>
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-600">Alat Transportasi</label>
        <select v-model="form.alat_transportasi" class="w-full mt-1 border rounded-lg p-2">
          <option value="">Pilih alat transportasi</option>
          <option value="kendaraan_dinas">Kendaraan Dinas</option>
          <option value="kendaraan_pribadi">Kendaraan Pribadi</option>
          <option value="transportasi_umum">Transportasi Umum</option>
        </select>
        <p v-if="form.errors.alat_transportasi" class="text-red-500 text-xs mt-1">{{ form.errors.alat_transportasi }}</p>
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-600">Alasan</label>
        <textarea v-model="form.alasan" rows="4" class="w-full mt-1 border rounded-lg p-2" placeholder="Alasan dinas..."></textarea>
        <p v-if="form.errors.alasan" class="text-red-500 text-xs mt-1">{{ form.errors.alasan }}</p>
      </div>

      <button type="submit" :disabled="form.processing" class="w-full py-3 bg-brand-primary hover:bg-brand-variant text-white font-bold rounded-xl disabled:opacity-50">
        Kirim Pengajuan
      </button>
    </form>
  </div>
</template>