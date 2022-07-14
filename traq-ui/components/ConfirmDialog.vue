<script setup lang="ts">
import { ref } from "vue"
import { Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild } from "@headlessui/vue"

const props = defineProps(["btnLabel", "btnIcon", "btnClass", "btnTitle", "btnLabelClass", "message"])

const isOpen = ref<boolean>(false)

const setIsOpen = (value) => {
  isOpen.value = value
}
</script>

<template>
  <button :class="props.btnClass" :title="props.btnTitle" @click="setIsOpen(true)">
    <fa-icon v-if="props.btnIcon" :icon="props.btnIcon" />
    <span :class="props.btnLabelClass">{{ props.btnLabel }}</span>
  </button>

  <TransitionRoot :show="isOpen" as="template">
    <Dialog @close="setIsOpen" class="confirm-dialog">
      <Transition
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/30" />
      </Transition>

      <div class="confirm-dialog-wrapper">
        <TransitionChild
          enter="duration-300 ease-out"
          enter-from="opacity-0 scale-95"
          enter-to="opacity-100 scale-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100 scale-100"
          leave-to="opacity-0 scale-95"
        >
          <DialogPanel class="confirm-dialog-panel">
            <DialogTitle class="confirm-dialog-title">
              <template v-if="!props.message">Are you sure?</template>
              <template v-if="props.message">{{ props.message }}</template>
            </DialogTitle>
            <div class="confirm-dialog-actions">
              <button @click="isOpen = false">Cancel</button>
              <button class="btn-danger">Confirm</button>
            </div>
          </DialogPanel>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<style scoped lang="postcss">
.confirm-dialog {
  @apply relative z-50;
}

.confirm-dialog-wrapper {
  @apply fixed inset-0 flex items-center justify-center p-4;
}

.confirm-dialog-panel {
  @apply w-full max-w-sm rounded bg-white shadow-md;
}

.confirm-dialog-title {
  text-align: center;
  @apply p-4;
  @apply text-lg;
}

.confirm-dialog-actions {
  display: flex;

  & > button {
    flex-grow: 1;

    @apply rounded-none;

    &:first-child {
      @apply rounded-bl;
    }

    &:last-child {
      @apply rounded-br;
    }
  }
}
</style>
