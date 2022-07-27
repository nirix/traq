<script setup lang="ts">
import { onMounted, ref, computed, reactive } from "vue"
import { useRoute } from "vue-router"
import axios from "axios"
import DOMPurify from "dompurify"
import { marked } from "marked"
import { DateTime } from "luxon"
import { faPencil, faTrashCan, faAnglesRight, faEye, faEyeSlash, faCircleNotch } from "@fortawesome/free-solid-svg-icons"
import { faSquare, faSquareCheck } from "@fortawesome/free-regular-svg-icons"
import { useAuthStore } from "../stores/auth"
import EasyMDE from "../components/EasyMDE.vue"
import ConfirmDialog from "../components/ConfirmDialog.vue"
import SkeletonLoader from "../components/SkeletonLoader.vue"

const auth = useAuthStore()
const route = useRoute()

const projectSlug = ref()
const ticketId = ref()
const editing = ref<boolean>(false)
const saving = ref<boolean>(false)
const ticket = ref()
const ticketDescription = ref<string>()
const savingTasks = reactive({})

// Computed
const formattedDescription = computed(() => DOMPurify.sanitize(marked.parse(ticket.value.body)))
const formattedCreatedAtDate = computed(() =>
  ticket.value.created_at ? DateTime.fromSQL(ticket.value.created_at, { zone: "UTC" }).toLocal().toRelative() : "-"
)
const formattedUpdatedAtDate = computed(() =>
  ticket.value.created_at ? DateTime.fromSQL(ticket.value.updated_at, { zone: "UTC" }).toLocal().toRelative() : "-"
)

onMounted((): void => {
  projectSlug.value = route.params.project
  ticketId.value = route.params.ticket

  const ticketUrl = `${window.traq.base}${projectSlug.value}/tickets/${ticketId.value}.json`
  axios.get(ticketUrl).then((resp) => {
    ticket.value = resp.data
    ticketDescription.value = resp.data.body
  })
})

const saveDescription = (): void => {
  saving.value = true

  const updateUrl = `${window.traq.base}${projectSlug.value}/tickets/${ticketId.value}/edit`
  const formData = new FormData()
  formData.append("body", ticketDescription.value)

  axios.post(updateUrl, formData).then(() => {
    saving.value = false
    editing.value = false
    ticket.value.body = ticketDescription.value
  })
}

const cancelDescriptionEdit = (): void => {
  ticketDescription.value = ticket.value.body
  editing.value = false
}

const toggleTask = (index): void => {
  if (!auth.can("ticket_properties_complete_tasks")) {
    return
  }

  savingTasks[index] = true

  const url = `${window.traq.base}${projectSlug.value}/tickets/${ticketId.value}/tasks/${index}`
  axios
    .post(url)
    .then((resp) => {
      if (resp.data.success) {
        ticket.value.tasks[index].completed = !ticket.value.tasks[index].completed
      }
    })
    .finally(() => {
      savingTasks[index] = false
    })
}

const savingTask = (index): boolean => {
  return savingTasks[index] ?? false
}
</script>

