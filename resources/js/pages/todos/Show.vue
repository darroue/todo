<script setup lang="ts">
import { Form, Head, Link, router, setLayoutProps, useForm, usePage } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { ArrowLeft, Check, GripVertical, MessageSquare, Paperclip, Plus, Trash2, X } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';
import { useI18n } from '@/composables/useTranslation';
import { VueDraggable as VueDraggablePlus } from 'vue-draggable-plus';
import Heading from '@/components/Heading.vue';
import ImageLightbox from '@/components/ImageLightbox.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index, show } from '@/routes/todos';
import { store as storeAttachment, destroy as destroyAttachment } from '@/routes/todos/tasks/attachments';
import { store as storeComment, destroy as destroyComment } from '@/routes/todos/tasks/comments';
import { destroy as destroyTask, reorder as reorderTasks, store as storeTask, update as updateTask } from '@/routes/todos/tasks';
import type { Task, TaskAttachment, TaskComment, Team, TodoDetail } from '@/types';

type UploadQueueItem = {
    id: string;
    file: File;
    task: Task;
    status: 'queued' | 'uploading';
    abortController?: AbortController;
};

type Props = {
    todo: TodoDetail;
    tasks: Task[];
};

const props = defineProps<Props>();

const { t } = useI18n();
const page = usePage();
const currentTeam = page.props.currentTeam as Team | null;

setLayoutProps({
    breadcrumbs: [
        {
            title: t('todos.title'),
            href: currentTeam ? index(currentTeam.slug).url : '/',
        },
        {
            title: props.todo.title,
            href: currentTeam
                ? show({ current_team: currentTeam.slug, todo: props.todo.id }).url
                : '/',
        },
    ],
});

const currentTeamSlug = () => page.props.currentTeam!.slug;
const taskArgs = () => ({ current_team: currentTeamSlug(), todo: props.todo.id });

const newTaskKey = ref(0);

const teamId = computed(() => page.props.currentTeam?.id);

const editingTaskId = ref<number | null>(null);
const editTitle = ref('');
const editDescription = ref('');
const editTitleEl = ref<HTMLInputElement[]>([]);

const uploadQueue = ref<UploadQueueItem[]>([]);

const taskList = ref<Task[]>([]);
watch(() => props.tasks, (val) => { taskList.value = [...val]; }, { immediate: true });

const expandedCommentsTaskId = ref<number | null>(null);

const commentForms = ref<Record<number, ReturnType<typeof useForm<{ body: string }>>>>({});

function getCommentForm(taskId: number) {
    if (!commentForms.value[taskId]) {
        commentForms.value[taskId] = useForm({ body: '' });
    }
    return commentForms.value[taskId];
}

function toggleComments(task: Task) {
    expandedCommentsTaskId.value = expandedCommentsTaskId.value === task.id ? null : task.id;
}

