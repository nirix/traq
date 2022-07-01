<script lang="ts">
import { faTrashCan } from "@fortawesome/free-regular-svg-icons"
import { faPlus } from "@fortawesome/free-solid-svg-icons"
import axios from "axios"

interface FilterOption {
  field: string
  label: string
  type: "is" | "isOr" | "contains"
  condition?: boolean
  dataSet?: string
  value?: Array<string | number>
  count?: number
}

export default {
  data() {
    return {
      isExpanded: true,
      filters: [],
      filterData: {
        milestones: [],
        components: [],
        statuses: [],
        priorities: [],
      },
    }
  },

  mounted() {
    this.convertQueryString()

    const roadmapUrl = window.traq.base + window.traq.project_slug + "/roadmap.json"
    const componentsUrl = window.traq.base + "api/" + window.traq.project_slug + "/components"
    const statusesUrl = window.traq.base + "api/statuses"
    const prioritiesUrl = window.traq.base + "api/priorities"

    axios.get(roadmapUrl).then((resp) => {
      this.filterData.milestones =
        resp.data.map((data) => ({
          label: data.name,
          value: data.slug,
        })) ?? []
    })

    axios.get(statusesUrl).then((resp) => {
      const open =
        resp.data
          .filter((status) => status.status === 1)
          .map((data) => ({
            label: data.name,
            value: data.name,
          })) ?? []

      const closed =
        resp.data
          .filter((status) => status.status === 0)
          .map((data) => ({
            label: data.name,
            value: data.name,
          })) ?? []

      this.filterData.statuses = {
        Open: open,
        Closed: closed,
      }
    })

    axios.get(prioritiesUrl).then((resp) => {
      this.filterData.priorities =
        resp.data.map((data) => ({
          label: data.name,
          value: data.name,
        })) ?? []
    })

    axios.get(componentsUrl).then((resp) => {
      this.filterData.components =
        resp.data.map((data) => ({
          label: data.name,
          value: data.name,
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
          dataSet: "components",
        },
        {
          field: "milestone",
          label: "Milestone",
          type: "is",
          dataSet: "milestones",
        },
        {
          field: "version",
          label: "Version",
          type: "is",
          dataSet: "milestones",
        },
        {
          field: "status",
          label: "Status",
          type: "is",
          dataSet: "statuses",
        },
        {
          field: "priority",
          label: "Priorities",
          type: "is",
          dataSet: "priorities",
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
    applyFilters(filters = null) {
      if (Array.isArray(filters)) {
        this.filters = filters
      }

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
    convertQueryString() {
      const filters = []
      const params = new URLSearchParams(window.location.search.substring(1))

      // Loop over filter options and check params for a value
      this.filterOptions.map((filter: FilterOption) => {
        const paramValue = params.get(filter.field)

        if (paramValue) {
          const condition = !paramValue.startsWith("!")
          const values = !condition ? paramValue.substring(1)?.split(",") : paramValue.split(",")

          filters.push({
            ...filter,
            condition,
            values: values ?? [],
          })
        }
      })

      this.applyFilters(filters)
    },
  },
}
</script>

<template>
  <fieldset :class="['ticket-filters-container', isExpanded ? 'open' : '']">
    <legend @click="toggleExpand">Filters</legend>
    <div class="active-filters">
      <div class="no-filters" v-if="filters.length === 0">No filters set.</div>
      <div v-for="filter in filters" :key="filter.field" class="filter">
        <div class="label">{{ filter.label }}</div>
        <div class="conditions-and-values" v-if="['is'].includes(filter.type)">
          <div>
            <div class="condition">
              <select :name="`filters[${filter.field}][prefix]`" v-model="filter.condition">
                <option :value="true">is</option>
                <option :value="false">is not</option>
              </select>
            </div>
            <div class="value">
              <select multiple :name="`filters[${filter.field}][values][]`" v-model="filter.values">
                <template v-if="Array.isArray(filterData[filter.dataSet])">
                  <option v-for="option in filterData[filter.dataSet]" :key="option.value" :value="option.value">{{ option.label }}</option>
                </template>
                <template v-else>
                  <optgroup v-for="key in Object.keys(filterData[filter.dataSet])" :label="key" :key="key">
                    <option v-for="option in filterData[filter.dataSet][key]" :key="option.value" :value="option.value">{{ option.label }}</option>
                  </optgroup>
                </template>
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
              <select :name="`filters[${filter.field}][prefix]`" v-if="['is', 'isOr'].includes(filter.type)" v-model="filter.condition">
                <option :value="true">is</option>
                <option :value="false">is not</option>
              </select>
              <select :name="`filters[${filter.field}][prefix]`" v-if="['contains'].includes(filter.type)" v-model="filter.condition">
                <option :value="true">contains</option>
                <option :value="false">does not contain</option>
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
    <div class="actions">
      <div class="apply">
        <div class="btn-group">
          <button class="btn-danger" @click="applyFilters([])" :disabled="filters.length === 0">Clear</button>
        </div>
      </div>
      <div class="add-filter">
        <div class="input-group">
          <select name="new_filter" @change="addFilter">
            <option value="" disabled selected>Add filter</option>
            <option v-for="option in filterOptions" :value="option.field" :key="option.value">
              {{ option.label }}
            </option>
          </select>
          <button class="btn-primary" @click="applyFilters">Apply</button>
        </div>
      </div>
    </div>
  </fieldset>
</template>

<style scoped lang="postcss">
@import "../css/forms.css";

.ticket-filters-container {
  padding: 0;
  overflow: scroll;
  transition: max-height ease-in-out 0.4s;
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

    & select {
      margin: 0;
    }
  }
}
</style>
