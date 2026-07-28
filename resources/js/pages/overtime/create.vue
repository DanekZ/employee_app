<script setup>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: AuthenticatedLayout })

const today = new Date().toISOString().split('T')[0];

const form = useForm({
  tanggal: today,
  jam_mulai: '',
  jam_selesai: '',
  lokasi_lembur: '',
  alasan: '',
})

function submit() {
  form.post(route('lembur.store'), {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <div class="max-w-lg mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Ajukan Lembur</h2>

    <form @submit.prevent="submit" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
      <div>
        <label class="text-sm font-semibold text-gray-600">Tanggal</label>
        <input type="date" v-model="form.tanggal" class="w-full mt-1 border rounded-lg p-2" />
        <p v-if="form.errors.tanggal" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal }}</p>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-sm font-semibold text-gray-600">Jam Mulai</label>
          <input type="time" v-model="form.jam_mulai" class="w-full mt-1 border rounded-lg p-2" />
          <p v-if="form.errors.jam_mulai" class="text-red-500 text-xs mt-1">{{ form.errors.jam_mulai }}</p>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-600">Jam Selesai</label>
          <input type="time" v-model="form.jam_selesai" class="w-full mt-1 border rounded-lg p-2" />
          <p v-if="form.errors.jam_selesai" class="text-red-500 text-xs mt-1">{{ form.errors.jam_selesai }}</p>
        </div>
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-600">Lokasi Lembur</label>
        <input type="text" v-model="form.lokasi_lembur" placeholder="misal: Kantor Pusat" class="w-full mt-1 border rounded-lg p-2" />
        <p v-if="form.errors.lokasi_lembur" class="text-red-500 text-xs mt-1">{{ form.errors.lokasi_lembur }}</p>
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-600">Alasan</label>
        <textarea v-model="form.alasan" rows="4" class="w-full mt-1 border rounded-lg p-2" placeholder="Alasan lembur..."></textarea>
        <p v-if="form.errors.alasan" class="text-red-500 text-xs mt-1">{{ form.errors.alasan }}</p>
      </div>

      <button type="submit" :disabled="form.processing" class="w-full py-3 bg-brand-primary hover:bg-brand-variant text-white font-bold rounded-xl disabled:opacity-50">
        Kirim Pengajuan
      </button>
    </form>
  </div>
</template>