<script setup lang="ts">
const props = defineProps(["height", "width", "class"])

const style = {
  width: props.width ?? "auto",
  height: props.height ?? "100px",
}
</script>

<template>
  <div :class="['skeleton-loader', props.class]" :style="style"></div>
</template>

<style scoped lang="postcss">
.skeleton-loader {
  @apply animate-pulse;
  @apply bg-gray-400;
  @apply rounded;
}
</style>
