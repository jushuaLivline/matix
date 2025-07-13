export default (id, description, key) => {
  if (!id.val().trim()) localStorage.removeItem(key)
  const stored = localStorage.getItem(key)
  id.on('input', () => {
    description.val('')
    localStorage.removeItem(key)
  })
  if (stored) {
    const parsed = JSON.parse(stored)
    const value = parsed[id.val()]
    if (!value) localStorage.removeItem(key)
    description.val(value)
  }
}