function submitComment(task: Task) {
    const form = getCommentForm(task.id);
    if (!form.body.trim()) {
        return;
    }
    form.post(storeComment({ current_team: currentTeamSlug(), todo: props.todo.id, task: task.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
}

function startEdit(task: Task) {
    if (task.isCompleted) {
        return;
    }
    editingTaskId.value = task.id;
    editTitle.value = task.title;
    editDescription.value = task.description ?? '';
    nextTick(() => editTitleEl.value[0]?.focus());
}

function cancelEdit() {
    editingTaskId.value = null;
}

function saveEdit(task: Task) {
    if (editingTaskId.value !== task.id) {
        return;
    }

    const form = useForm({
        title: editTitle.value.trim() || task.title,
        description: editDescription.value.trim() || null,
    });

    form.patch(updateTask({ ...taskArgs(), task: task.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            editingTaskId.value = null;
        },
    });
}

function onDragEnd() {
    router.patch(reorderTasks(taskArgs()).url, {
        ids: taskList.value.map((t) => t.id),
    }, { preserveScroll: true });
}

function taskRouteArgs(task: Task) {
    return { current_team: currentTeamSlug(), todo: props.todo.id, task: task.id };
}

function attachmentRouteArgs(task: Task, attachment: TaskAttachment) {
    return { current_team: currentTeamSlug(), todo: props.todo.id, task: task.id, attachment: attachment.id };
}

function commentRouteArgs(task: Task, comment: TaskComment) {
    return { current_team: currentTeamSlug(), todo: props.todo.id, task: task.id, comment: comment.id };
}

function uploadAttachment(task: Task, event: Event) {
    const files = (event.target as HTMLInputElement).files;

    if (!files || files.length === 0) {
        return;
    }

    for (const file of Array.from(files)) {
        uploadQueue.value.push({
            id: crypto.randomUUID(),
            file,
            task,
            status: 'queued',
        });
    }

    (event.target as HTMLInputElement).value = '';
}

function cancelUpload(item: UploadQueueItem) {
    if (item.status === 'uploading' && item.abortController) {
        item.abortController.abort();
    }
    uploadQueue.value = uploadQueue.value.filter((q) => q.id !== item.id);
}

async function processQueue() {
    const isUploading = uploadQueue.value.some((q) => q.status === 'uploading');
    if (isUploading) {
        return;
    }

    const next = uploadQueue.value.find((q) => q.status === 'queued');
    if (!next) {
        return;
    }

    const abortController = new AbortController();
    next.status = 'uploading';
    next.abortController = abortController;

    const url = storeAttachment(taskRouteArgs(next.task)).url;
    const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

    const formData = new FormData();
    formData.append('file', next.file);

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: formData,
            signal: abortController.signal,
        });

        if (response.ok) {
            uploadQueue.value = uploadQueue.value.filter((q) => q.id !== next.id);
            router.reload({ only: ['tasks'] });
        } else {
            uploadQueue.value = uploadQueue.value.filter((q) => q.id !== next.id);
        }
    } catch (error) {
        if ((error as Error).name !== 'AbortError') {
            uploadQueue.value = uploadQueue.value.filter((q) => q.id !== next.id);
        }
    }
}

watch(uploadQueue, processQueue, { deep: true });

function queueItemsForTask(task: Task): UploadQueueItem[] {
    return uploadQueue.value.filter((q) => q.task.id === task.id);
}

const lightboxOpen = ref(false);
const lightboxIndex = ref(0);
const lightboxImages = ref<{ url: string; filename: string }[]>([]);

function openLightbox(task: Task, attachment: TaskAttachment) {
    const images = task.attachments.filter((a) => a.isImage);
    lightboxImages.value = images.map((a) => ({ url: a.url, filename: a.filename }));
    lightboxIndex.value = images.findIndex((a) => a.id === attachment.id);
    lightboxOpen.value = true;
}

const currentUserId = computed(() => (page.props.auth as { user: { id: number } } | null)?.user?.id);

useEcho<{ todoId: number }>(
    `team.${teamId.value}`,
    'Todos\\TaskChanged',
    (e) => {
        if (e.todoId === props.todo.id) {
            router.reload({ only: ['tasks'] });
        }
    },
    [teamId],
);

useEcho<{ todoId: number; taskId: number; action: string; comment: TaskComment }>(
    `team.${teamId.value}`,
    'Todos\\TaskCommentChanged',
    (e) => {
        if (e.todoId !== props.todo.id) {
            return;
        }
        const task = taskList.value.find((t) => t.id === e.taskId);
        if (!task) {
            return;
        }
        if (e.action === 'created') {
            task.comments.push(e.comment);
        } else if (e.action === 'deleted') {
            task.comments = task.comments.filter((c) => c.id !== e.comment.id);
        }
    },
    [teamId],
);
</script>

