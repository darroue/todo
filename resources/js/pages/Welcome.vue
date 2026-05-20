<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { dashboard, login, register } from '@/routes';

const { t } = useI18n();

const page = usePage();
const dashboardUrl = computed(() =>
    page.props.currentTeam ? dashboard(page.props.currentTeam.slug).url : '/',
);
</script>

<template>
    <Head :title="t('welcome.title')" />
    <div class="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] px-6 dark:bg-[#0a0a0a]">
        <div class="flex w-full max-w-sm flex-col items-center gap-8 text-center">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-semibold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                    {{ t('welcome.app_name') }}
                </h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    {{ t('welcome.tagline') }}
                </p>
            </div>

            <div class="flex w-full flex-col gap-3">
                <template v-if="$page.props.auth.user">
                    <Link
                        :href="dashboardUrl"
                        class="inline-flex w-full items-center justify-center rounded-md bg-[#1b1b18] px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-black dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white"
                    >
                        {{ t('welcome.go_to_dashboard') }}
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-flex w-full items-center justify-center rounded-md bg-[#1b1b18] px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-black dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white"
                    >
                        {{ t('welcome.sign_in') }}
                    </Link>
                    <Link
                        :href="register()"
                        class="inline-flex w-full items-center justify-center rounded-md border border-[#19140035] px-5 py-2.5 text-sm font-medium text-[#1b1b18] transition-colors hover:border-[#1915014a] hover:bg-[#f5f5f3] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b] dark:hover:bg-[#161615]"
                    >
                        {{ t('welcome.sign_up') }}
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>
