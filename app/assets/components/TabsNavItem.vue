<template>
  <li
      class="nav-item"
      role="presentation"
  >
    <button
        class="nav-link"
        style=".active{ border-color: #0dcaf0; }"
        :class="{ active: active }"
        :id="`${id}-tab`" data-bs-toggle="tab"
        :data-bs-target="`#${id}`"
        type="button"
        role="tab"
        :aria-controls="`${id}`"
        aria-selected="true"
    >
      {{ displayValue }}
    </button>
  </li>
</template>

<script>
export default {
  name: "TabsNavItem",
  props: {
    id: {
      type: String,
      required: true
    },
    displayValue: {
      type: String,
      required: true
    },
    active: {
      type: Boolean,
      required: false,
      default: () => false
    }
  }
}
</script>

<style scoped>

</style>