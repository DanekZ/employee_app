<script setup>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: AuthenticatedLayout })

const form = useForm({
  jenis: '',
  tujuan: '',
  tanggal_mulai: '',
  tanggal_selesai: '',
  durasi_menit: '',
  jam_mulai: '',
  jam_selesai: '',
  keterangan: '',
})

function submit() {
  form.post(route('izin.store'), {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <div class="max-w-lg mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Ajukan Izin</h2>

    <form @submit.prevent="submit" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
      <div>
        <label class="text-sm font-semibold text-gray-600">Jenis Izin</label>
        <select v-model="form.jenis" class="w-full mt-1 border rounded-lg p-2">
          <option value="">Pilih jenis izin</option>
          <option value="tidak_masuk">Tidak Masuk Kerja</option>
          <option value="terlambat">Datang Terlambat</option>
          <option value="pulang_awal">Pulang Lebih Awal</option>
          <option value="keluar_kantor">Izin Keluar Kantor Sementara</option>
        </select>
        <p v-if="form.errors.jenis" class="text-red-500 text-xs mt-1">{{ form.errors.jenis }}</p>
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-600">Tujuan</label>
        <input type="text" v-model="form.tujuan" placeholder="ke samarinda" class="w-full mt-1 border rounded-lg p-2" />
        <p v-if="form.errors.tujuan" class="text-red-500 text-xs mt-1">{{ form.errors.tujuan }}</p>
      </div>

      <!-- Tanggal mulai (selalu muncul) -->
      <div>
        <label class="text-sm font-semibold text-gray-600">
          {{ form.jenis === 'tidak_masuk' ? 'Tanggal Mulai' : 'Tanggal' }}
        </label>
        <input type="date" v-model="form.tanggal_mulai" class="w-full mt-1 border rounded-lg p-2" />
        <p v-if="form.errors.tanggal_mulai" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal_mulai }}</p>
      </div>

      <!-- Khusus: Tidak Masuk Kerja -->
      <div v-if="form.jenis === 'tidak_masuk'">
        <label class="text-sm font-semibold text-gray-600">Tanggal Selesai</label>
        <input type="date" v-model="form.tanggal_selesai" class="w-full mt-1 border rounded-lg p-2" />
        <p v-if="form.errors.tanggal_selesai" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal_selesai }}</p>
      </div>

      <!-- Khusus: Terlambat / Pulang Awal -->
      <div v-if="form.jenis === 'terlambat' || form.jenis === 'pulang_awal'">
        <label class="text-sm font-semibold text-gray-600">Durasi (menit)</label>
        <input type="number" v-model="form.durasi_menit" placeholder="misal: 30" class="w-full mt-1 border rounded-lg p-2" />
        <p v-if="form.errors.durasi_menit" class="text-red-500 text-xs mt-1">{{ form.errors.durasi_menit }}</p>
      </div>

      <!-- Khusus: Izin Keluar Kantor -->
      <div v-if="form.jenis === 'keluar_kantor'" class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-sm font-semibold text-gray-600">Jam Keluar</label>
          <input type="time" v-model="form.jam_mulai" class="w-full mt-1 border rounded-lg p-2" />
          <p v-if="form.errors.jam_mulai" class="text-red-500 text-xs mt-1">{{ form.errors.jam_mulai }}</p>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-600">Jam Kembali</label>
          <input type="time" v-model="form.jam_selesai" class="w-full mt-1 border rounded-lg p-2" />
          <p v-if="form.errors.jam_selesai" class="text-red-500 text-xs mt-1">{{ form.errors.jam_selesai }}</p>
        </div>
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-600">Keterangan</label>
        <textarea v-model="form.keterangan" rows="4" class="w-full mt-1 border rounded-lg p-2" placeholder="Alasan izin..."></textarea>
        <p v-if="form.errors.keterangan" class="text-red-500 text-xs mt-1">{{ form.errors.keterangan }}</p>
      </div>

      <button type="submit" :disabled="form.processing" class="w-full py-3 bg-brand-primary hover:bg-brand-variant text-white font-bold rounded-xl disabled:opacity-50">
        Kirim Pengajuan
      </button>
    </form>
  </div>
</template>