<script setup lang="ts">
import { onMounted, ref, computed } from "vue"
import { useRoute } from "vue-router"
import axios from "axios"
import DOMPurify from "dompurify"
import { marked } from "marked"
import { DateTime } from "luxon"
import { faPencil, faTrashCan, faAnglesRight, faEnvelopeCircleCheck } from "@fortawesome/free-solid-svg-icons"
import { useAuthStore } from "../stores/auth"
import EasyMDE from "../components/EasyMDE.vue"
import ConfirmDialog from "../components/ConfirmDialog.vue"
import SkeletonLoader from "../components/SkeletonLoader.vue"

const auth = useAuthStore()
const route = useRoute()

const editing = ref<boolean>(false)
const ticket = ref()
const ticketDescription = ref<string>()

// Computed
const formattedDescription = computed(() => DOMPurify.sanitize(marked.parse(ticket.value.body)))
const formattedCreatedAtDate = computed(() =>
  ticket.value.created_at ? DateTime.fromSQL(ticket.value.created_at, { zone: "UTC" }).toLocal().toRelative() : "-"
)
const formattedUpdatedAtDate = computed(() =>
  ticket.value.created_at ? DateTime.fromSQL(ticket.value.updated_at, { zone: "UTC" }).toLocal().toRelative() : "-"
)

onMounted((): void => {
  const { project: projectSlug, ticket: ticketId } = route.params

  const ticketUrl = `${window.traq.base}${projectSlug}/tickets/${ticketId}.json`
  axios.get(ticketUrl).then((resp) => {
    ticket.value = resp.data
    ticketDescription.value = resp.data.body
  })
})
</script>

<template>
  <div class="view-ticket" v-if="ticket">
    <div class="ticket-info">
      <div class="ticket-header">
        <div class="summary">#{{ ticket.ticket_id }} - {{ ticket.summary }}</div>
        <div class="actions">
          <div class="btn-group">
            <button class="btn-info btn-sm" title="Subscribe to updates">
              <fa-icon :icon="faEnvelopeCircleCheck" />
              <span class="visually-hidden">Subscribe</span>
            </button>
            <button class="btn-warning btn-sm" title="Move ticket">
              <fa-icon :icon="faAnglesRight" />
              <span class="visually-hidden">Move</span>
            </button>
            <ConfirmDialog
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
          <button class="btn-success" @click="() => (editing = false)">Save</button>
        </template>
        <template v-else>
          <button class="btn-warning btn-sm edit-btn" title="Edit description" v-if="auth.can('edit_ticket_description')" @click="() => (editing = true)">
            <fa-icon :icon="faPencil" />
          </button>
          <div v-html="formattedDescription"></div>
        </template>
      </div>
    </div>
  </div>
  <div v-else>
    <SkeletonLoader height="250px" class="my-4" />
    <SkeletonLoader height="200px" class="mb-4" />
    <SkeletonLoader height="200px" class="mb-4" />
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

  & > .summary {
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

    & > strong {
      @apply mr-2;
      @apply font-semibold;
    }
  }
}

.ticket-description {
  @apply py-2;

  & > .edit-btn {
    float: right;
  }
}
</style>
