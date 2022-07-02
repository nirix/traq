<script lang="ts">
export default {
  props: ["ticketIds"],
}
</script>

<template>
  <div v-if="ticketIds.length">
    <h2>Mass Actions</h2>
  </div>
</template>

<style lang="postcss"></style>
