<script lang="ts">
import EasyMDE from "easymde"

export default {
  props: {
    modelValue: String,
    minHeight: String,
  },

  emits: ["update:modelValue"],

  mounted() {
    this.easyMDE = new EasyMDE({
      element: this.$refs["textarea"],
      status: false,
      uploadImage: false,
      minHeight: this.minHeight ?? "150px",
    })

    this.easyMDE.codemirror.on("change", (instance, changeObj) => {
      if (changeObj.origin === "setValue") {
        return
      }

      this.$emit("update:modelValue", this.easyMDE.value())
    })

    this.easyMDE.codemirror.on("blur", () => this.$emit("update:modelValue", this.easyMDE.value()))
  },

  beforeUnmount() {
    if (!this.easyMDE) {
      return
    }

    const isFullScreen = this.easyMDE.codemirror.getOption("fullScreen")

    if (isFullScreen) {
      this.easyMDE.toggleFullScreen()
    }
  },
}
</script>

<template>
  <textarea :name="name" ref="textarea" :value="modelValue"></textarea>
</template>
