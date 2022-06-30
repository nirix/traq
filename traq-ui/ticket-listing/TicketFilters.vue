<script lang="ts">
import { faTrashCan } from "@fortawesome/free-regular-svg-icons"
import { faPlus } from "@fortawesome/free-solid-svg-icons"
import axios from "axios"

interface FilterOption {
  field: string
  label: string
  type: "is" | "isOr" | "contains"
  options?: Array<{ label: string; value: string }>

  condition?: boolean
  value?: Array<string | number>
  count?: number
}

interface Filter {
  field: string
  condition: boolean
  values: Array<string | number>
}

export default {
  data() {
    return {
      isExpanded: false,
      filters: [],
      milestones: [],
    }
  },

  mounted() {
    const roadmapUrl = window.traq.base + window.traq.project_slug + "/roadmap.json"
    axios.get(roadmapUrl).then((resp) => {
      this.milestones =
        resp.data.map((data) => ({
          label: data.name,
          value: data.slug,
        })) ?? []
    })
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
          options: this.milestones,
        },
        {
          field: "version",
          label: "Version",
          type: "is",
          options: this.milestones,
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
    // Icons
    faTrashCan: () => faTrashCan,
    faPlus: () => faPlus,
  },

  methods: {
    toggleExpand() {
      this.isExpanded = !this.isExpanded
    },
    applyFilters() {
      this.$emit("applyFilters", this.filters)
    },
    addFilter(event) {
      const field: string = event.target.value
      const filter: FilterOption = this.filterOptions.find((option: FilterOption) => option.field === field)

      if (filter) {
        this.filters.push({
          ...filter,
          condition: true,
          values: ["isOr", "contains"].includes(filter.type) ? [""] : [],
        })
      }

      event.target.value = ""
    },
    addFilterValue(filter) {
      filter.values.push("")
    },
    setFilterValue(filter, index, event) {
      filter.values[index] = event.target.value
    },
    removeFilter(filter, valueIndex = null) {
      if (valueIndex === null || (valueIndex === 0 && filter.values.length === 1)) {
        this.filters = this.filters.filter((option: FilterOption) => option.field !== filter.field)
      } else {
        filter.values.splice(valueIndex, 1)
      }
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
        <div class="conditions-and-values" v-if="['is'].includes(filter.type)">
          <div>
            <div class="condition">
              <select :name="`filters[${filter.field}][prefix]`">
                <option value="">is</option>
                <option value="!">is not</option>
              </select>
            </div>
            <div class="value">
              <select multiple :name="`filters[${filter.field}][values][]`" v-model="filter.values">
                <option v-for="option in filter.options" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div class="remove">
              <button class="btn-danger" @click="removeFilter(filter)">
                <fa-icon :icon="faTrashCan" />
              </button>
            </div>
          </div>
        </div>

        <div class="conditions-and-values" v-if="['isOr', 'contains'].includes(filter.type)">
          <div v-for="(value, index) in filter.values" :key="filter.field + index">
            <div class="condition" v-if="index === 0">
              <select :name="`filters[${filter.field}][prefix]`" v-if="['is', 'isOr'].includes(filter.type)">
                <option value="">is</option>
                <option value="!">is not</option>
              </select>
              <select :name="`filters[${filter.field}][prefix]`" v-if="['contains'].includes(filter.type)">
                <option value="">contains</option>
                <option value="!">does not contain</option>
              </select>
            </div>
            <div class="condition condition-static" v-if="index >= 1">
              <span>or</span>
            </div>
            <div class="value">
              <input type="text" :name="`filters[${filter.field}][values][]`" :value="value" @change="(event) => setFilterValue(filter, index, event)" />
            </div>
            <div class="add" v-if="index === filter.values.length - 1">
              <button class="btn-success" @click="addFilterValue(filter)">
                <fa-icon :icon="faPlus" />
              </button>
            </div>
            <div class="remove">
              <button class="btn-danger" @click="removeFilter(filter, index)">
                <fa-icon :icon="faTrashCan" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="actions" v-if="isExpanded">
      <div class="apply">
        <button @click="applyFilters">Apply</button>
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
select,
input[type="text"] {
  @apply border border-gray-400 focus:border-brand-600 rounded;
  @apply focus:ring-4 focus:ring-brand-100;
  @apply text-sm text-gray-800;
  @apply px-2 py-1;
  @apply box-border min-h-[32px];
  @apply transition-all;
}

.ticket-filters-container {
  padding: 0;
  @apply border-gray-400;
  @apply rounded;

  & legend {
    cursor: pointer;
    @apply text-gray-600;
    @apply px-2 py-0;
    @apply ml-3;
    @apply underline;
  }
}

.no-filters {
  @apply px-6 py-4;
  @apply text-gray-700;
}

.filter {
  display: flex;
  align-items: top;
  @apply p-2;

  &:nth-child(even) {
    @apply bg-gray-50;
  }

  & .label {
    text-align: right;
    font-weight: bold;
    @apply w-28;
    @apply mr-3;
    @apply pt-2;
  }

  & .conditions-and-values {
    display: flex;
    flex-direction: column;
    flex-grow: 1;

    & > div {
      display: flex;
      flex-direction: row;
      @apply mb-2;

      &:last-child {
        @apply mb-0;
      }
    }

    & .value,
    & .condition {
      & select,
      & input {
        margin: 0;
      }

      &.condition-static {
        box-sizing: border-box;
        @apply pt-2 pl-3;

        & > span {
          @apply text-gray-500;
        }
      }
    }

    & .condition {
      @apply w-32;

      & select,
      & input {
        @apply w-28;
      }
    }

    & .value {
      flex-grow: 1;

      & select,
      & input {
        @apply min-w-full;
      }
    }

    & .add,
    & .remove {
      @apply ml-4;
    }
  }
}

.actions {
  display: flex;
  align-items: center;
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