<template>
  <div class="view-ticket" v-if="ticket">
    <div class="ticket-info">
      <div class="ticket-header">
        <div class="ticket-summary">#{{ ticket.ticket_id }} - {{ ticket.summary }}</div>
        <div class="ticket-actions">
          <div class="btn-group">
            <button class="btn-info btn-sm" title="Subscribe to updates" v-if="auth.isAuthenticated">
              <fa-icon :icon="faEye" />
              <span class="visually-hidden">Subscribe</span>
            </button>
            <button class="btn-warning btn-sm" title="Move ticket" v-if="auth.can('move_tickets')">
              <fa-icon :icon="faAnglesRight" />
              <span class="visually-hidden">Move</span>
            </button>
            <ConfirmDialog
              v-if="auth.can('delete_tickets')"
              btn-class="btn-danger btn-sm !rounded-r"
              btn-title="Delete ticket"
              btn-label="Delete"
              :btn-icon="faTrashCan"
              btn-label-class="visually-hidden"
              :message="`Really delete '${ticket.summary}'?`"
            />
          </div>
        </div>
      </div>
      <div class="ticket-properties">
        <div class="ticket-property">
          <strong>Type</strong>
          <span>{{ ticket.type.name }}</span>
        </div>

        <div class="ticket-property">
          <strong>Status</strong>
          <span>{{ ticket.status.name }}</span>
        </div>

        <div class="ticket-property">
          <strong>Milestone</strong>
          <span>{{ ticket.milestone.name }}</span>
        </div>

        <div class="ticket-property">
          <strong>Version</strong>
          <span>{{ ticket.version?.name ?? "-" }}</span>
        </div>

        <div class="ticket-property">
          <strong>Priority</strong>
          <span>{{ ticket.priority.name }}</span>
        </div>

        <div class="ticket-property">
          <strong>Reported by</strong>
          <span>{{ ticket.user.name }}</span>
        </div>

        <div class="ticket-property">
          <strong>Assigned to</strong>
          <span>{{ ticket.assigned_to?.name ?? "-" }}</span>
        </div>

        <div class="ticket-property">
          <strong>Created</strong>
          <span>{{ formattedCreatedAtDate }}</span>
        </div>

        <div class="ticket-property">
          <strong>Updated</strong>
          <span>{{ formattedUpdatedAtDate }}</span>
        </div>
      </div>
      <div class="ticket-description">
        <template v-if="editing">
          <EasyMDE v-model="ticketDescription" />
          <div class="ticket-edit-actions">
            <button class="btn-success" @click="saveDescription" :disabled="saving">
              <template v-if="!saving"> Save </template>
              <template v-if="saving">
                <fa-icon :icon="faCircleNotch" class="fa-spin" />
              </template>
            </button>
            <button class="btn-warning" @click="cancelDescriptionEdit" :disabled="saving">Cancel</button>
          </div>
        </template>
        <template v-else>
          <button
            class="btn-warning btn-sm ticket-edit-btn"
            title="Edit description"
            v-if="auth.can('edit_ticket_description')"
            @click="() => (editing = true)"
          >
            <fa-icon :icon="faPencil" />
          </button>
          <div v-html="formattedDescription"></div>
        </template>
      </div>
      <div class="ticket-tasks" v-if="ticket.tasks.length > 0">
        <h3>Tasks</h3>
        <ul>
          <template v-for="(task, index) in ticket.tasks" :key="index">
            <li class="ticket-task" @click="toggleTask(index)">
              <template v-if="savingTask(index)">
                <fa-icon :icon="faCircleNotch" class="fa-fw fa-spin" />
              </template>
              <template v-else>
                <template v-if="task.completed">
                  <fa-icon :icon="faSquareCheck" class="fa-fw" />
                </template>
                <template v-else>
                  <fa-icon :icon="faSquare" class="fa-fw" />
                </template>
              </template>
              <span>{{ task.task }}</span>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </div>
  <div v-else>
    <SkeletonLoader height="250px" class="my-4" />
  </div>
</template>

<style scoped lang="postcss">
.view-ticket {
  @apply text-base;
  @apply pt-5;
}

.ticket-info {
  display: flex;
  flex-direction: column;

  @apply text-gold-900;
  @apply bg-gold-300;
  @apply border border-solid border-gold-600 rounded;
  @apply mb-5;
  @apply p-4;
}

.ticket-header {
  display: flex;
  align-items: center;
  @apply border-b border-solid border-gold-600;

  & > .ticket-summary {
    flex-grow: 1;
    @apply text-2xl;
  }
}

.ticket-properties {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  @apply border-b border-solid border-gold-600 rounded;
  @apply py-2;

  & > .ticket-property {
    max-width: 235px;
    min-width: 235px;
    @apply mb-1 mr-2;

    & > * {
      @apply text-sm;
    }

    & > strong {
      display: inline-block;
      @apply mr-2;
      @apply font-semibold;
    }

    & > ul {
      display: inline-block;
      margin: 0;
      padding: 0;

      & > li {
        display: inline;
        margin: 0;
        padding: 0;
      }
    }
  }
}

.ticket-description {
  @apply py-4;

  & > .ticket-edit-btn {
    float: right;
    @apply text-sm;
  }
}

.ticket-edit-actions {
  @apply mt-2;

  & > button {
    @apply mr-2;
  }
}

.ticket-tasks {
  & > h3 {
    @apply text-base;
    @apply pt-2;
    @apply border-t border-solid border-gold-600 rounded;
  }

  & > ul {
    @apply m-0 p-0;
    @apply list-none;
    @apply inline-block;

    & > li {
      @apply m-0 p-0;
    }
  }

  & .ticket-task {
    @apply cursor-pointer;

    & > span:last-child {
      @apply ml-2;
    }
  }
}
</style>
