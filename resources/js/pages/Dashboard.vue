<script setup lang="ts">
import { Head, Link, setLayoutProps, usePage } from '@inertiajs/vue3';
import { CheckSquare, ListTodo, SquareCheck } from 'lucide-vue-next';
import { useI18n } from '@/composables/useTranslation';
import { dashboard } from '@/routes';
import { index } from '@/routes/todos';
import type { Team } from '@/types';

type Props = {
    stats: {
        todosCount: number;
        tasksCount: number;
        completedTasksCount: number;
    };
};

const props = defineProps<Props>();

const { t } = useI18n();
const page = usePage();

setLayoutProps({
    breadcrumbs: [
        {
            title: t('dashboard.title'),
            href: page.props.currentTeam ? dashboard((page.props.currentTeam as Team).slug).url : '/',
        },
    ],
});

const completionRate = () => {
    if (props.stats.tasksCount === 0) return 0;
    return Math.round((props.stats.completedTasksCount / props.stats.tasksCount) * 100);
};
</script>

<template>
    <Head :title="t('dashboard.title')" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <!-- Todos count -->
            <Link
                :href="index(($page.props.currentTeam as Team).slug).url"
                class="flex flex-col gap-3 rounded-xl border border-sidebar-border/70 p-6 transition-colors hover:bg-muted/50 dark:border-sidebar-border"
            >
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-muted-foreground">{{ t('dashboard.todos') }}</span>
                    <ListTodo class="h-5 w-5 text-muted-foreground" />
                </div>
                <span class="text-3xl font-bold">{{ stats.todosCount }}</span>
            </Link>

            <!-- Tasks count -->
            <div class="flex flex-col gap-3 rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-muted-foreground">{{ t('dashboard.tasks') }}</span>
                    <CheckSquare class="h-5 w-5 text-muted-foreground" />
                </div>
                <span class="text-3xl font-bold">{{ stats.tasksCount }}</span>
                <span class="text-sm text-muted-foreground">
                    {{ t('dashboard.completed', { count: stats.completedTasksCount }) }}
                </span>
            </div>

            <!-- Completion rate -->
            <div class="flex flex-col gap-3 rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-muted-foreground">{{ t('dashboard.completion_rate') }}</span>
                    <SquareCheck class="h-5 w-5 text-muted-foreground" />
                </div>
                <span class="text-3xl font-bold">{{ completionRate() }}%</span>
                <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                    <div
                        class="h-full rounded-full bg-primary transition-all"
                        :style="{ width: `${completionRate()}%` }"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
