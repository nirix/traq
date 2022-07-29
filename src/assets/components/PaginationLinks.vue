<script lang="ts">
import { faChevronLeft, faChevronRight } from "@fortawesome/free-solid-svg-icons"

export default {
  props: {
    currentPage: Number,
    totalPages: Number,
  },

  setup() {
    return {
      backIcon: faChevronLeft,
      nextIcon: faChevronRight,
    }
  },

  computed: {
    nextUrl() {
      const pageNum = parseInt(this.$route.query.page ?? 1)
      const path = window.location.pathname
      const search = window.location.search.replace(`page=${pageNum}`, "")

      return search.includes("?") ? `${path}${search}&page=${pageNum + 1}` : `${path}${search}?page=${pageNum + 1}`
    },
    prevUrl() {
      const pageNum = parseInt(this.$route.query.page ?? 1)
      const path = window.location.pathname
      const search = window.location.search.replace(`page=${pageNum}`, "")

      return search.includes("?") ? `${path}${search}&page=${pageNum - 1}` : `${path}${search}?page=${pageNum - 1}`
    },
  },

  methods: {
    navigate(page, event) {
      event.preventDefault()

      this.currentPage
      this.$emit("navigate", page)
    },
  },
}
</script>

<template>
  <div class="pagination">
    <div class="previous" v-if="currentPage === 1 || totalPages === 0">
      <fa-icon :icon="backIcon" />
      <span>Previous</span>
    </div>
    <a :href="prevUrl" class="previous" v-if="currentPage > 1" @click="(e) => navigate(currentPage - 1, e)">
      <fa-icon :icon="backIcon" />
      <span>Previous</span>
    </a>

    <div class="pages">{{ totalPages > 0 ? currentPage : 0 }} of {{ totalPages }}</div>

    <div class="next" v-if="currentPage === totalPages || totalPages === 0">
      <span>Next</span>
      <fa-icon :icon="nextIcon" />
    </div>
    <a :href="nextUrl" class="next" v-if="currentPage < totalPages" @click="(e) => navigate(currentPage + 1, e)">
      <span>Next</span>
      <fa-icon :icon="nextIcon" />
    </a>
  </div>
</template>

<style scoped lang="postcss">
.pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;

  @apply my-2;
}

.previous,
.next {
  display: flex;
  align-items: center;
  line-height: 1.25;

  @apply bg-gray-100 border-gray-100;
  @apply text-sm;
  @apply py-2 px-3;
  @apply border border-solid rounded;
  @apply transition-all;
}

.previous {
  & > span {
    @apply ml-1;
  }
}

.next {
  & > span {
    @apply mr-1;
  }
}

div.previous,
div.next {
  cursor: not-allowed;

  @apply text-gray-500;
}

a.previous,
a.next {
  cursor: pointer;
  text-decoration: none;

  @apply text-gray-900;
  @apply bg-gray-200 hover:bg-gray-300 active:bg-gray-400;
  @apply border-gray-200 hover:border-gray-300 active:border-gray-400;
  @apply focus:ring-4 ring-gray-400/30;
}
</style>
