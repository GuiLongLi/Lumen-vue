<template>
  <div>
    {{ json }}
  </div>
</template>
<script>
export default {
  data () {
    return {
      json: ''
    }
  },
  mounted () {
    // Code that will run only after the
    // entire view has been rendered
    this.$nextTick(function () {
      this.json = this.$route.query.ueResult
    })
  }
}
</script>
