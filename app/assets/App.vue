<template>
  <div class="container">
    <input
        type="text"
        name="url"
    >
  </div>
</template>

<script>
  export default {
    name: 'app',
    data() {
      return {
        url: '',
        method: '',
      }
    }
  }
</script>