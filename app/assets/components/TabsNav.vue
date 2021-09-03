<template>
  <div>
    <ul class="nav nav-tabs border-info" id="myTab" role="tablist">
      <tabs-nav-item
          v-for="item in items"
          :display-value="item.displayValue"
          :active="item.active"
          :id="item.id"
          :key="item.id"
      />
    </ul>
  </div>
</template>

<script>

import TabsNavItem from "./TabsNavItem";
export default {
  name: "Tabs",
  components: {
    TabsNavItem
  },

  props: {
    id: {
      type: String,
      required: true
    },
    items: {
      type: Array,
      required: true
    }
  }
}
</script>

<style scoped>

</style>