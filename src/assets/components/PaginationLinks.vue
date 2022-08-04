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
