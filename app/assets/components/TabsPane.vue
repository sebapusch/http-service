<template>
<div
    class="tab-pane fade border border-top-0 border-info p-2"
    :class="{ show: active, active: active }"
    :id="id"
    role="tabpanel"
    :aria-labelledby="`${id}-pane`"
>
  <slot></slot>
</div>
</template>

<script>
export default {
  name: "TabsPane",
  props: {
    id: {
      required: true,
      type: String
    },
    active: {
      required: false,
      type: Boolean,
      default: () => false
    }
  }
}
</script>

<style scoped>

</style>