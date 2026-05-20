<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { Eye, Pencil, Plus } from 'lucide-vue-next';
import { useI18n } from '@/composables/useTranslation';
import CreateTeamModal from '@/components/CreateTeamModal.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { edit, index } from '@/routes/teams';
import type { Team } from '@/types';

type Props = {
    teams: Team[];
};

defineProps<Props>();

const { t } = useI18n();

setLayoutProps({
    breadcrumbs: [
        {
            title: t('teams.title'),
            href: index(),
        },
    ],
});
</script>

<template>
    <Head :title="t('teams.title')" />

    <h1 class="sr-only">{{ t('teams.title') }}</h1>

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                :title="t('teams.title')"
                :description="t('teams.description')"
            />

            <CreateTeamModal>
                <Button data-test="teams-new-team-button">
                    <Plus /> {{ t('teams.new_team') }}
                </Button>
            </CreateTeamModal>
        </div>

        <div class="space-y-3">
            <div
                v-for="team in teams"
                :key="team.id"
                data-test="team-row"
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <div class="flex items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium">{{ team.name }}</span>
                            <Badge v-if="team.isPersonal" variant="secondary">
                                {{ t('teams.personal') }}
                            </Badge>
                        </div>
                        <span class="text-sm text-muted-foreground">
                            {{ team.roleLabel }}
                        </span>
                    </div>
                </div>

                <TooltipProvider>
                    <div class="flex items-center gap-2">
                        <Tooltip v-if="team.role === 'member'">
                            <TooltipTrigger as-child>
                                <Button
                                    data-test="team-view-button"
                                    variant="ghost"
                                    size="sm"
                                    as-child
                                >
                                    <Link :href="edit(team.slug)">
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{{ t('teams.view') }}</p>
                            </TooltipContent>
                        </Tooltip>

                        <Tooltip v-else>
                            <TooltipTrigger as-child>
                                <Button
                                    data-test="team-edit-button"
                                    variant="ghost"
                                    size="sm"
                                    as-child
                                >
                                    <Link :href="edit(team.slug)">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{{ t('teams.edit') }}</p>
                            </TooltipContent>
                        </Tooltip>
                    </div>
                </TooltipProvider>
            </div>

            <p
                v-if="teams.length === 0"
                class="py-8 text-center text-muted-foreground"
            >
                {{ t('teams.empty') }}
            </p>
        </div>
    </div>
</template>
