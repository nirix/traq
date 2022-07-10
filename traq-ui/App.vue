<script setup lang="ts">
import { watch } from "vue"
import { useRoute } from "vue-router"
import { useAuthStore } from "./stores/auth"

const route = useRoute()
const auth = useAuthStore()

watch(
  () => route.params.project,
  async (project) => {
    await auth.getUser(project)
  }
)
</script>

<template>
  <router-view></router-view>
</template>
