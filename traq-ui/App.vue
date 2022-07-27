<script setup lang="ts">
import { watch } from "vue"
import { useRoute } from "vue-router"
import { useAuthStore } from "./stores/auth"
import { useProjectStore } from "./stores/project"

const route = useRoute()
const auth = useAuthStore()
const currentProject = useProjectStore()

watch(
  () => route.params.project,
  (project) => {
    auth.getUser(project)
    currentProject.getProject(project)
  }
)
</script>

<template>
  <router-view></router-view>
</template>
