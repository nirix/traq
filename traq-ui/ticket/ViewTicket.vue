<script setup lang="ts">
import { onMounted, ref, computed } from "vue"
import { useRoute } from "vue-router"
import axios from "axios"
import DOMPurify from "dompurify"
import { marked } from "marked"
import { faPencil } from "@fortawesome/free-solid-svg-icons"
import { useAuthStore } from "../stores/auth"
import EasyMDE from "../components/EasyMDE.vue"

const auth = useAuthStore()
const route = useRoute()

const editing = ref<boolean>(false)
const ticket = ref()
const ticketDescription = ref<string>()
const formattedDescription = computed(() => DOMPurify.sanitize(marked.parse(ticket.value.body)))

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
        <div class="actions"></div>
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
          <span></span>
        </div>

        <div class="ticket-property">
          <strong>Updated</strong>
          <span></span>
        </div>
      </div>
      <div class="ticket-description">
        <template v-if="editing">
          <EasyMDE v-model="ticketDescription" />
          <button class="btn-success" @click="() => (editing = false)">Save</button>
        </template>
        <template v-else>
          <button class="btn-warning edit-btn" v-if="auth.can('edit_ticket_description')" @click="() => (editing = true)">
            <fa-icon :icon="faPencil" />
          </button>
          <div v-html="formattedDescription"></div>
        </template>
      </div>
    </div>
  </div>
  <div v-else>Loading...</div>
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
