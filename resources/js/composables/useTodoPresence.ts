import { useEchoPresence } from '@laravel/echo-vue';
import { computed, ref } from 'vue';
import type { Ref } from 'vue';

export type PresenceUser = {
    id: number;
    name: string;
};

type MemberView = PresenceUser & {
    todoId: number | null;
    taskId: number | null;
};

type ViewingWhisper = {
    id: number;
    todoId: number | null;
    taskId: number | null;
};

/**
 * Tracks which team members currently have a todo or a task detail open.
 *
 * Members join a shared presence channel and whisper their current view target
 * ({ todoId, taskId }). Other clients use the helpers to render "who is looking"
 * indicators next to the matching todo / task.
 */
export function useTodoPresence(
    teamId: Ref<number | undefined>,
    currentUserId: number,
) {
    const members = ref<Record<number, MemberView>>({});
    const own = ref<{ todoId: number | null; taskId: number | null }>({
        todoId: null,
        taskId: null,
    });

    const channelName = computed(() => `todos-presence.${teamId.value}`);

    const { channel } = useEchoPresence(channelName.value, [], () => {}, [
        teamId,
    ]);

    function whisperOwn(): void {
        channel().whisper('viewing', {
            id: currentUserId,
            ...own.value,
        } satisfies ViewingWhisper);
    }

    channel().here((users: PresenceUser[]) => {
        const next: Record<number, MemberView> = {};

        for (const user of users) {
            next[user.id] = {
                ...user,
                todoId: members.value[user.id]?.todoId ?? null,
                taskId: members.value[user.id]?.taskId ?? null,
            };
        }

        members.value = next;
        whisperOwn();
    });

    channel().joining((user: PresenceUser) => {
        members.value[user.id] = { ...user, todoId: null, taskId: null };
        whisperOwn();
    });

    channel().leaving((user: PresenceUser) => {
        delete members.value[user.id];
    });

    channel().listenForWhisper('viewing', (e: ViewingWhisper) => {
        const existing = members.value[e.id];

        if (existing) {
            existing.todoId = e.todoId;
            existing.taskId = e.taskId;
        }
    });

    /**
     * Announce the current view target to the rest of the team.
     */
    function setViewing(todoId: number | null, taskId: number | null): void {
        own.value = { todoId, taskId };
        const mine = members.value[currentUserId];

        if (mine) {
            mine.todoId = todoId;
            mine.taskId = taskId;
        }

        whisperOwn();
    }

    const viewersForTodo = (todoId: number): MemberView[] =>
        Object.values(members.value).filter(
            (m) => m.id !== currentUserId && m.todoId === todoId,
        );

    const viewersForTask = (taskId: number): MemberView[] =>
        Object.values(members.value).filter(
            (m) => m.id !== currentUserId && m.taskId === taskId,
        );

    return { members, setViewing, viewersForTodo, viewersForTask };
}
