<script lang="ts">
interface FilterOption {
  field: string
  label: string
  type: "is" | "isOr" | "contains"
}

export default {
  data() {
    return {
      isExpanded: false,
      filters: [],
    }
  },

  computed: {
    filterOptions(): FilterOption[] {
      const options: FilterOption[] = [
        {
          field: "summary",
          label: "Summary",
          type: "contains",
        },
        {
          field: "description",
          label: "Description",
          type: "contains",
        },
        {
          field: "owner",
          label: "Owner",
          type: "isOr",
        },
        {
          field: "assigned_to",
          label: "Assigned to",
          type: "isOr",
        },
        {
          field: "component",
          label: "Component",
          type: "is",
        },
        {
          field: "milestone",
          label: "Milestone",
          type: "is",
        },
        {
          field: "version",
          label: "Version",
          type: "is",
        },
        {
          field: "status",
          label: "Status",
          type: "is",
        },
      ]

      const filters = options.filter((filter: FilterOption) => {
        const added = this.filters.find((option: FilterOption) => option.field === filter.field)

        return added === undefined ? true : false
      })

      return filters
    },
  },

  methods: {
    toggleExpand() {
      this.isExpanded = !this.isExpanded
    },
    addFilter(event) {
      const field: string = event.target.value
      const filter: FilterOption = this.filterOptions.find((option: FilterOption) => option.field === field)

      console.log(field, filter)

      if (filter) {
        this.filters.push(filter)
      }

      event.target.value = ""
    },
  },
}
</script>

<template>
  <fieldset class="ticket-filters-container">
    <legend @click="toggleExpand">Filters</legend>
    <div class="active-filters" v-if="isExpanded">
      <div class="no-filters" v-if="filters.length === 0">No filters set.</div>
      <div v-for="filter in filters" :key="filter.field" class="filter">
        <div class="label">{{ filter.label }}</div>
        <div class="condition">
          <select :name="`filters[${filter.field}][prefix]`" v-if="['is', 'isOr'].includes(filter.type)">
            <option value="">is</option>
            <option value="!">is not</option>
          </select>
          <select :name="`filters[${filter.field}][prefix]`" v-if="['contains'].includes(filter.type)">
            <option value="">contains</option>
            <option value="!">does not contain</option>
          </select>
        </div>
        <div class="value"></div>
      </div>
    </div>
    <div class="actions" v-if="isExpanded">
      <div class="apply">
        <button>Apply</button>
      </div>
      <div class="add-filter">
        <select name="new_filter" @change="addFilter">
          <option value="" disabled selected>Add filter</option>
          <option v-for="option in filterOptions" :value="option.field" :key="option.value">
            {{ option.label }}
          </option>
        </select>
      </div>
    </div>
  </fieldset>
</template>

<style scoped lang="postcss">
.ticket-filters-container {
  padding: 0;
  @apply border-gray-600;
  @apply rounded;

  & legend {
    cursor: pointer;
    @apply px-2 py-0;
    @apply ml-3;
    @apply underline;
  }
}

.no-filters {
  @apply p-2;
  @apply text-gray-700;
}

.filter {
  display: flex;
  align-items: center;
  @apply p-2;

  &:nth-child(even) {
    @apply bg-gray-50;
  }

  & .label {
    text-align: right;
    font-weight: bold;
    @apply w-28;
    @apply mr-3;
  }
}

.actions {
  display: flex;
  @apply p-2;
  @apply bg-gray-100;
  @apply rounded-b;

  & .apply {
    flex-grow: 1;
  }

  & .add-filter {
    justify-items: end;
  }
}
</style>
