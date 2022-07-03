<script lang="ts">
import axios from "axios"
import { useAuthStore } from "../stores/auth"

export default {
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
    }
  },

  mounted() {
    const roadmapUrl = window.traq.base + window.traq.project_slug + "/roadmap.json"
    const componentsUrl = window.traq.base + "api/" + window.traq.project_slug + "/components"
    const statusesUrl = window.traq.base + "api/statuses"
    const prioritiesUrl = window.traq.base + "api/priorities"
    const typesUrl = window.traq.base + "api/types"

    Promise.all([axios.get(roadmapUrl), axios.get(statusesUrl), axios.get(prioritiesUrl), axios.get(componentsUrl), axios.get(typesUrl)]).then(
      ([roadmap, statuses, priorities, components, ticketTypes]) => {
        this.milestones =
          roadmap.data.map((data) => ({
            label: data.name,
            value: data.slug,
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
      }
    )
  },

  computed: {
    canMassActions(): boolean {
      return this.auth.canOneOf([
        "ticket_properties_change_status",
        "ticket_properties_change_type",
        "ticket_properties_change_priority",
        "ticket_properties_change_milestone",
        "ticket_properties_change_assigned_to",
      ])
    },
  },
}
</script>

<template>
  <div class="mass-actions-container" v-if="canMassActions">
    <h2>
      Mass Actions <small>({{ ticketIds.length }} tickets)</small>
    </h2>

    <div class="mass-actions-panel">
      <div class="mass-actions-fields">
        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_status')">
          <label for="">Status</label>
          <select name="status">
            <option value="-1">No change</option>
            <optgroup v-for="statusGroup in Object.keys(statuses)" :key="statusGroup" :label="statusGroup">
              <option v-for="status in statuses[statusGroup]" :value="status.id" :key="status.id">
                {{ status.label }}
              </option>
            </optgroup>
          </select>
        </div>

        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_type')">
          <label for="">Type</label>
          <select name="type">
            <option value="-1">No change</option>
            <option v-for="ticketType in types" :value="ticketType.id" :key="ticketType.id">
              {{ ticketType.label }}
            </option>
          </select>
        </div>

        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_priority')">
          <label for="">Priority</label>
          <select name="priority">
            <option value="-1">No change</option>
            <option v-for="priority in priorities" :value="priority.id" :key="priority.id">
              {{ priority.label }}
            </option>
          </select>
        </div>

        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_milestone')">
          <label for="">Milestone</label>
          <select name="milestone">
            <option value="-1">No change</option>
            <option v-for="milestone in milestones" :value="milestone.id" :key="milestone.id">
              {{ milestone.label }}
            </option>
          </select>
        </div>

        <div class="mass-actions-field" v-if="auth.can('ticket_properties_change_assigned_to')">
          <label for="">Assignee</label>
          <select name="assigned_to">
            <option value="-1">No change</option>
            <option value="0">None</option>
            <option v-for="assignee in assignees" :value="ticketType.id" :key="assignee.id">
              {{ assignee.label }}
            </option>
          </select>
        </div>
      </div>
      <div class="mass-actions-footer">
        <button class="btn-primary">Update</button>
      </div>
    </div>
  </div>
</template>

<style scoped lang="postcss">
@import "../css/forms.css";

.mass-actions-container {
  @apply my-5;

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
