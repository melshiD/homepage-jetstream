<script setup>
import { Head } from '@inertiajs/vue3';
import BuildDB from '@/Components/BuildDB.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    policy: String,
});
</script>

<template>
    <Head title="Privacy Policy" />
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Build
            </h2>
        </template>
        <div class="font-sans text-gray-900 dark:text-gray-100 antialiased">
            <div class="pt-4 bg-gray-100 dark:bg-gray-900">
                <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
                    <div>
                        <BuildDB />
                    </div>

                    <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg prose dark:prose-invert"
                    v-html="policy" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>