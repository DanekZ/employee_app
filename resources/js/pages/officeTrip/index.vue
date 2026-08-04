<script setup>
    import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
    import {Link} from '@inertiajs/vue3';


    defineOptions({layout: AuthenticatedLayout});

    defineProps({
        officeTrips: Array,
    })


    function formatTanggal(tanggal) {
    return new Date(tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
    }


    function labelTransportasi(alat){
        return {
            kendaraan_dinas: 'Kendaraan Dinas',
            kendaraan_pribadi: 'Kendaraan Pribadi',
            transportasi_umum: 'Transportasi Umum'
        }[alat]
    }

    function statusBadge(status) {
        return {
            pending: 'bg-yellow-50 text-yellow-700 border-yellow-200',
            approved: 'bg-green-50 text-brand-accent border-green-200',
            rejected: 'bg-red-50 text-red-600 border-red-200',
        }[status]
    }
</script>

<template>
  <div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Riwayat Dinas</h2>
      <Link :href="route('dinas.create')" class="px-4 py-2 bg-brand-primary hover:bg-brand-variant text-white text-sm font-bold rounded-xl">
        + Ajukan Dinas
      </Link>
    </div>

    <div v-if="officeTrips.length === 0" class="text-gray-500 text-sm">
      Belum ada pengajuan dinas.
    </div>

    <div v-else class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
      <table class="w-full text-left">
        <thead>
          <tr class="bg-gray-50 text-xs text-gray-500 uppercase border-b">
            <th class="py-3 px-6 font-bold">Tanggal</th>
            <th class="py-3 px-6 font-bold">Tujuan</th>
            <th class="py-3 px-6 font-bold">Jam</th>
            <th class="py-3 px-6 font-bold">Transportasi</th>
            <th class="py-3 px-6 font-bold">Status</th>
          </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-100">
          <tr v-for="item in officeTrips" :key="item.id" class="hover:bg-gray-50">
            <td class="py-3 px-6 font-semibold text-gray-800">{{ formatTanggal(item.tanggal) }}</td>
            <td class="py-3 px-6 text-gray-600">{{ item.tujuan_alamat }}</td>
            <td class="py-3 px-6 text-gray-600">{{ item.jam_keluar }} - {{ item.jam_kembali }}</td>
            <td class="py-3 px-6 text-gray-600">{{ labelTransportasi(item.alat_transportasi) }}</td>
            <td class="py-3 px-6">
              <span :class="statusBadge(item.status)" class="px-2 py-1 rounded text-xs font-bold border">
                {{ item.status }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

