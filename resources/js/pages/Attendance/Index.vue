<script setup>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    today: Object,
    riwayat: Array,
});

const loading = ref(false);
const errorLokasi = ref('');

function getLocationAndSubmit(routeName) {
    errorLokasi.value = '';
    loading.value = true;

    if (!navigator.geolocation) {
        errorLokasi.value = 'Browser tidak mendukung geolokasi.';
        loading.value = false;
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            router.post(
                route(routeName),
                {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                },
                {
                    onFinish: () => {
                        loading.value = false;
                    },
                },
            );
        },
        () => {
            errorLokasi.value = 'Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan.';
            loading.value = false;
        },
    );
}

function statusBadge(status) {
    return {
        hadir: 'bg-green-50 text-brand-accent border-green-200',
        telat: 'bg-yellow-50 text-yellow-700 border-yellow-200',
        izin: 'bg-blue-50 text-blue-600 border-blue-200',
        alpha: 'bg-red-50 text-red-600 border-red-200',
    }[status];
}
</script>

<template>
    <div class="mx-auto max-w-3xl space-y-6">
        <h2 class="text-2xl font-bold text-gray-900">Absensi</h2>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <p v-if="errorLokasi" class="mb-3 text-sm text-red-500">{{ errorLokasi }}</p>

            <div v-if="!today" class="text-center">
                <p class="mb-4 text-sm text-gray-500">Anda belum absen masuk hari ini.</p>
                <button
                    @click="getLocationAndSubmit('absensi.checkin')"
                    :disabled="loading"
                    class="rounded-xl bg-brand-primary px-6 py-3 font-bold text-white hover:bg-brand-variant disabled:opacity-50"
                >
                    {{ loading ? 'Mengambil lokasi...' : 'Absen Masuk' }}
                </button>
            </div>

            <div v-else class="text-center">
                <p class="mb-1 text-sm text-gray-500">Jam Masuk</p>
                <p class="mb-4 text-2xl font-bold text-gray-900">{{ today.jam_masuk }}</p>

                <button
                    v-if="!today.jam_keluar"
                    @click="getLocationAndSubmit('absensi.checkout')"
                    :disabled="loading"
                    class="rounded-xl bg-brand-primary px-6 py-3 font-bold text-white hover:bg-brand-variant disabled:opacity-50"
                >
                    {{ loading ? 'Mengambil lokasi...' : 'Absen Keluar' }}
                </button>
                <p v-else class="font-semibold text-brand-accent">Absensi hari ini sudah lengkap ✓</p>
            </div>
        </div>

        <div>
            <h3 class="mb-3 text-lg font-bold text-gray-800">Riwayat Bulan Ini</h3>
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b bg-gray-50 text-xs uppercase text-gray-500">
                            <th class="px-6 py-3 font-bold">Tanggal</th>
                            <th class="px-6 py-3 font-bold">Masuk</th>
                            <th class="px-6 py-3 font-bold">Keluar</th>
                            <th class="px-6 py-3 font-bold">Status</th>
                            <th class="px-6 py-3 font-bold">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <tr v-for="item in riwayat" :key="item.id" class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-semibold text-gray-800">{{ item.tanggal }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ item.jam_masuk ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ item.jam_keluar ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <span :class="statusBadge(item.status)" class="rounded border px-2 py-1 text-xs font-bold">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <a
                                    v-if="item.latitude && item.longitude"
                                    :href="`https://www.google.com/maps?q=${item.latitude},${item.longitude}`"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 text-xs font-semibold text-brand-primary hover:text-brand-variant"
                                >
                                    <i class="fas fa-map-marker-alt"></i> Lihat Lokasi
                                </a>
                                <span v-else class="text-xs text-gray-400">-</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
