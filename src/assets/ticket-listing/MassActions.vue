<script lang="ts">
import axios from "axios"
import { useAuthStore } from "../stores/auth"
import EasyMDE from "../components/EasyMDE.vue"

export default {
  components: { EasyMDE },

  props: ["ticketIds"],

  setup() {
    const auth = useAuthStore()
    return {
      auth,
    }
  },

  data() {
    return {
      types: [],
      milestones: [],
      components: [],
      priorities: [],
      assignees: [],
      statuses: [],
      comment: "",
      status: -1,
      type: -1,
      priority: -1,
      milestone: -1,
      component: -1,
      assignee: -1,
    }
  },

  mounted() {
    const roadmapUrl = window.traq.base + window.traq.project_slug + "/roadmap.json"
    const componentsUrl = window.traq.base + "api/" + window.traq.project_slug + "/components"
    const membersUrl = window.traq.base + "api/" + window.traq.project_slug + "/members"
    const statusesUrl = window.traq.base + "api/statuses"
    const prioritiesUrl = window.traq.base + "api/priorities"
    const typesUrl = window.traq.base + "api/types"

    // Fetch required data
    Promise.all([
      axios.get(roadmapUrl),
      axios.get(statusesUrl),
      axios.get(prioritiesUrl),
      axios.get(componentsUrl),
      axios.get(typesUrl),
      axios.get(membersUrl),
    ]).then(([roadmap, statuses, priorities, components, ticketTypes, members]) => {
      this.milestones =
        roadmap.data.map((data) => ({
          label: data.name,
          value: data.id,
        })) ?? []

      const open =
        statuses.data
          .filter((status) => status.status === 1)
          .map((data) => ({
            label: data.name,
            value: data.id,
          })) ?? []

      const closed =
        statuses.data
          .filter((status) => status.status === 0)
          .map((data) => ({
            label: data.name,
            value: data.id,
          })) ?? []

      this.statuses = {
        Open: open,
        Closed: closed,
      }

      this.priorities =
        priorities.data.map((data) => ({
          label: data.name,
          value: data.id,
        })) ?? []

      this.components =
        components.data.map((data) => ({
          label: data.name,
          value: data.id,
        })) ?? []

      this.types =
        ticketTypes.data.map((data) => ({
          label: data.name,
          value: data.id,
        })) ?? []

      this.assignees =
        members.data.map((data) => ({
          label: data.name,
          value: data.id,
        })) ?? []
    })
  },

  computed: {
    canMassActions(): boolean {
      return this.auth.canOneOf([
        "ticket_properties_change_status",
        "ticket_properties_change_type",
        "ticket_properties_change_priority",
        "ticket_properties_change_milestone",
        "ticket_properties_change_component",
        "ticket_properties_change_assigned_to",
      ])
    },
  },

  methods: {
    updateTickets(): void {
      const massActionsUrl = `${window.traq.base}${window.traq.project_slug}/tickets/mass-actions.json`

      // Build form data
      const formData = new FormData()
      this.ticketIds.map((ticketId) => {
        formData.append("tickets[]", ticketId)
      })
      formData.append("comment", this.comment)
      formData.append("status", this.status)
      formData.append("type", this.type)
      formData.append("priority", this.priority)
      formData.append("milestone", this.milestone)
      formData.append("component", this.component)
      formData.append("assigned_to", this.assignee)

      // Update tickets and emit event on completion
      axios
        .post(massActionsUrl, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((resp) => {
          this.$emit("on-update", resp.data)
        })
    },
  },
}
</script>

<template>
  <div class="mass-actions-container" v-if="canMassActions && ticketIds.length">
    <h2>
      Mass Actions <small>({{ ticketIds.length }} tickets)</small>
    </h2>

    <div class="mass-actions-panel">
      <div class="mass-actions-comment">
        <EasyMDE v-model="comment" min-height="100px" />
      </div>
      <div class="mass-actions-fields">
        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_status')">
          <label for="">Status</label>
          <select name="status" v-model="status">
            <option value="-1">No change</option>
            <optgroup v-for="statusGroup in Object.keys(statuses)" :key="statusGroup" :label="statusGroup">
              <option v-for="status in statuses[statusGroup]" :value="status.value" :key="status.value">
                {{ status.label }}
              </option>
            </optgroup>
          </select>
        </div>

        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_type')">
          <label for="">Type</label>
          <select name="type" v-model="type">
            <option value="-1">No change</option>
            <option v-for="ticketType in types" :value="ticketType.value" :key="ticketType.value">
              {{ ticketType.label }}
            </option>
          </select>
        </div>

        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_priority')">
          <label for="">Priority</label>
          <select name="priority" v-model="priority">
            <option value="-1">No change</option>
            <option v-for="priority in priorities" :value="priority.value" :key="priority.value">
              {{ priority.label }}
            </option>
          </select>
        </div>

        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_milestone')">
          <label for="">Milestone</label>
          <select name="milestone" v-model="milestone">
            <option value="-1">No change</option>
            <option v-for="milestone in milestones" :value="milestone.value" :key="milestone.value">
              {{ milestone.label }}
            </option>
          </select>
        </div>

        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_component')">
          <label for="">Component</label>
          <select name="component" v-model="component">
            <option value="-1">No change</option>
            <option v-for="component in components" :value="component.value" :key="component.value">
              {{ component.label }}
            </option>
          </select>
        </div>

        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_assigned_to')">
          <label for="">Assignee</label>
          <select name="assigned_to" v-model="assignee">
            <option value="-1">No change</option>
            <option value="0">None</option>
            <option v-for="assignee in assignees" :value="assignee.value" :key="assignee.value">
              {{ assignee.label }}
            </option>
          </select>
        </div>
      </div>
      <div class="mass-actions-footer">
        <button class="btn-primary" :onClick="updateTickets">Update</button>
      </div>
    </div>
  </div>
</template>

<style scoped lang="postcss">
.mass-actions-container {
  @apply mt-5;

  & > h2 {
    @apply text-lg;

    & > small {
      @apply text-sm;
    }
  }

  & .mass-actions-panel {
    @apply rounded;
    @apply bg-gray-50;

    & .mass-actions-fields {
      display: flex;
      flex-wrap: wrap;
      flex-direction: row;

      @apply p-4;
      @apply bg-gray-100;
      @apply rounded;
    }

    & .mass-actions-field {
      --mass-action-field-width: 305px;

      display: flex;
      align-items: center;
      min-width: var(--mass-action-field-width);
      max-width: var(--mass-action-field-width);

      & > label {
        min-width: 60px;
        text-align: right;
        min-width: calc(var(--mass-action-field-width) / 3);
        max-width: calc(var(--mass-action-field-width) / 3);
        box-sizing: border-box;

        @apply pr-2;
      }

      & > select {
        min-width: calc(var(--mass-action-field-width) / 1.5);
        max-width: calc(var(--mass-action-field-width) / 1.5);
      }
    }

    & .mass-actions-footer {
      @apply p-2;
      @apply rounded-b;

      display: flex;
      justify-content: center;
    }
  }
}
</style>