<template>
    <Head :title="todo.title" />

    <div class="flex flex-col space-y-6 px-4 py-6">
        <div class="flex items-center gap-4">
            <Button variant="ghost" size="sm" as-child>
                <Link :href="index(currentTeamSlug()).url">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
            </Button>
            <Heading variant="small" :title="todo.title" :description="t('todos.show.description')" />
        </div>

        <!-- Add task form -->
        <Form
            :key="newTaskKey"
            v-bind="storeTask.form(taskArgs())"
            class="flex flex-col gap-3 rounded-lg border p-4"
            v-slot="{ errors, processing }"
            @success="newTaskKey++"
        >
            <p class="text-sm font-medium">{{ t('todos.show.add_heading') }}</p>
            <div class="flex flex-col gap-1">
                <Label for="task-title">{{ t('todos.show.task_title') }}</Label>
                <Input
                    id="task-title"
                    name="title"
                    :placeholder="t('todos.show.task_title_placeholder')"
                    required
                    data-test="task-title-input"
                />
                <InputError :message="errors.title" />
            </div>
            <div class="flex flex-col gap-1">
                <Label for="task-description">{{ t('todos.show.task_description') }}</Label>
                <textarea
                    id="task-description"
                    name="description"
                    :placeholder="t('todos.show.task_description_placeholder')"
                    rows="2"
                    class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    data-test="task-description-input"
                />
                <InputError :message="errors.description" />
            </div>
            <div>
                <Button type="submit" :disabled="processing" data-test="task-create-button">
                    <Plus /> {{ t('todos.show.add_task') }}
                </Button>
            </div>
        </Form>

        <!-- Task list -->
        <VueDraggablePlus
            v-model="taskList"
            handle=".drag-handle"
            :animation="150"
            class="space-y-2"
            @end="onDragEnd"
        >
            <div
                v-for="task in taskList"
                :key="task.id"
                data-test="task-row"
                class="rounded-lg border p-4"
                :class="task.isCompleted ? 'opacity-60' : ''"
            >
            <div class="flex items-start justify-between">
                <div class="flex flex-1 min-w-0 items-start gap-3">
                    <GripVertical class="drag-handle mt-0.5 h-4 w-4 shrink-0 cursor-grab text-muted-foreground/40 active:cursor-grabbing" />

                    <Form
                        v-bind="updateTask.form(taskRouteArgs(task))"
                        class="mt-0.5"
                    >
                        <input type="hidden" name="isCompleted" :value="task.isCompleted ? '0' : '1'" />
                        <button type="submit" class="cursor-pointer" :data-test="`task-toggle-${task.id}`">
                            <div
                                class="flex h-5 w-5 items-center justify-center rounded border-2"
                                :class="task.isCompleted ? 'border-primary bg-primary text-primary-foreground' : 'border-muted-foreground'"
                            >
                                <Check v-if="task.isCompleted" class="h-3 w-3" />
                            </div>
                        </button>
                    </Form>

                    <div class="flex-1">
                        <template v-if="editingTaskId === task.id && !task.isCompleted">
                            <input
                                ref="editTitleEl"
                                v-model="editTitle"
                                class="w-full rounded border border-input bg-transparent px-2 py-0.5 text-sm font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                :data-test="`task-edit-title-${task.id}`"
                                @keydown.enter.prevent="saveEdit(task)"
                                @keydown.escape="cancelEdit"
                                @blur="saveEdit(task)"
                            />
                            <textarea
                                v-model="editDescription"
                                rows="2"
                                class="mt-1 w-full rounded border border-input bg-transparent px-2 py-0.5 text-sm text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                :data-test="`task-edit-description-${task.id}`"
                                @keydown.escape="cancelEdit"
                                @blur="saveEdit(task)"
                            />
                        </template>
                        <template v-else>
                            <p
                                class="cursor-text font-medium"
                                :class="task.isCompleted ? 'line-through' : ''"
                                :data-test="`task-title-${task.id}`"
                                @dblclick="startEdit(task)"
                            >
                                {{ task.title }}
                            </p>
                            <p
                                v-if="task.description"
                                class="mt-1 cursor-text text-sm text-muted-foreground"
                                :data-test="`task-description-${task.id}`"
                                @dblclick="startEdit(task)"
                            >
                                {{ task.description }}
                            </p>
                            <p
                                v-else
                                class="mt-1 cursor-text text-sm text-muted-foreground/40 italic"
                                @dblclick="startEdit(task)"
                            >
                                {{ t('todos.show.add_description_hint') }}
                            </p>
                        </template>
                    </div>
                </div>

                <div v-if="editingTaskId !== task.id" class="flex shrink-0 items-start gap-1">
                    <button
                        type="button"
                        class="inline-flex h-8 items-center justify-center gap-1 rounded-md px-2 text-muted-foreground hover:bg-accent hover:text-accent-foreground"
                        :class="expandedCommentsTaskId === task.id ? 'text-foreground' : ''"
                        :data-test="`task-comments-toggle-${task.id}`"
                        @click="toggleComments(task)"
                    >
                        <MessageSquare class="h-4 w-4" />
                        <span v-if="task.comments.length > 0" class="text-xs">{{ task.comments.length }}</span>
                    </button>
                    <label
                        v-if="!task.isCompleted"
                        :for="`attachment-${task.id}`"
                        class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-md text-muted-foreground hover:bg-accent hover:text-accent-foreground"
                        :title="t('todos.show.upload')"
                    >
                        <Paperclip class="h-4 w-4" />
                        <input
                            :id="`attachment-${task.id}`"
                            type="file"
                            multiple
                            class="sr-only"
                            :data-test="`task-attachment-input-${task.id}`"
                            @change="uploadAttachment(task, $event)"
                        />
                    </label>
                    <Form v-bind="destroyTask.form(taskRouteArgs(task))">
                        <Button
                            type="submit"
                            variant="ghost"
                            size="sm"
                            class="cursor-pointer"
                            :data-test="`task-delete-${task.id}`"
                        >
                            <Trash2 class="h-4 w-4 text-destructive" />
                        </Button>
                    </Form>
                </div>
            </div>

            <!-- Attachments -->
            <div v-if="task.attachments.length > 0 || queueItemsForTask(task).length > 0" class="mt-3 flex flex-wrap gap-2 pl-8">
                <div
                    v-for="attachment in task.attachments"
                    :key="attachment.id"
                    class="group relative"
                    :data-test="`task-attachment-${attachment.id}`"
                >
                    <button
                        v-if="attachment.isImage"
                        type="button"
                        class="block cursor-pointer"
                        :data-test="`task-attachment-open-${attachment.id}`"
                        @click="openLightbox(task, attachment)"
                    >
                        <img
                            :src="attachment.url"
                            :alt="attachment.filename"
                            class="h-20 w-20 rounded-md border object-cover transition-opacity group-hover:opacity-75"
                        />
                    </button>
                    <a
                        v-else
                        :href="attachment.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex h-20 w-20 flex-col items-center justify-center gap-1 rounded-md border bg-muted text-center transition-colors group-hover:bg-muted/70"
                    >
                        <span class="text-xs font-bold uppercase text-muted-foreground">
                            {{ attachment.extension }}
                        </span>
                        <span class="line-clamp-2 break-all px-1 text-[10px] text-muted-foreground">
                            {{ attachment.filename }}
                        </span>
                    </a>
                    <Form
                        v-if="!task.isCompleted"
                        v-bind="destroyAttachment.form(attachmentRouteArgs(task, attachment))"
                        class="absolute -right-1.5 -top-1.5 hidden group-hover:block"
                    >
                        <button
                            type="submit"
                            class="flex h-4 w-4 cursor-pointer items-center justify-center rounded-full bg-destructive text-destructive-foreground"
                            :data-test="`task-attachment-delete-${attachment.id}`"
                        >
                            <X class="h-2.5 w-2.5" />
                        </button>
                    </Form>
                </div>

                <!-- Upload queue items -->
                <div
                    v-for="item in queueItemsForTask(task)"
                    :key="item.id"
                    class="relative flex h-20 w-20 flex-col items-center justify-center gap-1 rounded-md border bg-muted text-center"
                    :class="item.status === 'uploading' ? 'animate-pulse' : ''"
                >
                    <span class="line-clamp-2 break-all px-1 text-[10px] text-muted-foreground">
                        {{ item.file.name }}
                    </span>
                    <span
                        class="rounded px-1 py-0.5 text-[9px] font-medium"
                        :class="item.status === 'uploading' ? 'bg-blue-100 text-blue-700' : 'bg-muted-foreground/20 text-muted-foreground'"
                    >
                        {{ item.status === 'uploading' ? t('todos.show.uploading') : t('todos.show.queued') }}
                    </span>
                    <button
                        type="button"
                        class="absolute -right-1.5 -top-1.5 flex h-4 w-4 cursor-pointer items-center justify-center rounded-full bg-destructive text-destructive-foreground"
                        @click="cancelUpload(item)"
                    >
                        <X class="h-2.5 w-2.5" />
                    </button>
                </div>
            </div>

            <!-- Comments section -->
            <div v-if="expandedCommentsTaskId === task.id" class="mt-3 border-t pt-3 pl-8">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                    {{ t('todos.show.comments_heading') }}
                </p>

                <div v-if="task.comments.length === 0" class="mb-2 text-sm text-muted-foreground/60 italic">
                    {{ t('todos.show.no_comments') }}
                </div>

                <div v-else class="mb-3 space-y-2">
                    <div
                        v-for="comment in task.comments"
                        :key="comment.id"
                        class="group flex items-start gap-2"
                        :data-test="`task-comment-${comment.id}`"
                    >
                        <div class="flex-1 rounded-md bg-muted px-3 py-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-medium">{{ comment.user.name }}</span>
                                <span class="text-[10px] text-muted-foreground">
                                    {{ new Date(comment.createdAt).toLocaleString() }}
                                </span>
                            </div>
                            <p class="mt-0.5 text-sm">{{ comment.body }}</p>
                        </div>
                        <Form
                            v-if="comment.user.id === currentUserId && !task.isCompleted"
                            v-bind="destroyComment.form(commentRouteArgs(task, comment))"
                            class="opacity-0 group-hover:opacity-100 transition-opacity"
                        >
                            <button
                                type="submit"
                                class="flex h-6 w-6 cursor-pointer items-center justify-center rounded text-muted-foreground hover:text-destructive"
                                :data-test="`task-comment-delete-${comment.id}`"
                            >
                                <X class="h-3 w-3" />
                            </button>
                        </Form>
                    </div>
                </div>

                <!-- New comment form -->
                <div v-if="!task.isCompleted" class="flex gap-2">
                    <textarea
                        v-model="getCommentForm(task.id).body"
                        :placeholder="t('todos.show.comment_placeholder')"
                        rows="1"
                        class="flex-1 rounded-md border border-input bg-transparent px-3 py-1.5 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        :data-test="`task-comment-input-${task.id}`"
                        @keydown.enter.prevent="submitComment(task)"
                    />
                    <Button
                        size="sm"
                        :disabled="getCommentForm(task.id).processing || !getCommentForm(task.id).body.trim()"
                        :data-test="`task-comment-submit-${task.id}`"
                        @click="submitComment(task)"
                    >
                        {{ t('todos.show.add_comment') }}
                    </Button>
                </div>
            </div>
            </div>
        </VueDraggablePlus>

        <p
            v-if="tasks.length === 0"
            class="py-8 text-center text-muted-foreground"
        >
            {{ t('todos.show.empty') }}
        </p>

        <ImageLightbox
            v-model:open="lightboxOpen"
            v-model:index="lightboxIndex"
            :images="lightboxImages"
        />
    </div>
</template>
