<template>
  <div>
  <div class="row my-1" v-for="(item, index) in items">
    <div class="col-5">
      <input
          v-model="item.key"
          type="text"
          class="form-control"
          name="key"
      >
    </div>
    <div class="col-5">
      <input
          v-model="item.value"
          type="text"
          class="form-control"
          name="value"
      >
    </div>
    <div class="col-2">
      <button
          class="btn btn-danger"
          @click="$emit('remove-item', index)"
      >
        Remove
      </button>
    </div>
  </div>
  </div>
</template>

<script>
export default {
  name: "KeyValueInputGroup",
  props: {
    items: {
      type: Array,
      required: true
    }
  }
}
</script>

<style scoped>

</style>