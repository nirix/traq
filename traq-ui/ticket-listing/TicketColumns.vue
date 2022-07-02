<script lang="ts">
import axios from "axios"

export interface CustomField {
  name: string
  field: string
  custom?: boolean
}

export default {
  props: ["customFields"],

  data() {
    return {
      isExpanded: true,
      columns: [
        {
          name: "ID",
          field: "ticket_id",
        },
        {
          name: "Summary",
          field: "summary",
        },
        {
          name: "Status",
          field: "status",
        },
        {
          name: "Owner",
          field: "owner",
        },
        {
          name: "Type",
          field: "type",
        },
        {
          name: "Component",
          field: "component",
        },
        {
          name: "Milestone",
          field: "milestone",
        },
        {
          name: "Assignee",
          field: "assigned_to",
        },
        {
          name: "Priority",
          field: "priority",
        },
        {
          name: "Severity",
          field: "severity",
        },
        {
          name: "Reported",
          field: "created_at",
        },
        {
          name: "Updated",
          field: "updated_at",
        },
        {
          name: "Votes",
          field: "votes",
        },
      ],
      active: ["ticket_id", "summary", "status", "owner", "type", "component", "milestone"],
    }
  },

  mounted() {
    this.$emit("apply-columns", this.active)
  },

  watch: {
    customFields() {
      // Map custom fields to available columns.
      this.customFields.map((field) => {
        this.columns.push({
          name: field.name,
          field: field.slug,
          custom: true,
        })
      })
    },
  },

  methods: {
    toggleExpand(): void {
      this.isExpanded = !this.isExpanded
    },
    toggleField(field): void {
      if (this.active.includes(field)) {
        this.active = this.active.filter((activeField) => activeField !== field)
      } else {
        this.active.push(field)
      }

      this.$emit("apply-columns", this.active)
    },
  },
}
</script>

<template>
  <fieldset :class="['ticket-columns-container', isExpanded ? 'open' : '']">
    <legend @click="toggleExpand">Columns</legend>
    <div class="ticket-columns">
      <div
        v-for="column in columns"
        :class="['ticket-column', active.includes(column.field) ? 'column-active' : '']"
        :key="column.field"
        @click="toggleField(column.field)"
      >
        {{ column.name }}
      </div>
    </div>
  </fieldset>
</template>

<style scoped lang="postcss">
.ticket-columns-container {
  padding: 0;
  overflow: scroll;
  transition: max-height ease-out 0.4s;
  max-height: 0px;
  background-color: #fff;
  @apply border-gray-400;
  @apply rounded;

  &.open {
    max-height: 600px;
  }

  & legend {
    cursor: pointer;
    @apply text-gray-600;
    @apply px-2 py-0;
    @apply ml-3;
    @apply underline;
  }
}

.ticket-columns {
  @apply p-2;
}

.ticket-column {
  display: inline-block;
  cursor: pointer;
  transition: all ease-in-out 0.1s;

  @apply mx-1 my-1;
  @apply text-gray-500;
  @apply bg-gray-100;
  @apply border border-solid border-gray-400 rounded;
  @apply py-2 px-3;

  &.column-active {
    @apply border-emerald-300 bg-emerald-50 text-emerald-800;
  }
}
</style>
