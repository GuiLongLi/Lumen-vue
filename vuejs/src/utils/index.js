export default {
  str_substr (str, len) {
    if (str === null || str === '') {
      return ''
    }
    str = str.toString()
    if (str.length > len) {
      return str.substr(0, len) + '...'
    } else {
      return str
    }
  }
}
